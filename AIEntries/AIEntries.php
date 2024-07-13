<?php

/**
 * Plugin Name:       IA Entries
 * Description:       Automates the creation of WordPress site entries based on an AI API call to Google's GEMINI
 * Version:           1.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Julio Bermúdez
 * Author URI:        https://github.com/berchj/
 * Plugin URI:        https://github.com/berchj/AIEntries
 * License:           MIT
 */

function set_featured_image_from_base64($base64_image, $post_id)
{
    // Validar que el post ID sea un entero
    if (!is_int($post_id)) {
        return false;
    }

    // Decodificar la cadena base64
    $image_data = base64_decode($base64_image);

    // Crear un archivo temporal para almacenar la imagen
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['path'] . '/' . uniqid() . '.jpg';
    file_put_contents($file_path, $image_data);

    // Verificar el tipo MIME del archivo para asegurarse que es una imagen
    $mime_type = mime_content_type($file_path);
    if (strpos($mime_type, 'image') === false) {
        return false;
    }

    // Subir el archivo a la biblioteca de medios de WordPress
    $filetype = wp_check_filetype(basename($file_path), null);

    $attachment = array(
        'guid' => $upload_dir['url'] . '/' . basename($file_path),
        'post_mime_type' => $filetype['type'],
        'post_title' => sanitize_file_name(basename($file_path)),
        'post_content' => '',
        'post_status' => 'inherit',
    );

    $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);

    // Generar metadatos para el archivo adjunto y las diferentes miniaturas
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
    wp_update_attachment_metadata($attach_id, $attach_data);

    // Establecer como imagen destacada del post
    set_post_thumbnail($post_id, $attach_id);

    return true;
}

function generate_post_image_with_AI($title)
{
    $base_url = 'https://api.stability.ai';
    $url = "$base_url/v1/generation/stable-diffusion-v1-6/text-to-image";

    // Obtener la clave API de Stable Diffusion desde las opciones almacenadas
    $api_key_stable_diffusion = get_option('AIEntries_api_key_stable_diffusion', '');

    $body = json_encode(array(
        "text_prompts" => array(
            array(
                "text" => $title,
            ),
        ),
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
        echo 'Error occurred: ' . $response->get_error_message();
    }

    $body_request = json_decode($response['body'], true);

    return $body_request['artifacts'][0]['base64'];
}

function create_new_entry($title, $content, $category_name)
{
    // Check if the current user has permissions to publish posts
    if (current_user_can('publish_posts')) {
        // Get the category ID by name or create the category if it doesn't exist
        $category_id = get_term_by('name', $category_name, 'category');
        if (!$category_id) {
            // Create the category
            $new_category = wp_insert_term(
                $category_name, // The term which you want to insert
                'category' // The taxonomy to which the term belongs
            );

            if (is_wp_error($new_category)) {
                echo 'There was an error creating the category: ' . $new_category->get_error_message();
                return;
            }

            $category_id = $new_category['term_id'];
        } else {
            $category_id = $category_id->term_id;
        }

        // Define the post data
        $new_entry = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish', // Options: 'publish', 'draft', 'private', 'pending'
            'post_category' => array($category_id), // Categoría de la publicación
        );

        // Insert the post into the database
        $post_id = wp_insert_post($new_entry);

        // Check for errors
        if (is_wp_error($post_id)) {
            echo 'There was an error creating the post: ' . $post_id->get_error_message();
        } else {

            //get image base64
            $base64_image = generate_post_image_with_AI($title);

            //save image into post
            set_featured_image_from_base64($base64_image,$post_id);

            //return
            return get_post($post_id);
        }
    } else {
        echo 'You do not have permission to publish posts.';
    }
}

// Hook to add the menu page
add_action('admin_menu', 'AIEntries_menu');

function AIEntries_menu()
{
    add_menu_page(
        'AIEntries Settings', // Page title
        'AIEntries', // Menu title
        'manage_options', // Required capability to access
        'AIEntries-settings', // Page slug
        'AIEntries_settings_page' // Function to display the page content
    );
}

