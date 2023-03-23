<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Bob_Core {
    private static $instance;
    private $plugin_file;

    private $settings;
    private $bob_stats;
    
    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
    }

    public static function get_instance($plugin_file = '') {
        if (!isset(self::$instance)) {
            self::$instance = new self($plugin_file);
        }
        return self::$instance;
    }

    public function run() {
        require_once BOB_PLUGIN_DIR . 'includes/admin/bob-settings.php';
        require_once BOB_PLUGIN_DIR . 'stats/bob-stats.php';

        $this->settings = new Bob_Settings();
        $this->bob_stats = new Bob_Stats();

        register_activation_hook($this->plugin_file, array($this, 'bob_activate'));
        register_uninstall_hook($this->plugin_file, array('Bob_Core', 'bob_uninstall_callback'));
    }

    public function bob_activate() {
        $this->bob_stats->bob_stats_table();
    }

    public function bob_uninstall() {
        delete_option('bob_openai_api_key');
        wp_clear_scheduled_hook('bob_optimizer_cron');
        $this->bob_stats->delete_bob_stats_table();
    }

    public static function bob_uninstall_callback() {
        $instance = self::get_instance();
        $instance->bob_uninstall();
    }
}