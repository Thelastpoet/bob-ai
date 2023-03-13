<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Bob_Core {
    private static $instance;

    private $openai;
    private $settings;
    private $seo_optimizer;

    public function __construct() {
        require_once BOB_PLUGIN_DIR . 'includes/bob-openai.php';
        require_once BOB_PLUGIN_DIR . 'includes/bob-optimizer.php';
        require_once BOB_PLUGIN_DIR . 'includes/admin/bob-settings.php';

        $this->openai = new Bob_OpenAI();
        $this->seo_optimizer = new Bob_SEO_Optimizer();
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
        wp_redirect( admin_url( 'admin.php?page=bob-settings' ) );
        exit;
    }

    public function bob_deactivate() {
        delete_option( 'bob_openai_api_key' );
        wp_clear_scheduled_hook( 'bob_seo_optimizer' );
    }
}