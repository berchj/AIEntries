<?php

class AIEntries_Cron {

    public function daily_task() {
        $question = get_option('AIEntries_question', '');
        $num_calls = get_option('AIEntries_num_calls', 1);
        $api_key = get_option('AIEntries_api_key', '');
        $category_name = get_option('AIEntries_category', ''); 

        if (!empty($question) && $num_calls > 0) {
            for ($i = 0; $i < $num_calls; $i++) {
                AIEntries_API::call($question, $api_key, $category_name);
            }
        }
    }
    public static function show_next_scheduled_cron() {
        // Specify the hook name of the cron job you are interested in
        $hook = 'AIEntries_daily_cron_job';
    
        // Get the next scheduled occurrence of the specified cron job
        $next_scheduled = wp_next_scheduled( $hook );
    
        if ( $next_scheduled !== false ) {
            // Convert Unix timestamp to human-readable format
            $next_scheduled_date = date( 'Y-m-d H:i:s', $next_scheduled );
    
            // Display the admin notice
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p>Next scheduled occurrence of the cron job "<strong>' . $hook . '</strong>" is: ' . $next_scheduled_date . '</p>';
            echo '</div>';
        } else {
            // If the event is not scheduled
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p>The cron job "<strong>' . $hook . '</strong>" is not currently scheduled.</p>';
            echo '</div>';
        }
    }
}
