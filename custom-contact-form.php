<?php
/**
 * Plugin Name: Custom Contact Form
 * Description: HERE_PRINT.
 * Version: 1.0
 * Author: HERE_PRINT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define plugin constants.
define('CCF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CCF_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files.
require_once CCF_PLUGIN_DIR . 'includes/ajax-handler.php';
require_once CCF_PLUGIN_DIR . 'includes/settings-page.php';
require_once CCF_PLUGIN_DIR . 'includes/logger-page.php';

// Function to register the block and styles
function ccf_register_block_and_assets()
{
    if (!WP_Block_Type_Registry::get_instance()->is_registered('ccf/contact-form')) {
        wp_register_script(
            'ccf-block',
            CCF_PLUGIN_URL . 'build/index.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-api'],
            filemtime(CCF_PLUGIN_DIR . 'build/index.js')
        );

        wp_register_style(
            'ccf-block-style',
            CCF_PLUGIN_URL . 'src/style.css',
            [],
            filemtime(CCF_PLUGIN_DIR . 'src/style.css')
        );

        register_block_type('ccf/contact-form', [
            'editor_script' => 'ccf-block',
            'style' => 'ccf-block-style',
        ]);
    }

    wp_register_style(
        'ccf-contact-form-style',
        plugin_dir_url(__FILE__) . './src/style.css',
        [],
        filemtime(plugin_dir_path(__FILE__) . './src/style.css')
    );

    if (!WP_Block_Type_Registry::get_instance()->is_registered('ccf/contact-form')) {
        register_block_type('ccf/contact-form', [
            'style' => 'ccf-contact-form-style',
        ]);
    }
}

add_action('init', 'ccf_register_block_and_assets');

// Add admin menu pages.
function ccf_add_admin_pages()
{
    add_menu_page(
        'Contact Form Settings',
        'Contact Form',
        'manage_options',
        'ccf-settings',
        'ccf_render_settings_page',
        'dashicons-email',
        20
    );

    add_submenu_page(
        'ccf-settings',
        'Email Logs',
        'Logs',
        'manage_options',
        'ccf-logs',
        'ccf_render_logs_page'
    );
}
add_action('admin_menu', 'ccf_add_admin_pages');

// Handle AJAX request for sending email.
add_action('wp_ajax_ccf_send_email', 'ccf_handle_form_submission');
add_action('wp_ajax_nopriv_ccf_send_email', 'ccf_handle_form_submission');

// Enqueue frontend script.
function ccf_enqueue_frontend_assets()
{
    wp_enqueue_script(
        'ccf-frontend',
        CCF_PLUGIN_URL . 'assets/frontend.js',
        ['jquery'],
        filemtime(CCF_PLUGIN_DIR . 'assets/frontend.js'),
        true
    );

    wp_localize_script('ccf-frontend', 'ccf_ajax', [
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ccf_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'ccf_enqueue_frontend_assets');

register_activation_hook(__FILE__, 'ccf_create_logs_table');

// Отключение проверки сертификата SSL.
add_action('phpmailer_init', function($phpmailer) {
    $phpmailer->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
});
