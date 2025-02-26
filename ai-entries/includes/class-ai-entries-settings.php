<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AIEntries_Settings {

    public static function add_menu_page() {
        add_menu_page(
            'AIEntries Settings',
            'AIEntries',
            'manage_options',
            'AIEntries-settings',
            [self::class, 'settings_page'],
            'dashicons-visibility'
        );
    }

    public static function settings_page() {
        if (isset($_POST['submit'])) {
            self::process_form();
        }

        $question = esc_attr(get_option('AIEntries_question', ''));
        $num_calls = intval(get_option('AIEntries_num_calls', 1));
        $api_key = esc_attr(get_option('AIEntries_api_key', ''));
        $news_api_key = esc_attr(get_option('AIEntries_news_api_key', ''));
        $category = esc_attr(get_option('AIEntries_category', ''));
        $api_key_stable_diffusion = esc_attr(get_option('AIEntries_api_key_stable_diffusion', ''));

        include plugin_dir_path(__FILE__) . 'settings-page.php';
    }

    private static function process_form() {
        // Verificar el nonce
        if (isset($_POST['aic_entries_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['aic_entries_nonce'])), 'aic_entries_settings_nonce')) {
            // Procesar los datos del formulario
            update_option('AIEntries_question', sanitize_text_field($_POST['question']));
            update_option('AIEntries_num_calls', intval($_POST['num_calls']));
            update_option('AIEntries_news_api_key', sanitize_text_field($_POST['news_api_key']));
            update_option('AIEntries_api_key', sanitize_text_field($_POST['api_key']));
            update_option('AIEntries_category', sanitize_text_field($_POST['category']));
            update_option('AIEntries_api_key_stable_diffusion', sanitize_text_field($_POST['api_key_stable_diffusion']));
        } else {
            // Si el nonce no es válido, muestra un mensaje de error o realiza alguna acción
            echo 'Nonce verification failed. Please try again.';
        }
    }

    public static function handle_ajax() {
        if (!check_ajax_referer('aic_entries_settings_nonce', 'aic_entries_nonce', false)) {
            wp_send_json_error('Nonce verification failed. Please try again.');
        }

        // Procesar los datos del formulario
        update_option('AIEntries_question', sanitize_text_field($_POST['question']));
        update_option('AIEntries_num_calls', intval($_POST['num_calls']));
        update_option('AIEntries_news_api_key', sanitize_text_field($_POST['news_api_key']));
        update_option('AIEntries_api_key', sanitize_text_field($_POST['api_key']));
        update_option('AIEntries_category', sanitize_text_field($_POST['category']));
        update_option('AIEntries_api_key_stable_diffusion', sanitize_text_field($_POST['api_key_stable_diffusion']));

        // Llamar a la función API
        $question = sanitize_text_field($_POST['question']);
        $api_key = sanitize_text_field($_POST['api_key']);
        $category = sanitize_text_field($_POST['category']);

        $response = AIEntries_API::call($question, $api_key, $category);

        if (!is_wp_error($response)) {
            wp_send_json_success('Posts created successfully!'); // O devuelve el número de posts creados
        } else {
            wp_send_json_error($response->get_error_message());
        }
    }
}

// Agregando el manejo AJAX
add_action('wp_ajax_ai_entries_submit', ['AIEntries_Settings', 'handle_ajax']);