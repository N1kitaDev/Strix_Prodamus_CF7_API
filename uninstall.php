<?php
/**
 * Uninstall CF7 Prodamus XL Integration
 *
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('cf7_prodamus_xl_api_token');
delete_option('cf7_prodamus_xl_stage_id');
delete_option('cf7_prodamus_xl_responsible_id');
delete_option('cf7_prodamus_xl_form_ids');