function call($question, $api_key, $category_name, $iterator = "")
{
    // URL for the API call
    $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=' . $api_key;
    // Request arguments
    $args = array(
        'timeout' => 60,
        'body' => json_encode(array(
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
        return 'Error :' . $response->get_error_message();
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

    // Create WordPress post
    return create_new_entry($article['title'], $article['content'], $category_name);
}

function AIEntries_settings_page()
{
    if (isset($_POST['submit'])) {
        $question = sanitize_text_field($_POST['question']);
        $num_calls = intval($_POST['num_calls']);
        $api_key = sanitize_text_field($_POST['api_key']);
        $category_name = sanitize_text_field($_POST['category']);
        // Añadir campo para la clave API de Stable Diffusion
        $api_key_stable_diffusion = sanitize_text_field($_POST['api_key_stable_diffusion']);

        update_option('AIEntries_question', $question);
        update_option('AIEntries_num_calls', $num_calls);
        update_option('AIEntries_api_key', $api_key);
        update_option('AIEntries_category', $category_name);
        update_option('AIEntries_api_key_stable_diffusion', $api_key_stable_diffusion);

        $responses = [];
        $errors = [];

        if ($num_calls > 0) {
            for ($i = 0; $i < $num_calls; $i++) {
                $response = $i > 0 ? call($question, $api_key, $category_name, '') : call($question, $api_key, $category_name, 'more distinct');

                if (!is_wp_error($response)) {
                    $responses[] = $response;
                } else {
                    $errors[] = $response->get_error_message();
                }
            }
        } else {
            $errors[] = 'Number of calls must be greater than 0.';
        }
    } else {
        $responses = [];
        $errors = [];
    }

    $question = get_option('AIEntries_question', '');
    $num_calls = get_option('AIEntries_num_calls', 1);
    $api_key = get_option('AIEntries_api_key', '');
    $category_name = get_option('AIEntries_category', '');
    // Recuperar opción para la clave API de Stable Diffusion
    $api_key_stable_diffusion = get_option('AIEntries_api_key_stable_diffusion', '');

    ?>
    <div class="wrap">
        <h2>AIEntries Settings</h2>
        <p>The api call returns jsons using this JSON schema : <code>{'title': str,'content':str}</code> to automate the creation of WordPress posts</p>
        <p>This plugin runs once a day according to the following parameters:</p>

        <form method="post" action="">
            <label for="question">
                <h3>Theme about the entries you want to create:</h3>
            </label>
            <input type="text" id="question" name="question" value="<?php echo esc_attr($question); ?>" required><br>
            <label for="num_calls">
                <h3>Number of posts created based on GEMINI API Call (we recommend 10 because possible errors calling the API):</h3>
            </label>
            <input type="number" id="num_calls" name="num_calls" min="1" value="<?php echo intval($num_calls); ?>" required><br>
            <label for="api_key">
                <h3>GEMINI API Key:</h3>
            </label>
            <input type="password" id="api_key" name="api_key" value="<?php echo esc_attr($api_key); ?>" required><br>
            <p>Note: You can get one for free <a target="_blank" href="https://ai.google.dev/gemini-api/docs/api-key?hl=es-419">here</a></p>
            <label for="api_key_stable_diffusion">
                <h3>Stable Diffusion API Key:</h3>
            </label>
            <input type="password" id="api_key_stable_diffusion" name="api_key_stable_diffusion" value="<?php echo esc_attr($api_key_stable_diffusion); ?>" required><br>
            <label for="category">
                <h3>Category Name for the posts:</h3>
            </label>
            <input type="text" id="category" name="category" value="<?php echo esc_attr($category_name); ?>" required><br><br>
            <input type="submit" name="submit" value="Submit">
        </form>

        <?php if (!empty($errors)): ?>
            <h3>Errors during creation of posts: <?php echo count($errors) ?></h3>
            <p>The creation of the posts could fail due to the request made to the model API, remember that if the API key you are using is free it could generate this type of errors due to limitations with the requests.
                For more information <a target="_blank" href="https://gemini.google.com/advanced?utm_source=google&utm_medium=cpc&utm_campaign=sem_lp_sl&gad_source=1&gclid=CjwKCAjwqMO0BhA8EiwAFTLgII3-Yyyf4-LZHwQgJNtl7-LAGz9OmcyBNtUVowaQXhznCYZx3qlGCxoCyvUQAvD_BwE">click here</a></p>
            <?php foreach ($errors as $error): ?>
                <p style="color: red;">1 post create failed due to: <?php echo esc_html($error); ?></p>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($responses)): ?>
            <h3>Posts Created by GEMINI's API Call:</h3>
            <?php foreach ($responses as $response): ?>
                <pre><a href="<?php echo get_post_permalink($response->ID); ?>" target="_blank"><?php echo get_the_title($response->ID); ?></a></pre>
            <?php endforeach; ?>
        <?php endif; ?>
        <p style="color: red;"><b>DISCLAIMER: this is a work in progress. The quantity of posts created by this plugin depends on your API key limitations</b></p>
    </div>
    <?php
}

function AIEntries_daily_task()
{
    $question = get_option('AIEntries_question', '');
    $num_calls = get_option('AIEntries_num_calls', 1);
    $api_key = get_option('AIEntries_api_key', '');
    $category_name = get_option('AIEntries_category', '');

    if (!empty($question) && $num_calls > 0) {
        for ($i = 0; $i < $num_calls; $i++) {
            call($question, $api_key, $category_name);
        }
    }
}

// Schedule cron task
if (!wp_next_scheduled('AIEntries_daily_cron_job')) {
    wp_schedule_event(time(), 'daily', 'AIEntries_daily_cron_job');
}

// Hook to execute the task
add_action('AIEntries_daily_cron_job', 'AIEntries_daily_task');

// On plugin activation, ensure the cron job is scheduled
register_activation_hook(__FILE__, 'AIEntries_activation');
function AIEntries_activation()
{
    if (!wp_next_scheduled('AIEntries_daily_cron_job')) {
        wp_schedule_event(time(), 'daily', 'AIEntries_daily_cron_job');
    }
}

// On plugin deactivation, remove the cron job
register_deactivation_hook(__FILE__, 'AIEntries_deactivation');
function AIEntries_deactivation()
{
    $timestamp = wp_next_scheduled('AIEntries_daily_cron_job');
    wp_unschedule_event($timestamp, 'AIEntries_daily_cron_job');
}
