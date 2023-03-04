<?php

/**
 * Represents the settings page for the Bob plugin.
 */
class Bob_Settings {
    /**
     * The instance of the Bob_Settings class.
     *
     * @var Bob_Settings
     */
    private static $instance;

    /**
     * The OpenAI settings object.
     *
     * @var Bob_OpenAI_Settings
     */
    private $openai_settings;

    /**
     * The Post Type settings object.
     *
     * @var Bob_Post_Type_Settings
     */
    private $post_type_settings;

    /**
     * The SEO settings object.
     *
     * @var Bob_SEO_Settings
     */
    private $seo_settings;

    /**
     * Creates a new instance of the Bob_Settings class.
     *
     * @param Bob_OpenAI_Settings    $openai_settings The OpenAI settings object.
     * @param Bob_Post_Type_Settings $post_type_settings The Post Type settings object.
     * @param Bob_SEO_Settings       $seo_settings The SEO settings object.
     */

    public function __construct( Bob_OpenAI_Settings $openai_settings, Bob_Post_Type_Settings $post_type_settings, Bob_SEO_Settings $seo_settings ) {
        $this->file = __FILE__;
        $this->dir = dirname( $this->file );
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
        
        $this->openai_settings    = $openai_settings;
        $this->post_type_settings = $post_type_settings;
        $this->seo_settings       = $seo_settings;

        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Gets the instance of the Bob_Settings class.
     *
     * @return Bob_Settings The instance of the Bob_Settings class.
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            $openai_settings    = new Bob_OpenAI_Settings();
            $post_type_settings = new Bob_Post_Type_Settings();
            $seo_settings       = new Bob_SEO_Settings();

            self::$instance = new self( $openai_settings, $post_type_settings, $seo_settings );
        }

        return self::$instance;
    }

    /**
     * Adds the settings pages to the WordPress admin menu.
     */
    public function add_settings_page() {
        add_menu_page(
            __( 'Bob Settings', 'bob' ),
            __( 'Bob', 'bob' ),
            'manage_options',
            'bob-settings',
            array( $this, 'render_settings_page' )
        );
    }
        
    /**
     * Registers the settings for the OpenAI and Post Type settings pages.
     */
    public function register_settings() {
        $this->openai_settings->register_settings();
        $this->post_type_settings->register_settings();
        $this->seo_settings->register_settings();
    }

    /**
     * Enqueues the plugin scripts and styles.
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'bob-scripts', esc_url($this->assets_url) . 'js/bob-admin.js', array( 'jquery' ), false, true );
    }
    
    /**
     * Renders the settings page.
     */
    public function render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Bob Settings', 'bob' ); ?></h1>
        <h2 class="nav-tab-wrapper">
            <a class="nav-tab nav-tab-active" href="#bob-settings-general" onclick="showTab('bob-settings-general')"><?php esc_html_e( 'General', 'bob' ); ?></a>
            <a class="nav-tab" href="#bob-settings-openai" onclick="showTab('bob-settings-openai')"><?php esc_html_e( 'OpenAI', 'bob' ); ?></a>
            <a class="nav-tab" href="#bob-settings-post-type" onclick="showTab('bob-settings-post-type')"><?php esc_html_e( 'Post Type', 'bob' ); ?></a>
            <a class="nav-tab" href="#bob-settings-seo" onclick="showTab('bob-settings-seo')"><?php esc_html_e( 'SEO', 'bob' ); ?></a>
        </h2>

        <div id="bob-settings-general" class="bob-settings-tab">
            <p><?php esc_html_e( 'Welcome to the Bob plugin! This plugin uses OpenAI to update the description of WordPress Taxonomies. Please use the other tabs to configure the plugin settings.', 'bob' ); ?></p>
        </div>

        <div id="bob-settings-openai" class="bob-settings-tab" style="display:none;">
            <?php $this->openai_settings->render_openai_settings_page(); ?>
        </div>

        <div id="bob-settings-post-type" class="bob-settings-tab" style="display:none;">
            <?php $this->post_type_settings->render_post_typesettings_page(); ?>
        </div>

        <div id="bob-settings-seo" class="bob-settings-tab" style="display:none;">
            <?php $this->seo_settings->render_seo_settings_page(); ?>
        </div>
    </div>
    <?php
}

}