<?php
/**
 * Plugin Name:       IA Entries
 * Description:       Automates the creation of standard WordPress posts, using Google Gemini artificial intelligence (SEO-friendly titles and content) and AI stability (images associated with posts).
 * Version:           1.0.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            GPLv2 or later.
 * Author URI:        https://github.com/berchj/
 * Plugin URI:        https://github.com/berchj/AIEntries
 * License:           MIT
 */

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-ai-entries.php';

register_activation_hook(__FILE__, ['AIEntries', 'activate']);
register_deactivation_hook(__FILE__, ['AIEntries', 'deactivate']);

AIEntries::instance();
