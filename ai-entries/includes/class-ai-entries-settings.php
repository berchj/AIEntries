<?php

class AIEntries_Settings {

    public static function add_menu_page() {
        add_menu_page(
            'AIEntries Settings',
            'AIEntries',
            'manage_options',
            'AIEntries-settings',
            [self::class, 'settings_page']
        );
    }

    public static function settings_page() {
        if (isset($_POST['submit'])) {
            update_option('AIEntries_question', sanitize_text_field($_POST['question']));
            update_option('AIEntries_num_calls', intval($_POST['num_calls']));
            update_option('AIEntries_api_key', sanitize_text_field($_POST['api_key']));
            update_option('AIEntries_category', sanitize_text_field($_POST['category']));
            update_option('AIEntries_api_key_stable_diffusion', sanitize_text_field($_POST['api_key_stable_diffusion']));

            $responses = [];
            $errors = [];
            for ($i = 0; $i < intval($_POST['num_calls']); $i++) {
                $response = AIEntries_API::call($_POST['question'], $_POST['api_key'], $_POST['category'], $i > 0 ? '' : 'more distinct');
                if (!is_wp_error($response)) {
                    $responses[] = $response;
                } else {
                    $errors[] = $response->get_error_message();
                }
            }
        } else {
            $responses = [];
            $errors = [];
        }

        $question = get_option('AIEntries_question', '');
        $num_calls = get_option('AIEntries_num_calls', 1);
        $api_key = get_option('AIEntries_api_key', '');
        $category = get_option('AIEntries_category', '');
        $api_key_stable_diffusion = get_option('AIEntries_api_key_stable_diffusion', '');

        include plugin_dir_path(__FILE__) . 'settings-page.php';
    }
}
