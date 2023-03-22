<?php
defined('ABSPATH') or die('No script kiddies please!');

require_once BOB_PLUGIN_DIR . 'includes/admin/bob-settings.php';
require_once BOB_PLUGIN_DIR . 'stats/bob-stats.php';

class Bob_Core {
    private static $instance;

    private $settings;
    private $bob_stats;
    
    public function __construct() {
        $this->settings = new Bob_Settings();
        $this->bob_stats = new Bob_Stats();
    }

    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run() {
        register_activation_hook( __FILE__, array( $this, 'bob_activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'bob_deactivate' ) );
        $this->bob_stats->bob_stats_table();
    }

    public function bob_activate() {
        // to implement later
    }

    public function bob_deactivate() {
        delete_option( 'bob_openai_api_key' );
        wp_clear_scheduled_hook( 'bob_optimizer_cron' );
    }
}