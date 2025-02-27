<?php

class AIEntries_API
{

    public static $responses = array();
    public static function fetch_news()
    {
        $api_base_url = 'https://newsapi.org/v2/everything';

        // Construir la URL completa con los parámetros
        $url = add_query_arg(array(
            'q' => get_option('AIEntries_question', ''),
            'apiKey' => get_option('AIEntries_news_api_key', ''),
            'pageSize' => get_option('AIEntries_num_calls', 1),
        ), $api_base_url);

        // Realizar la solicitud GET utilizando wp_remote_get
        $response = wp_remote_get($url, array('headers' => array('User-Agent' => get_option('AIEntries_news_api_key', ''))));

        // Verificar si la solicitud fue exitosa
        if (is_wp_error($response)) {
            return "Error: " . $response->get_error_message();
        }

        $body = wp_remote_retrieve_body($response);

        // Decodificar el cuerpo de la respuesta JSON
        $data = json_decode($body, true);

        // Devolver los datos decodificados
        return $data['articles'];
    }

    public static function call($question, $api_key, $category_name, $iterator = "") {
        $news_articles = self::fetch_news();
    
        foreach ($news_articles as $key => $value) {
            $title = sanitize_text_field($value['title']);
            $description = sanitize_text_field($value['description']);
            $content = sanitize_text_field($value['content']);
    
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . urlencode($api_key);
    
            $args = array(
                'timeout' => 600,
                'body' => wp_json_encode(array(
                    "contents" => array(
                        array(
                            "parts" => array(
                                array(
                                    "text" => "Analyze this article: {'title':'" . wp_json_encode($title) . "','description':'" . wp_json_encode($description) . "','content':'" . wp_json_encode($content) . "'}. Now write 1 related original article in English using this JSON schema: {'title': str,'content':str} (Return only the JSON String without spaces and blank spaces or \n) the title must be good for SEO and the content must be in HTML string",
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
    
            $response = wp_remote_post($url, $args);
            
            if (is_wp_error($response)) {
                return new WP_Error('api_error', esc_html($response->get_error_message()));
            }
    
            $body = wp_remote_retrieve_body($response);
    
            if (empty($body)) {
                return new WP_Error('api_error', 'Empty response from API.');
            }
    
            $data = json_decode($body, true);
    
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return new WP_Error('api_error', 'Invalid API response structure.');
            }
    
            // Manejo de la respuesta para obtener el artículo
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'];
            $responseText = trim($responseText, "```json\n"); // Elimina delimitadores de código.
            $responseText = trim($responseText, "\n```");
    
            $article = json_decode($responseText, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new WP_Error('api_error', 'JSON decode error: ' . json_last_error_msg());
            }
    
            if (!isset($article['title']) || !isset($article['content'])) {
                return new WP_Error('api_error', 'Invalid article structure', $data);
            }
    
            $title = sanitize_text_field($article['title']);
            $content = wp_kses_post($article['content']);
            $category_name = sanitize_text_field($category_name);
    
            self::create_new_entry($title, $content, $category_name);
        }
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

                wp_schedule_event(strtotime('now') + (1 * 60 * 60), 'hourly', 'AIEntries_daily_cron_job');

                array_push(self::$responses, get_post($post_id));

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
