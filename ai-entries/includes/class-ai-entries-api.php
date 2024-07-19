<?php

class AIEntries_API
{

    public static function call($question, $api_key, $category_name, $iterator = "")
    {
        // URL for the API call
        $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=' . $api_key;

        // Request arguments
        $args = array(
            'timeout' => 60,
            'body' => wp_json_encode(array(
                "contents" => array(
                    array(
                        "parts" => array(
                            array(
                                "text" => "List 1 " . $iterator . " article about " . $question . ". Using this JSON schema :{'title': str,'content':str} (Return only the JSON String without spaces) the title must be good for SEO and the content must be in html string",
                            ),
                        ),
                    ),
                ),
            )),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'method' => 'POST',
        );

        // Response
        $response = wp_remote_post($url, $args);

        // If anything goes wrong
        if (is_wp_error($response)) {
            return new WP_Error('api_error', $response->get_error_message());
        }

        // Retrieve body
        $body = wp_remote_retrieve_body($response);

        // Format data
        if (empty($body)) {
            return new WP_Error('api_error', 'Empty response from API.');
        }

        $data = json_decode($body, true);

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return new WP_Error('api_error', 'Invalid API response structure.');
        }

        // AI Post
        $article = json_decode($data['candidates'][0]['content']['parts'][0]['text'], true);

        if (!isset($article['title']) || !isset($article['content'])) {
            return new WP_Error('api_error', 'API response does not contain title or content.');
        }

        return self::create_new_entry($article['title'], $article['content'], $category_name);
    }

    private static function create_new_entry($title, $content, $category_name)
    {
        if (current_user_can('publish_posts')) {
            $category_id = get_term_by('name', $category_name, 'category');
            if (!$category_id) {
                $new_category = wp_insert_term($category_name, 'category');
                if (is_wp_error($new_category)) {
                    return new WP_Error('insert_error', $new_category->get_error_message());
                }
                $category_id = $new_category['term_id'];
            } else {
                $category_id = $category_id->term_id;
            }

            $new_entry = array(
                'post_title' => $title,
                'post_content' => $content,
                'post_status' => 'publish',
                'post_category' => array($category_id),
            );

            $post_id = wp_insert_post($new_entry);

            if (is_wp_error($post_id)) {
                return new WP_Error('insert_error', $post_id->get_error_message());
            } else {
                $base64_image = self::generate_post_image_with_AI($title);
                self::set_featured_image_from_base64($base64_image, $post_id);

                wp_clear_scheduled_hook('AIEntries_daily_cron_job');

                wp_schedule_event(strtotime('now') + (1 * 60 * 60) , 'hourly', 'AIEntries_daily_cron_job');

                return get_post($post_id);
            }
        }
        return new WP_Error('permission_error', 'You do not have permission to publish posts.');
    }

    private static function generate_post_image_with_AI($title)
    {
        $base_url = 'https://api.stability.ai';
        $url = "$base_url/v1/generation/stable-diffusion-v1-6/text-to-image";
        $api_key_stable_diffusion = get_option('AIEntries_api_key_stable_diffusion', '');

        $body = wp_json_encode(array(
            "text_prompts" => array(array("text" => $title)),
            "cfg_scale" => 7,
            "height" => 1024,
            "width" => 1024,
            "samples" => 1,
            "steps" => 30,
        ));

        $response = wp_remote_post($url, array(
            'timeout' => 600,
            'method' => 'POST',
            'headers' => array(
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer $api_key_stable_diffusion",
            ),
            'body' => $body,
        ));

        if (is_wp_error($response)) {
            return '';
        }

        $body_request = json_decode($response['body'], true);
        return $body_request['artifacts'][0]['base64'];
    }

    private static function set_featured_image_from_base64($base64_image, $post_id)
    {
        if (!is_int($post_id)) {
            return false;
        }

        // Inicializar WP_Filesystem
        WP_Filesystem();

        global $wp_filesystem;

        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['path'] . '/' . uniqid() . '.jpg';

        // Usar WP_Filesystem para escribir el contenido en el archivo
        if (!$wp_filesystem->put_contents($file_path, base64_decode($base64_image), FS_CHMOD_FILE)) {
            return false;
        }

        $mime_type = mime_content_type($file_path);

        if (strpos($mime_type, 'image') === false) {
            return false;
        }

        $filetype = wp_check_filetype(basename($file_path), null);

        $attachment = array(
            'guid' => $upload_dir['url'] . '/' . basename($file_path),
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name(basename($file_path)),
            'post_content' => '',
            'post_status' => 'inherit',
        );

        $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);

        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($post_id, $attach_id);

        return true;
    }

}
