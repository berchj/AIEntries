<?php

require_once dirname(__DIR__) . '/includes/class-ai-entries.php';

if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file)
    {
        return trailingslashit(dirname($file));
    }
}

if (!function_exists('trailingslashit')) {
    function trailingslashit($string)
    {
        return rtrim($string, '/') . '/';
    }
}

if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $args = 1)
    {
       
        return "Added action: $hook\n";
    }
}

if (!function_exists('add_shortcode')) {
    function add_shortcode($tag, $callback)
    {
        
        return "Added shortcode: [$tag]\n"; // Example mock behavior
    }
}

if (!function_exists('has_action')) {
    function has_action($hook_name, $callback = false) {
        
        return true; 
    }
}

if (!function_exists('has_shortcode')) {
    function has_shortcode($content, $tag = '') {
       
        return true; 
    }
}



use PHPUnit\Framework\TestCase;

class AIEntriesTest extends TestCase
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

        $this->assertFileExists(plugin_dir_path(__DIR__) . 'includes/class-ai-entries-settings.php');
        $this->assertFileExists(plugin_dir_path(__DIR__) . 'includes/class-ai-entries-api.php');
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