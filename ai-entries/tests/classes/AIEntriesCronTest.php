<?php

require_once __DIR__ . '/../../includes/class-ai-entries-cron.php';

class AIEntriesCronTest extends WP_Mock\Tools\TestCase
{
    public function test_daily_task()
    {
        WP_Mock::userFunction('_get_cron_array');
        $this->assertNull(AIEntries_Cron::daily_task());

    }
    public function test_show_all_cron_tasks()
    {
        WP_Mock::userFunction('_get_cron_array');
        $output = AIEntries_Cron::show_all_cron_tasks();

        $this->assertNotEmpty($output);
    }

}
