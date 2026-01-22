<?php
/**
 * Integration class for CF7 Prodamus XL
 */

class CF7_Prodamus_XL_Integration_Core {

    public static function process_form_submission($cf) {
        // Check if Contact Form 7 is available
        if (!class_exists('WPCF7_Submission')) {
            error_log('CF7 Prodamus XL: WPCF7_Submission class not found');
            return;
        }

        $submission = WPCF7_Submission::get_instance();
        if (!$submission) {
            error_log('CF7 Prodamus XL: No submission instance');
            return;
        }

        // Get settings
        $api_token = get_option('cf7_prodamus_xl_api_token', '');
        $stage_id = get_option('cf7_prodamus_xl_stage_id', '1gKg6-JsY0aftO_lcO6vwQ');
        $responsible_id = get_option('cf7_prodamus_xl_responsible_id', 'GEqhVZNU5kG2dgZf48RkEQ');
        $form_ids = get_option('cf7_prodamus_xl_form_ids', '');

        // Check if this form should be processed
        if (!empty($form_ids)) {
            $allowed_form_ids = array_map('trim', explode(',', $form_ids));
            if (!in_array((string)$cf->id(), $allowed_form_ids)) {
                return; // Skip this form
            }
        }

        // Check API token
        if (empty($api_token)) {
            error_log('CF7 Prodamus XL: API token not configured');
            return;
        }

        // Get posted data
        $posted = (array) $submission->get_posted_data();

        // Extract form fields
        $name = sanitize_text_field($posted['your-name'] ?? '');
        $email = sanitize_email($posted['your-email'] ?? '');
        $phone = sanitize_text_field($posted['your-phone'] ?? '');
        $contactway = sanitize_text_field($posted['your-contactway'] ?? '');
        $page_url = (string) $submission->get_meta('url');

        // Validate required fields
        if (empty($email)) {
            error_log('CF7 Prodamus XL: Email is required, skipping');
            return;
        }

        // Process the data and send to Prodamus XL
        self::send_to_prodamus_xl($api_token, $stage_id, $responsible_id, $name, $email, $phone, $contactway, $page_url);
    }

    private static function send_to_prodamus_xl($api_token, $stage_id, $responsible_id, $name, $email, $phone, $contactway, $page_url) {
        // Normalize token (strip "Bearer " if user pasted it)
        $token = trim($api_token);
        if (stripos($token, 'Bearer ') === 0) {
            $token = trim(substr($token, 7));
        }

        // API configuration
        $base = 'https://app.xl.ru';
        $endpoint_lead = $base . '/api/v1/crm/lead';
        $endpoint_item = $base . '/api/v1/pipeline/stage/item';

        // Headers
        $headers = [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];

        // Create comment
        $comment_parts = [];
        if ($contactway) $comment_parts[] = 'Способ связи: ' . $contactway;
        if ($page_url)   $comment_parts[] = 'Страница: ' . $page_url;
        $comment = implode("\n", $comment_parts);

        // Split name to first/last
        $first_name = $name ?: 'Заявка с сайта';
        $last_name  = '';
        if ($name && strpos(trim($name), ' ') !== false) {
            $parts = preg_split('/\s+/', trim($name), 2);
            $first_name = $parts[0] ?? $first_name;
            $last_name  = $parts[1] ?? '';
        }

        // Step 1: Create lead/contact
        $lead_payload = [
            'email'     => $email,
            'firstName' => $first_name,
            'lastName'  => $last_name,
            'phone'     => $phone,
        ];

        error_log('CF7 Prodamus XL: Creating lead for email: ' . $email);

        $lead_response = wp_remote_post($endpoint_lead, [
            'timeout' => 20,
            'headers' => $headers,
            'body'    => wp_json_encode($lead_payload),
        ]);

        if (is_wp_error($lead_response)) {
            error_log('CF7 Prodamus XL: Lead creation HTTP error: ' . $lead_response->get_error_message());
            return;
        }

        $lead_code = wp_remote_retrieve_response_code($lead_response);
        $lead_body = wp_remote_retrieve_body($lead_response);

        error_log('CF7 Prodamus XL: Lead response code: ' . $lead_code);

        $lead_json = json_decode($lead_body, true);
        if (!is_array($lead_json) || empty($lead_json['success'])) {
            error_log('CF7 Prodamus XL: Lead creation failed. Response: ' . $lead_body);
            return;
        }

        $contact_id = $lead_json['body'] ?? '';
        if (!$contact_id) {
            error_log('CF7 Prodamus XL: Lead created but no contactId in response');
            return;
        }

        error_log('CF7 Prodamus XL: Lead created successfully, contact ID: ' . $contact_id);

        // Step 2: Create deal/item
        $deal_amount = 0; // Can be modified based on form data

        $item_payload = [
            'contactId'      => $contact_id,
            'responsibleId'  => $responsible_id,
            'dealAmount'     => $deal_amount,
            'fieldValues'    => [],
            'stageId'        => $stage_id,
            'tagIds'         => [],
        ];

        // Log comment if exists
        if ($comment) {
            error_log('CF7 Prodamus XL: Comment: ' . str_replace("\n", ' | ', $comment));
        }

        error_log('CF7 Prodamus XL: Creating deal for contact ID: ' . $contact_id);

        $item_response = wp_remote_post($endpoint_item, [
            'timeout' => 20,
            'headers' => $headers,
            'body'    => wp_json_encode($item_payload),
        ]);

        if (is_wp_error($item_response)) {
            error_log('CF7 Prodamus XL: Deal creation HTTP error: ' . $item_response->get_error_message());
            return;
        }

        $item_code = wp_remote_retrieve_response_code($item_response);
        $item_body = wp_remote_retrieve_body($item_response);

        error_log('CF7 Prodamus XL: Deal response code: ' . $item_code);

        $item_json = json_decode($item_body, true);
        if (is_array($item_json) && !empty($item_json['success'])) {
            $item_id = $item_json['body'] ?? '';
            error_log('CF7 Prodamus XL: Deal created successfully, ID: ' . ($item_id ?: '[no id]'));
        } else {
            error_log('CF7 Prodamus XL: Deal creation failed. Response: ' . $item_body);
        }
    }
}