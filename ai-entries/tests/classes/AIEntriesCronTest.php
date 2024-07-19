<?php

require_once __DIR__ . '/../../includes/class-ai-entries-cron.php';


class AIEntriesCronTest extends WP_Mock\Tools\TestCase
{

    public function test_my_six_hour_function()
    {
        // Call the method to test        
        $this->assertNull(AIEntries_Cron::my_six_hour_function());
    }

    public function test_check_six_hour_function()
    {
       
        // Call the method to test
        WP_Mock::userFunction('get_transient'); 
        WP_Mock::userFunction('set_transient'); 
        $this->assertNull(AIEntries_Cron::check_six_hour_function());
    }

    public function test_show_all_cron_tasks()
    {
        WP_Mock::userFunction('_get_cron_array');
        $output = AIEntries_Cron::show_all_cron_tasks();

        $this->assertNotEmpty($output);
    }

}
