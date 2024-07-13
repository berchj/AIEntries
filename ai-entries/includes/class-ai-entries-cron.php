<?php

class AIEntries_Cron {

    public static function daily_task() {
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
}
