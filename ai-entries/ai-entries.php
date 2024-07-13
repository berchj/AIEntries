<?php
/**
 * Plugin Name:       IA Entries
 * Description:       Automates the creation of WordPress site entries based on an AI API call to Google's GEMINI
 * Version:           1.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Julio Bermúdez
 * Author URI:        https://github.com/berchj/
 * Plugin URI:        https://github.com/berchj/AIEntries
 * License:           MIT
 */

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-ai-entries.php';

register_activation_hook(__FILE__, ['AIEntries', 'activate']);
register_deactivation_hook(__FILE__, ['AIEntries', 'deactivate']);

AIEntries::instance();
