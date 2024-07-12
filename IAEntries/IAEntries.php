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
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

function create_new_entry($title, $content, $category_name)
{
    // Check if the current user has permissions to publish posts
    if (current_user_can('publish_posts')) {
        // Get the category ID by name or create the category if it doesn't exist
        $category_id = get_term_by('name', $category_name, 'category');
        if (!$category_id) {
            // Create the category
            $new_category = wp_insert_term(
                $category_name,  // The term which you want to insert
                'category'       // The taxonomy to which the term belongs
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
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish', // Options: 'publish', 'draft', 'private', 'pending'
            'post_author'   => 1,  // Author ID
            'post_category' => array($category_id) // Categoría de la publicación
        );

        // Insert the post into the database
        $post_id = wp_insert_post($new_entry);

        // Check for errors
        if (is_wp_error($post_id)) {
            echo 'There was an error creating the post: ' . $post_id->get_error_message();
        } else {
            return get_post($post_id);
        }
    } else {
        echo 'You do not have permission to publish posts.';
    }
}

// Hook to add the menu page
add_action('admin_menu', 'iaentries_menu');

function iaentries_menu()
{
    add_menu_page(
        'IAEntries Settings', // Page title
        'IAEntries', // Menu title
        'manage_options', // Required capability to access
        'iaentries-settings', // Page slug
        'iaentries_settings_page' // Function to display the page content
    );
}

function call($question, $api_key, $category_name)
{
    // URL for the API call
    $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=' . $api_key;
    // Request arguments
    $args = array(
        'timeout' => 30,
        'body' => json_encode(array(
            "contents" => array(
                array(
                    "parts" => array(
                        array(
                            "text" => "List 1 article about " . $question . ". Using this JSON schema : {'title': str,'content':str} (Return only the JSON String without spaces)"
                        )
                    )
                )
            )
        )),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'method' => 'POST'
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

function iaentries_settings_page()
{
    if (isset($_POST['submit'])) {
        $question = sanitize_text_field($_POST['question']);
        $num_calls = intval($_POST['num_calls']);
        $api_key = sanitize_text_field($_POST['api_key']);
        $category_name = sanitize_text_field($_POST['category']);

        update_option('iaentries_question', $question);
        update_option('iaentries_num_calls', $num_calls);
        update_option('iaentries_api_key', $api_key);
        update_option('iaentries_category', $category_name);

        $responses = [];
        $errors = [];

        if ($num_calls > 0) {
            for ($i = 0; $i < $num_calls; $i++) {
                $response = call($question, $api_key, $category_name);

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

    $question = get_option('iaentries_question', '');
    $num_calls = get_option('iaentries_num_calls', 1);
    $api_key = get_option('iaentries_api_key', '');
    $category_name = get_option('iaentries_category', '');

?>
    <div class="wrap">
        <h2>IAEntries Settings</h2>
        <p>The api call returns jsons using this JSON schema : <code>{'title': str,'content':str}</code> to automatize the creation of wordpress posts</p>
        <p>This plugin runs once a day according to the following parameters:</p>

        <form method="post" action="">
            <label for="question">
                <h3>The theme about the entries you want to create:</h3>
            </label>
            <input type="text" id="question" name="question" value="<?php echo esc_attr($question); ?>" required><br>
            <label for="num_calls">
                <h3>Number of posts created based on GEMINI API Call (we recommend 10 because possible errors calling the API):</h3>
            </label>
            <input type="number" id="num_calls" name="num_calls" min="1" value="<?php echo intval($num_calls); ?>" required><br>
            <label for="api_key">
                <h3>API Key:</h3>
            </label>
            <input type="password" id="api_key" name="api_key" value="<?php echo esc_attr($api_key); ?>" required><br>
            <label for="category">
                <h3>Category Name for the posts:</h3>
            </label>
            <input type="text" id="category" name="category" value="<?php echo esc_attr($category_name); ?>" required><br><br>
            <input type="submit" name="submit" value="Submit">
        </form>

        <?php if (!empty($errors)) : ?>
            <h3>Errors during creation of posts:</h3>
            <p>The creation of the posts could fail due to the request made to the model API, remember that if the API key you are using is free it could generate this type of errors due to limitations with the requests.
                For more information <a target="_blank" href="https://ai.google.dev/api/rest/v1beta/models/generateContent?hl=es-419">click here</a></p>
            <?php foreach ($errors as $error) : ?>
                <p style="color: red;"><?php echo esc_html($error); ?></p>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($responses)) : ?>
            <h3>Posts Created by GEMINI's API Call:</h3>
            <?php foreach ($responses as $response) : ?>
                <pre><?php echo get_the_title($response->ID); ?></pre>
            <?php endforeach; ?>
        <?php endif; ?>
        <p style="color: red;"><b>DISCLAIMER: this is an in-progress project .</b></p>
    </div>
<?php
}

function iaentries_daily_task()
{
    $question = get_option('iaentries_question', '');
    $num_calls = get_option('iaentries_num_calls', 1);
    $api_key = get_option('iaentries_api_key', '');
    $category_name = get_option('iaentries_category', '');

    if (!empty($question) && $num_calls > 0) {
        for ($i = 0; $i < $num_calls; $i++) {
            call($question, $api_key, $category_name);
        }
    }
}

// Schedule cron task
if (!wp_next_scheduled('iaentries_daily_cron_job')) {
    wp_schedule_event(time(), 'daily', 'iaentries_daily_cron_job');
}

// Hook to execute the task
add_action('iaentries_daily_cron_job', 'iaentries_daily_task');

// On plugin activation, ensure the cron job is scheduled
register_activation_hook(__FILE__, 'iaentries_activation');
function iaentries_activation()
{
    if (!wp_next_scheduled('iaentries_daily_cron_job')) {
        wp_schedule_event(time(), 'daily', 'iaentries_daily_cron_job');
    }
}

// On plugin deactivation, remove the cron job
register_deactivation_hook(__FILE__, 'iaentries_deactivation');
function iaentries_deactivation()
{
    $timestamp = wp_next_scheduled('iaentries_daily_cron_job');
    wp_unschedule_event($timestamp, 'iaentries_daily_cron_job');
}

