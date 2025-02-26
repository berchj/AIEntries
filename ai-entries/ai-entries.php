<?php
/**
 * Plugin Name:       AI Entries
 * Description:       Automates the creation of standard WordPress posts.
 * Version:           1.0.7
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            berchj
 * Author URI:        https://github.com/berchj/
 * Plugin URI:        https://github.com/berchj/AIEntries
 * License:           GPLv2 or later.
 */

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-ai-entries.php';

register_deactivation_hook(__FILE__, ['AIEntries', 'deactivate']);

function ai_entries_enqueue_scripts() {
    wp_enqueue_script('ai-entries-ajax', plugin_dir_url(__FILE__) . 'includes/js/ai-entries-ajax.js', array('jquery'), null, true);
}

add_action('admin_enqueue_scripts', 'ai_entries_enqueue_scripts');

AIEntries::instance();
