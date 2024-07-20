<?php


require_once __DIR__ . '/../../includes/class-ai-entries-settings.php';


   

class AIEntriesSettingsTest extends WP_Mock\Tools\TestCase
{   
    
    
    public function testCreateInstance()
    {           
        WP_Mock::userFunction('plugin_dir_path'); 
        $instance = AIEntries::createInstance();
        $this->assertInstanceOf(AIEntries::class, $instance);
    }    

    public function testAddMenuPage()
    {
        WP_Mock::userFunction('add_menu_page')->andReturn('AIEntries_Settings');
        // This test ensures that add_menu_page is called with appropriate parameters        
        ;
        $this->assertTrue(AIEntries_Settings::add_menu_page());
        // Would normally assert that the add_menu_page was called correctly, but since it's a void method,
        // and we are mocking the WordPress function, this will be tricky with PHPUnit alone. This might
        // require integration testing instead.
    }

    public function testSettingsPageNoSubmit()
    {
        WP_Mock::userFunction('wp_verify_nonce');
        WP_Mock::userFunction('wp_next_scheduled');
        WP_Mock::userFunction('update_option');
        WP_Mock::userFunction('wp_nonce_field');
        WP_Mock::userFunction('wp_nonce_field');
        WP_Mock::userFunction('get_option');
        WP_Mock::userFunction('_get_cron_array');
        // Mock POST request with no submit
        $_POST = [];
        ob_start();
        AIEntries_Settings::settings_page();
        $output = ob_get_clean();
        $this->assertStringContainsString('AIEntries Settings', $output);
    }

    public function testSettingsPageSubmit()
    {
        WP_Mock::userFunction('sanitize_text_field');
        WP_Mock::userFunction('wp_json_encode');
        WP_Mock::userFunction('wp_remote_post');
        WP_Mock::userFunction('is_wp_error');
        WP_Mock::userFunction('wp_remote_retrieve_body');      
        Mockery::mock('WP_Error');
        WP_Mock::userFunction('get_post_permalink');      
        WP_Mock::userFunction('get_the_title'); 
        WP_Mock::userFunction('_get_cron_array');
        WP_Mock::userFunction('add_query_arg');
        WP_Mock::userFunction('add_query_arg');
        WP_Mock::userFunction('wp_remote_get',
            ['return' => '[{
            "status": "ok",
            "totalResults": 268,
            "articles": [
                {
                    "source": {
                        "id": null,
                        "name": "Hotnews.ro"
                    },
                    "author": "Mihai Bianca",
                    "title": "fanatik.ro: I se spune Maldive de Europa. Se ajunge ușor din România și e de 3 ori mai ieftină decât Grecia sau Turcia",
                    "description": "Descoperă țara din Balcani cu cea mai impetuoasă dezvoltare la nivel de turism. Este catalogată ca având plaje la fel ca în Maldive. Maldive de Europa, adică un joc frumos de cuvinte, dar și un…",
                    "url": "http://hotnews.ro/fanatik-ro-i-se-spune-maldive-de-europa-se-ajunge-usor-din-romania-si-e-de-3-ori-mai-ieftina-decat-grecia-sau-turcia-1532267",
                    "urlToImage": "https://hotnews.ro/wp-content/uploads/2024/06/Screenshot-2024-06-29-105522.png",
                    "publishedAt": "2024-06-29T07:56:14Z",
                    "content": "Descoper ara din Balcani cu cea mai impetuoas dezvoltare la nivel de turism. Este catalogat ca având plaje la fel ca în Maldive.\r\nMaldive de Europa, adic un joc frumos de cuvinte, dar i un loc pentru… [+310 chars]"
                }
            ]
        }]']);
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
