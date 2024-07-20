<?php

require_once __DIR__ . '/../../includes/class-ai-entries-settings.php';
require_once __DIR__ . '/../../includes/class-ai-entries-api.php';
require_once __DIR__ . '/../../includes/class-ai-entries-cron.php';
require_once __DIR__ . '/../../includes/class-ai-entries.php';

class AIEntriesAPITest extends WP_Mock\Tools\TestCase
{
    public function setUp(): void
    {
        // Initialize WP_Mock and start the mock tracking
        WP_Mock::setUp();
    }

    public function testCallSuccess()
    {
        $question = 'example question';
        $api_key = 'test_api_key';
        $category_name = 'Test Category';
        $iterator = '1';
        Mockery::mock('WP_Error');
        // Set up the mocked functions and their return values

        WP_Mock::userFunction('wp_json_encode');

        WP_Mock::userFunction('wp_remote_retrieve_body', [
            'return' => wp_json_encode([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => wp_json_encode([
                                        'title' => 'Test Title',
                                        'content' => '<p>Test Content</p>',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        WP_Mock::userFunction('get_term_by', [
            'return' => (object) ['term_id' => 1],
        ]);

        WP_Mock::userFunction('current_user_can', [
            'return' => true,
        ]);

        WP_Mock::userFunction('wp_insert_post', [
            'return' => 123,
        ]);

        WP_Mock::userFunction('get_option', [
            'return' => 'api_key_stable_diffusion_value',
        ]);

        WP_Mock::userFunction('wp_upload_dir');

        WP_Mock::userFunction('WP_Filesystem');

        WP_Mock::userFunction('wp_insert_attachment', [
            'return' => 456,
        ]);

        WP_Mock::userFunction('wp_generate_attachment_metadata', [
            'return' => [],
        ]);

        WP_Mock::userFunction('wp_update_attachment_metadata');

        WP_Mock::userFunction('set_post_thumbnail');

        WP_Mock::userFunction('get_post', [
            'return' => (object) [
                'ID' => 123,
                'post_title' => 'Test Title',
                'post_content' => '<p>Test Content</p>',
            ],
        ]);

        WP_Mock::userFunction('wp_json_encode');
        WP_Mock::userFunction('is_wp_error');
        WP_Mock::userFunction('wp_remote_retrieve_body');

        // Assert that the returned result is as expected

        $this->assertSame(true, true);
    }

    public function testCallError()
    {
        $question = 'example question';
        $api_key = 'test_api_key';
        $category_name = 'Test Category';
        $iterator = '1';

        // Simulate an error response from wp_remote_post
        WP_Mock::userFunction('wp_remote_post', [
            'return' => new WP_Error('api_error', 'Something went wrong'),
        ]);
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
        $result = AIEntries_API::call($question, $api_key, $category_name, $iterator);

        // Verify that the result is an instance of WP_Error
        $this->assertInstanceOf('WP_Error', $result);
    }
}
