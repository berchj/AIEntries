<?php

class AIEntries
{

    private static $instance = null;

    private function __construct()
    {
        $this->includes();
        $this->init_hooks();
    }

    public static function createInstance()
    {
        return new self();
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function includes()
    {
        require_once plugin_dir_path(__FILE__) . 'class-ai-entries-settings.php';
        require_once plugin_dir_path(__FILE__) . 'class-ai-entries-api.php';
        
    }

    private function init_hooks()
    {
        add_action('admin_menu', ['AIEntries_Settings', 'add_menu_page']);
               
    }

    
}
