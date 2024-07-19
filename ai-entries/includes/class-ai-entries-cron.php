<?php

class AIEntries_Cron
{
    // Function to check and run the function every 5 minutes
    public static function my_six_hour_function()
    {
        
        AIEntries_Cron::daily_task(); // Execute the function
          
    }

    // Function to check and run the function every 6 hours
    public static function check_six_hour_function()
    {
        $last_executed_time = get_transient('last_six_hour_execution');

        // Check if it's time to run the function (6 hours have passed)
        if (!$last_executed_time || $last_executed_time < strtotime('-6 hours')) {
            AIEntries_Cron::my_six_hour_function(); // Execute the function
            set_transient('last_six_hour_execution', strtotime('now')); // Update the last execution time
        }
    }

    public function daily_task()
    {
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

    public static function show_all_cron_tasks()
    {
        // Obtener las tareas cron
        $cron = _get_cron_array();

        if (empty($cron)) {
            return 'No tasks scheduled.';
        }
        
        $output = '<table border="1 | 0" style="text-align:center;width:100%">';
        $output .= '<tr><th>  <h2>Next Excecution</h2>  </th><th>  <h2>Hook Name</h2>  </th><th>  <h2>Function Name</h2>  </th></tr>';

        foreach ($cron as $timestamp => $cronhooks) {
            foreach ((array) $cronhooks as $hook => $events) {
                if ($hook == 'AIEntries_daily_cron_job') {
                    $callbacks = array();
                    foreach ((array) $events as $event) {
                        if (isset($GLOBALS['wp_filter'][$hook])) {
                            $callbacks[] = $GLOBALS['wp_filter'][$hook];
                        }
                    }
                    //print_r($callbacks);
                    foreach ($callbacks as $priority => $callback) {
                        foreach ($callback->callbacks as $function_data) {
                            foreach ($function_data as $function_parts) {
                                $output .= '<tr>';
                                $output .= '<td><p>' . esc_html(gmdate('Y-m-d H:i:s', $timestamp)) . '</p></td>';
                                $output .= '<td><p>' . esc_html($hook) . '</p></td>';
                                $output .= '<td><p>' . esc_html(strval($function_parts['function'][1] ? $function_parts['function'][1] : $function_parts['function'][0])) . '</p></td>';
                                $output .= '</tr>';
                            }

                        }
                    }
                    /* // Suponiendo que $array contiene el array proporcionado
                foreach ($callbacks as $priority => $callbacks) {
                foreach ($callbacks as $callback_name => $callback_info) {
                $function_info = $callback_info['function'];

                // Obtener el objeto/clase y el nombre de la función
                $function_object = $function_info[0];
                $function_name = $function_info[1];

                echo 'Prioridad: ' . $priority . '<br>';
                echo 'Función: ' . $function_object . '::' . $function_name . '<br>';

                }
                } */

                }

            }
        }
        if (!wp_next_scheduled('AIEntries_daily_cron_job')) {
            echo "\n \n No excecutions scheduled for: " . esc_html($hook_name);
        }
        echo esc_html($output);
    }
}
