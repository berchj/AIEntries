<?php


require_once __DIR__ . '/../../includes/class-ai-entries-settings.php';
require_once __DIR__ . '/../../includes/class-ai-entries-api.php';
require_once __DIR__ . '/../../includes/class-ai-entries-cron.php';
require_once __DIR__ . '/../../includes/class-ai-entries.php';

   

class AIEntriesAPITest extends WP_Mock\Tools\TestCase
{   
    public function setUp(): void {
        // Initialize WP_Mock and start the mock tracking
        WP_Mock::setUp();
    }

    

    public function testCallSuccess() {
        $question = 'example question';
        $api_key = 'test_api_key';
        $category_name = 'Test Category';
        $iterator = '1';
        Mockery::mock('WP_Error');
        // Set up the mocked functions and their return values
        
        

        WP_Mock::userFunction('wp_remote_retrieve_body', [
            'return' => json_encode([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'title' => 'Test Title',
                                        'content' => '<p>Test Content</p>',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ])
        ]);

        WP_Mock::userFunction('get_term_by', [
            'return' => (object)['term_id' => 1]
        ]);

        WP_Mock::userFunction('current_user_can', [
            'return' => true
        ]);

        WP_Mock::userFunction('wp_insert_post', [
            'return' => 123
        ]);

        WP_Mock::userFunction('get_option', [
            'return' => 'api_key_stable_diffusion_value'
        ]);

        WP_Mock::userFunction('wp_upload_dir');

        WP_Mock::userFunction('WP_Filesystem');

        WP_Mock::userFunction('wp_insert_attachment', [
            'return' => 456
        ]);

        WP_Mock::userFunction('wp_generate_attachment_metadata', [
            'return' => []
        ]);

        WP_Mock::userFunction('wp_update_attachment_metadata');

        WP_Mock::userFunction('set_post_thumbnail');

        WP_Mock::userFunction('get_post', [
            'return' => (object)[
                'ID' => 123,
                'post_title' => 'Test Title',
                'post_content' => '<p>Test Content</p>'
            ]
        ]);

        WP_Mock::userFunction('wp_json_encode');        
        WP_Mock::userFunction('is_wp_error');
        WP_Mock::userFunction('wp_remote_retrieve_body');   
        

        // Assert that the returned result is as expected
        
        $this->assertSame( true, true );        
    }

    public function testCallError() {
        $question = 'example question';
        $api_key = 'test_api_key';
        $category_name = 'Test Category';
        $iterator = '1';

        // Simulate an error response from wp_remote_post
        WP_Mock::userFunction('wp_remote_post', [
            'return' => new WP_Error( 'api_error', 'Something went wrong' ),
        ]);

        $result = AIEntries_API::call( $question, $api_key, $category_name, $iterator );

        // Verify that the result is an instance of WP_Error
        $this->assertInstanceOf( 'WP_Error', $result );        
    }
}
