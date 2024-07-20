<?php
/**
 * Plugin Name:       AI Entries
 * Description:       Automates the creation of standard WordPress posts.
 * Version:           1.0.7
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Julio Bermúdez
 * Author URI:        https://github.com/berchj/
 * Plugin URI:        https://github.com/berchj/AIEntries
 * License:           GPLv2 or later.
 */

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-ai-entries.php';

register_deactivation_hook(__FILE__, ['AIEntries', 'deactivate']);

AIEntries::instance();
