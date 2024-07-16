<?php

use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        // Definir constantes de WordPress
        if (!defined('FS_CHMOD_FILE')) {
            define('FS_CHMOD_FILE', 0644);
        }
        // Mocking WordPress functions
        $this->mockWordPressFunctions();
    }

    private function mockWordPressFunctions()
    {
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
            function has_action($hook_name, $callback = false)
            {

                return true;
            }
        }

        if (!function_exists('has_shortcode')) {
            function has_shortcode($content, $tag = '')
            {

                return true;
            }
        }
        if (!function_exists('add_menu_page')) {
            function add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function)
            {
                // Mock implementation
            }
        }

        if (!function_exists('wp_verify_nonce')) {
            function wp_verify_nonce($nonce, $action)
            {
                // Mock implementation - Assume that the nonce is always valid for these tests
                return true;
            }
        }

        if (!function_exists('wp_nonce_field')) {
            function wp_nonce_field($action, $name, $referer = true, $echo = true)
            {
                return '<input type="hidden" name="' . $name . '" value="mock_nonce">';
            }
        }

        if (!function_exists('update_option')) {
            function update_option($option_name, $option_value)
            {
                // Mock implementation
                return true;
            }
        }

        if (!function_exists('get_option')) {
            function get_option($option_name, $default = false)
            {
                // Mock implementation - return some default values
                $options = [
                    'AIEntries_question' => 'sample question',
                    'AIEntries_num_calls' => 1,
                    'AIEntries_api_key' => 'sample_api_key',
                    'AIEntries_category' => 'sample_category',
                    'AIEntries_api_key_stable_diffusion' => 'sample_api_key_stable_diffusion',
                ];
                return isset($options[$option_name]) ? $options[$option_name] : $default;
            }
        }

        if (!function_exists('plugin_dir_path')) {
            function plugin_dir_path($file)
            {
                return __DIR__ . '/';
            }
        }

        if (!function_exists('sanitize_text_field')) {
            function sanitize_text_field($str)
            {
                return filter_var($str, FILTER_SANITIZE_STRING);
            }
        }

        if (!function_exists('esc_attr')) {
            function esc_attr($text)
            {
                return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            }
        }

        if (!function_exists('wp_json_encode')) {
            function wp_json_encode($data, $options = 0, $depth = 512)
            {
                return json_encode($data, $options, $depth);
            }
        }

        if (!function_exists('wp_remote_post')) {
            function wp_remote_post($url, $args)
            {
                // Mock implementation of wp_remote_post
                return [
                    'body' => json_encode([
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
                    ]),
                ];
            }
        }

        if (!function_exists('wp_remote_retrieve_body')) {
            function wp_remote_retrieve_body($response)
            {
                return $response['body'];
            }
        }

        if (!function_exists('is_wp_error')) {
            function is_wp_error($thing)
            {
                return $thing instanceof WP_Error;
            }
        }

        if (!function_exists('current_user_can')) {
            function current_user_can($capability)
            {
                // Mock implementation - Assume the user can publish posts for the purpose of testing
                return $capability === 'publish_posts';
            }
        }

        if (!function_exists('get_term_by')) {
            function get_term_by($field, $value, $taxonomy)
            {
                // Mock implementation of get_term_by
                return null;
            }
        }

        if (!function_exists('wp_insert_term')) {
            function wp_insert_term($term, $taxonomy, $args = [])
            {
                // Mock implementation of wp_insert_term
                return ['term_id' => rand(1, 1000)];
            }
        }

        if (!function_exists('wp_insert_post')) {
            function wp_insert_post($postarr, $wp_error = false)
            {
                // Mock implementation of wp_insert_post
                return rand(1, 10000);
            }
        }

        if (!function_exists('set_post_thumbnail')) {
            function set_post_thumbnail($post, $thumbnail_id)
            {
                // Mock implementation
            }
        }

        if (!function_exists('wp_upload_dir')) {
            function wp_upload_dir($time = null, $create_dir = true, $refresh_cache = false)
            {
                // Mock implementation
                return [
                    'path' => __DIR__,
                    'url' => 'http://example.com/wp-content/uploads',
                    'subdir' => '',
                    'basedir' => __DIR__,
                    'baseurl' => 'http://example.com/wp-content/uploads',
                    'error' => false,
                ];
            }
        }

        if (!function_exists('mime_content_type')) {
            function mime_content_type($filename)
            {
                // Mock implementation
                return 'image/jpeg';
            }
        }

        if (!function_exists('wp_check_filetype')) {
            function wp_check_filetype($filename, $mimes = null)
            {
                // Mock implementation
                return [
                    'ext' => 'jpg',
                    'type' => 'image/jpeg',
                    'proper_filename' => $filename,
                ];
            }
        }

        if (!function_exists('wp_generate_attachment_metadata')) {
            function wp_generate_attachment_metadata($attachment_id, $file)
            {
                // Mock implementation
                return [];
            }
        }

        if (!function_exists('wp_update_attachment_metadata')) {
            function wp_update_attachment_metadata($attachment_id, $data)
            {
                // Mock implementation
                return true;
            }
        }

        if (!function_exists('get_post')) {
            function get_post($post_id, $output = '', $filter = 'raw')
            {
                // Mock implementation
                return (object) [
                    'ID' => $post_id,
                    'post_title' => 'Test Title',
                    'post_content' => '<p>Test Content</p>',
                ];
            }
        }

        if (!function_exists('WP_Filesystem')) {
            function WP_Filesystem()
            {
                // Mock implementation of WP_Filesystem
                return true;
            }
        }

        if (!function_exists('esc_html')) {
            function esc_html($string) {
                return htmlentities($string, ENT_QUOTES, 'UTF-8');
            }
        }

        if (!function_exists('get_post_permalink')) {
            function get_post_permalink($post_id) {
                return "https://example.com/post/" . $post_id; // Simulación de la estructura de una URL de un post
            }
        }
        if (!function_exists('get_the_title')) {
            function get_the_title($post_id) {
                // Aquí se simula una obtención ficticia del título del post basado en el ID del post
                $titles = array(
                    123 => 'Título del Post 1',
                    456 => 'Título del Post 2'
                );
            
                // Verificar si el ID del post existe en el array de títulos simulados
                if (array_key_exists($post_id, $titles)) {
                    return $titles[$post_id];
                } else {
                    return ''; // En caso de que no se encuentre el título, devolver una cadena vacía
                }
            }
        }

        // Ensure the global $wp_filesystem is available and mock its methods
        global $wp_filesystem;

        if (!isset($wp_filesystem)) {
            $wp_filesystem = $this->getMockBuilder('WP_Filesystem_Direct')
                ->setMethods(['put_contents'])
                ->getMock();

            $wp_filesystem->method('put_contents')
                ->willReturn(true);
        }
    }
}

// Mock implementation of WP_Filesystem_Direct class
if (!class_exists('WP_Filesystem_Direct')) {
    class WP_Filesystem_Direct
    {
        public function put_contents($file, $contents, $mode = false)
        {
            return true;
        }
    }
}
