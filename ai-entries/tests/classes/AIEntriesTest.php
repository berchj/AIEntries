<?php



require_once __DIR__ . '/../../includes/class-ai-entries-settings.php';
require_once __DIR__ . '/../../includes/class-ai-entries-api.php';
require_once __DIR__ . '/../../includes/class-ai-entries-cron.php';
require_once __DIR__ . '/../../includes/class-ai-entries.php';




class AIEntriesTest extends WP_Mock\Tools\TestCase
{
    
    /**
     *  @covers AIEntries::createInstance
     */
    public function testCreateInstance()
    {   
        WP_Mock::userFunction('plugin_dir_path');       
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
        WP_Mock::userFunction('plugin_dir_path')->andReturn( dirname(dirname( __FILE__ )) );               
        $this->assertFileExists(dirname(plugin_dir_path(__DIR__)) . '/includes/class-ai-entries-settings.php');
        $this->assertFileExists(dirname(plugin_dir_path(__DIR__)) . '/includes/class-ai-entries-api.php');
    }
    /**
     * @covers AIEntries::init_hooks
     */
    public function testInitHooks()
    {        

        $instance = AIEntries::createInstance();
 
        WP_Mock::userFunction('has_action')->andReturn(true);
        WP_Mock::userFunction('has_shortcode')->andReturn(true);
        $this->assertTrue(has_action('admin_menu', [AIEntries_Settings::class, 'add_menu_page']));
        $this->assertTrue(has_shortcode('AIEntries_form'));

        
        $this->assertTrue(has_action('admin_post_AIEntries_submit_form', [AIEntries_API::class, 'AIEntries_handle_form_submission']));
        $this->assertTrue(has_action('admin_post_nopriv_AIEntries_submit_form', [AIEntries_API::class, 'AIEntries_handle_form_submission']));
    }
}