<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Represents the core functionality of the Bob plugin.
 */
class Bob_Core {
    private static $instance;

    private $settings;
    
    public function __construct() {
        require_once BOB_PLUGIN_DIR . 'includes/admin/bob-settings.php';

        $this->settings = new Bob_Settings();
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
    }

    public function bob_activate() {
        // to implement	
    }

    public function bob_deactivate() {
        delete_option( 'bob_openai_api_key' );
        wp_clear_scheduled_hook( 'bob_optimizer_cron' );
    }
}