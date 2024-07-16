<?php

require_once dirname(__DIR__) . '/BaseTest.php';
require_once __DIR__ . '/../../includes/class-ai-entries-settings.php';
require_once __DIR__ . '/../../includes/class-ai-entries-api.php';
require_once __DIR__ . '/../../includes/class-ai-entries-cron.php';
require_once __DIR__ . '/../../includes/class-ai-entries.php';

use PHPUnit\Framework\TestCase;

class AiEntriesSettingsTest extends BaseTest
{   
    
    /**
     *  @covers AIEntries::createInstance
     */
    public function testCreateInstance()
    {           
        $instance = AIEntries::createInstance();
        $this->assertInstanceOf(AIEntries::class, $instance);
    }    

    public function testAddMenuPage()
    {
        // This test ensures that add_menu_page is called with appropriate parameters
        $this->expectOutputString('');
        AIEntries_Settings::add_menu_page();
        // Would normally assert that the add_menu_page was called correctly, but since it's a void method,
        // and we are mocking the WordPress function, this will be tricky with PHPUnit alone. This might
        // require integration testing instead.
    }

    public function testSettingsPageNoSubmit()
    {
        // Mock POST request with no submit
        $_POST = [];
        ob_start();
        AIEntries_Settings::settings_page();
        $output = ob_get_clean();
        $this->assertStringContainsString('AIEntries Settings', $output);
    }

    public function testSettingsPageSubmit()
    {
        // Mock POST request with submit
        $_POST = [
            'submit' => true,
            'aic_entries_nonce' => 'fake_nonce',
            'question' => 'test question',
            'num_calls' => 1,
            'api_key' => 'test_api_key',
            'category' => 'test_category',
            'api_key_stable_diffusion' => 'test_api_key_stable_diffusion',
        ];
        
        ob_start();
        AIEntries_Settings::settings_page();
        $output = ob_get_clean();
        $this->assertStringContainsString('AIEntries Settings', $output);
    }
}
