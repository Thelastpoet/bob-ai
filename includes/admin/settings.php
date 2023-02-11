<?php

class Bob_Settings {
    private static $instance;
    private $openai_settings;

    public function __construct() {
        require_once plugin_dir_path( __FILE__ ) . 'openai-settings.php';
        require_once plugin_dir_path( __FILE__ ) . 'post-type-settings.php';
        
        // Add plugin settings page
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // Initialize OpenAI settings
        $this->openai_settings = new Bob_OpenAI_Settings();
        // Initialize Post Type settings
        $this->post_type_settings = new Bob_Post_Type_Settings();
    }
    
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add_settings_page() {
        add_menu_page(
            __( 'Bob Settings', 'bob' ),
            __( 'Bob', 'bob' ),
            'manage_options',
            'bob-settings',
            array( $this, 'render_settings_page' )
        );
        
        add_submenu_page(
            'bob-settings',
            __( 'OpenAI Settings', 'bob' ),
            __( 'OpenAI', 'bob' ),
            'manage_options',
            'bob-openai-settings',
            array( $this->openai_settings, 'render_openai_settings_page' )
        );

        add_submenu_page(
            'bob-settings',
            __( 'Post Type Settings', 'bob' ),
            __( 'Post Type', 'bob' ),
            'manage_options',
            'bob-post-type-settings',
            array( $this->post_type_settings, 'render_post_typesettings_page' )
        );
    }

    public function register_settings() {
        $this->openai_settings->register_settings();
        $this->post_type_settings->register_settings();
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Bob Settings', 'bob' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'bob-settings-group' ); ?>
                <?php do_settings_sections( 'bob-settings' ); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}