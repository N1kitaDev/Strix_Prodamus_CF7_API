<?php
/**
 * Admin class for CF7 Prodamus XL Integration
 */

class CF7_Prodamus_XL_Admin {

    public static function admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Save settings if form is submitted
        if (isset($_POST['submit']) && check_admin_referer('cf7_prodamus_xl_settings')) {
            self::save_settings();
            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'cf7-prodamus-xl') . '</p></div>';
        }

        // Get current settings
        $api_token = get_option('cf7_prodamus_xl_api_token', '');
        $stage_id = get_option('cf7_prodamus_xl_stage_id', '1gKg6-JsY0aftO_lcO6vwQ');
        $responsible_id = get_option('cf7_prodamus_xl_responsible_id', 'GEqhVZNU5kG2dgZf48RkEQ');
        $form_ids = get_option('cf7_prodamus_xl_form_ids', '');

        ?>
        <div class="wrap">
            <h1><?php _e('CF7 Prodamus XL Integration Settings', 'cf7-prodamus-xl'); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field('cf7_prodamus_xl_settings'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="api_token"><?php _e('API Token', 'cf7-prodamus-xl'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="api_token" name="api_token" value="<?php echo esc_attr($api_token); ?>" class="regular-text" />
                            <p class="description">
                                <?php _e('Enter your Prodamus XL API token. You can find it in your Prodamus XL account settings.', 'cf7-prodamus-xl'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="stage_id"><?php _e('Stage ID', 'cf7-prodamus-xl'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="stage_id" name="stage_id" value="<?php echo esc_attr($stage_id); ?>" class="regular-text" />
                            <p class="description">
                                <?php _e('ID of the pipeline stage where deals will be created (default: "Новая регистрация").', 'cf7-prodamus-xl'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="responsible_id"><?php _e('Responsible User ID', 'cf7-prodamus-xl'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="responsible_id" name="responsible_id" value="<?php echo esc_attr($responsible_id); ?>" class="regular-text" />
                            <p class="description">
                                <?php _e('ID of the user responsible for the deals.', 'cf7-prodamus-xl'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="form_ids"><?php _e('Form IDs', 'cf7-prodamus-xl'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="form_ids" name="form_ids" value="<?php echo esc_attr($form_ids); ?>" class="regular-text" />
                            <p class="description">
                                <?php _e('Comma-separated list of Contact Form 7 IDs to integrate with Prodamus XL (leave empty for all forms).', 'cf7-prodamus-xl'); ?>
                                <br><?php _e('Example: 10619, 10620, 10621', 'cf7-prodamus-xl'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <div class="card">
                <h2><?php _e('Form Field Mapping', 'cf7-prodamus-xl'); ?></h2>
                <p><?php _e('The plugin automatically maps the following Contact Form 7 fields:', 'cf7-prodamus-xl'); ?></p>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><code>your-name</code> - <?php _e('Contact name (first name and last name)', 'cf7-prodamus-xl'); ?></li>
                    <li><code>your-email</code> - <?php _e('Contact email (required)', 'cf7-prodamus-xl'); ?></li>
                    <li><code>your-phone</code> - <?php _e('Contact phone', 'cf7-prodamus-xl'); ?></li>
                    <li><code>your-contactway</code> - <?php _e('Contact method preference', 'cf7-prodamus-xl'); ?></li>
                </ul>
                <p><?php _e('Make sure your Contact Form 7 forms have these field names for proper integration.', 'cf7-prodamus-xl'); ?></p>
            </div>

            <div class="card">
                <h2><?php _e('Testing', 'cf7-prodamus-xl'); ?></h2>
                <p><?php _e('To test the integration:', 'cf7-prodamus-xl'); ?></p>
                <ol>
                    <li><?php _e('Fill out and submit a Contact Form 7 form with the specified field names', 'cf7-prodamus-xl'); ?></li>
                    <li><?php _e('Check your Prodamus XL account for new leads and deals', 'cf7-prodamus-xl'); ?></li>
                    <li><?php _e('Check WordPress debug logs if something goes wrong', 'cf7-prodamus-xl'); ?></li>
                </ol>
            </div>
        </div>
        <?php
    }

    private static function save_settings() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $api_token = isset($_POST['api_token']) ? sanitize_text_field($_POST['api_token']) : '';
        $stage_id = isset($_POST['stage_id']) ? sanitize_text_field($_POST['stage_id']) : '';
        $responsible_id = isset($_POST['responsible_id']) ? sanitize_text_field($_POST['responsible_id']) : '';
        $form_ids = isset($_POST['form_ids']) ? sanitize_text_field($_POST['form_ids']) : '';

        update_option('cf7_prodamus_xl_api_token', $api_token);
        update_option('cf7_prodamus_xl_stage_id', $stage_id);
        update_option('cf7_prodamus_xl_responsible_id', $responsible_id);
        update_option('cf7_prodamus_xl_form_ids', $form_ids);
    }
}