<?php

/**
 *
 *
 *
 * @author            Julio César Bermúdez
 *
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
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
function upload_image_to_media_library($image_url, $post_id, $title)
{
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    // Verificar y ajustar permisos de la carpeta de cargas de WordPress
    $upload_dir = wp_upload_dir();
    $upload_dir_permissions = 0777; // Cambiar permisos a 755
    $upload_dir_path = $upload_dir['basedir'];

    if (!file_exists($upload_dir_path)) {
        if (!mkdir($upload_dir_path, $upload_dir_permissions, true)) {
            echo 'Error al crear directorio de cargas.';
            return;
        }
    }

    if (!is_writable($upload_dir_path)) {
        if (!chmod($upload_dir_path, $upload_dir_permissions)) {
            echo 'Error al cambiar permisos de directorio de cargas.';
            return;
        }
    }

    // Obtener el tipo de archivo basado en la URL de la imagen
    $filetype = wp_check_filetype(basename($image_url), null);

    $attachment = array(
        'post_mime_type' => $filetype['type'],
        'post_title' => sanitize_file_name($title),
        'post_content' => '',
        'post_status' => 'inherit',
    );

    // Subir la imagen a la biblioteca de medios
    $attachment_id = media_handle_sideload(array('name' => basename($image_url), 'file' => $image_url), $post_id, $title, $attachment);

    if (is_wp_error($attachment_id)) {
        // Manejar el error si la carga de la imagen falla
        echo 'Error al subir la imagen: ' . $attachment_id->get_error_message();
        return;
    }

    // Asignar la imagen como miniatura del post
    set_post_thumbnail($post_id, $attachment_id);
    return $attachment_id;
}

function generate_post_image_with_AI($title)
{
    $url = 'https://api.limewire.com/api/image/generation';
    $api_key = 'lmwr_sk_8lbP6JknCR_51LhiyJPHHXSjEhPlOFhrr6oU1kumueZRYMCL'; // Reemplaza con tu clave de API Limewire

    $args = array(
        'timeout' => 60,
        'body' => json_encode(array(
            'prompt' => $title,
            'aspect_ratio' => '1:1',
        )),
        'headers' => array(
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'X-Api-Version' => 'v1',
        ),
    );

    $response = wp_remote_post($url, $args);

    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message();
    }

    $body = wp_remote_retrieve_body($response);
    print_r($body);
    return json_decode($body, true)['data'][0]['asset_url'];

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
            /* //get image url
            $image_url = generate_post_image_with_AI($title);
            //upload image
            $image_id = upload_image_to_media_library($image_url, $post_id, $title);
            // Verificar si la carga de la imagen fue exitosa
            if ($image_id) {
            echo 'Imagen subida correctamente con ID: ' . $image_id;
            } */
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

        update_option('AIEntries_question', $question);
        update_option('AIEntries_num_calls', $num_calls);
        update_option('AIEntries_api_key', $api_key);
        update_option('AIEntries_category', $category_name);

        $responses = [];
        $errors = [];

        if ($num_calls > 0) {
            for ($i = 0; $i < $num_calls; $i++) {

                $i > 0 ? $response = call($question, $api_key, $category_name, '') : $response = call($question, $api_key, $category_name, 'more distinct');

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

    ?>
    <div class="wrap">
        <h2>AIEntries Settings</h2>
        <p>The api call returns jsons using this JSON schema : <code>{'title': str,'content':str}</code> to automatize the creation of wordpress posts</p>
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
            <p>note: You can get one for free <a target="_blank" href="https://ai.google.dev/gemini-api/docs/api-key?hl=es-419">here</a></p>
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
                <p style="color: red;"> 1 post create failed due <?php echo esc_html($error); ?></p>
            <?php endforeach;?>
        <?php endif;?>

        <?php if (!empty($responses)): ?>
            <h3>Posts Created by GEMINI's API Call:</h3>
            <?php foreach ($responses as $response): ?>
                <pre><a href="<?php echo get_post_permalink($response->ID); ?>" target="_blank" ><?php echo get_the_title($response->ID); ?></a></pre>
            <?php endforeach;?>
        <?php endif;?>
        <p style="color: red;"><b>DISCLAIMER: this is an in-progress project . The quantity of posts created by this plugin depents on your api key limitations</b></p>

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
