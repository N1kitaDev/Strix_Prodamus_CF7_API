<?php
/*
Plugin Name: CF7 Prodamus XL Integration
Plugin URI: https://yourwebsite.com
Description: Integrates Contact Form 7 with Prodamus XL API to automatically create leads and deals from form submissions.
Version: 1.0.0
Author: Your Name
License: GPL v2 or later
Text Domain: cf7-prodamus-xl
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.0
*/

defined('ABSPATH') or die('No script kiddies please!');

// Define plugin constants
define('CF7_PRODAMUS_XL_VERSION', '1.0.0');
define('CF7_PRODAMUS_XL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CF7_PRODAMUS_XL_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once CF7_PRODAMUS_XL_PLUGIN_DIR . 'includes/class-cf7-prodamus-xl-admin.php';
require_once CF7_PRODAMUS_XL_PLUGIN_DIR . 'includes/class-cf7-prodamus-xl-integration.php';

// Initialize the plugin
class CF7_Prodamus_XL_Integration {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));

        // Initialize integration if Contact Form 7 is active
        if (class_exists('WPCF7')) {
            add_action('wpcf7_before_send_mail', array('CF7_Prodamus_XL_Integration_Core', 'process_form_submission'), 10, 1);
        }
    }

    public function activate() {
        // Create database options if needed
        add_option('cf7_prodamus_xl_api_token', '');
        add_option('cf7_prodamus_xl_stage_id', '1gKg6-JsY0aftO_lcO6vwQ');
        add_option('cf7_prodamus_xl_responsible_id', 'GEqhVZNU5kG2dgZf48RkEQ');
        add_option('cf7_prodamus_xl_form_ids', '');
    }

    public function deactivate() {
        // Cleanup if needed
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'cf7-prodamus-xl',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    public function add_admin_menu() {
        add_menu_page(
            __('CF7 Prodamus XL', 'cf7-prodamus-xl'),
            __('CF7 Prodamus XL', 'cf7-prodamus-xl'),
            'manage_options',
            'cf7-prodamus-xl',
            array('CF7_Prodamus_XL_Admin', 'admin_page'),
            'dashicons-admin-plugins',
            30
        );
    }

    public function register_settings() {
        register_setting('cf7_prodamus_xl_settings', 'cf7_prodamus_xl_api_token');
        register_setting('cf7_prodamus_xl_settings', 'cf7_prodamus_xl_stage_id');
        register_setting('cf7_prodamus_xl_settings', 'cf7_prodamus_xl_responsible_id');
        register_setting('cf7_prodamus_xl_settings', 'cf7_prodamus_xl_form_ids');
    }
}

// Initialize the plugin
CF7_Prodamus_XL_Integration::get_instance();