<?php

require_once dirname(__DIR__) . '/BaseTest.php';

require_once __DIR__ . '/../../includes/class-ai-entries-settings.php';
require_once __DIR__ . '/../../includes/class-ai-entries-api.php';
require_once __DIR__ . '/../../includes/class-ai-entries-cron.php';
require_once __DIR__ . '/../../includes/class-ai-entries.php';


use PHPUnit\Framework\TestCase;

class AIEntriesTest extends BaseTest
{
    /**
     *  @covers AIEntries::createInstance
     */
    public function testCreateInstance()
    {
        $instance = AIEntries::createInstance();
        $this->assertInstanceOf(AIEntries::class, $instance);
    }
    /**
     *  @covers AIEntries::instance
     */
    public function testInstance()
    {
        $instance1 = AIEntries::instance();
        $instance2 = AIEntries::instance();

        $this->assertSame($instance1, $instance2);
    }
    /**
     * @covers AIEntries::includes
     */
    public function testIncludes()
    {
        $this->expectOutputString('');

        $instance = AIEntries::createInstance();
        
        $this->assertFileExists(dirname(plugin_dir_path(__DIR__)) . '/includes/class-ai-entries-settings.php');
        $this->assertFileExists(dirname(plugin_dir_path(__DIR__)) . '/includes/class-ai-entries-api.php');
    }
    /**
     * @covers AIEntries::init_hooks
     */
    public function testInitHooks()
    {
        $this->expectOutputString('');

        $instance = AIEntries::createInstance();

        
        $this->assertTrue(has_action('admin_menu', [AIEntries_Settings::class, 'add_menu_page']));
        $this->assertTrue(has_shortcode('AIEntries_form'));

        
        $this->assertTrue(has_action('admin_post_AIEntries_submit_form', [AIEntries_API::class, 'AIEntries_handle_form_submission']));
        $this->assertTrue(has_action('admin_post_nopriv_AIEntries_submit_form', [AIEntries_API::class, 'AIEntries_handle_form_submission']));
    }
}