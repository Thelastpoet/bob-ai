<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path( __FILE__ ) . 'bob-config.php';

/**
 * Represents the settings page for the Bob plugin.
 */
class Bob_Settings {

	/**
	 * The instance of the Bob_Config class.
	 *
	 * @var Bob_Config
	 */
	private $bob_config;

	/**
	 * Creates a new instance of the Bob_Settings class.
	 *
	 * @param Bob_Config $bob_config The Bob Config object.
	 */
	public function __construct( Bob_Config $bob_config ) {
        $this->file = __FILE__;
        $this->dir = dirname( $this->file );
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '../../assets/', $this->file ) ) );
    
        $this->bob_config = $bob_config;
    
        add_action( 'admin_menu', [ $this, 'bob_add_settings_page' ] );
        add_action( 'admin_init', [ $this->bob_config, 'bob_register_settings' ] );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }    

	/**
	 * Adds the settings pages to the WordPress admin menu.
	 */
	public function bob_add_settings_page() {
		add_menu_page(
			__( 'Bob Settings', 'bob' ),
			__( 'Bob', 'bob' ),
			'manage_options',
			'bob-settings',
			[ $this, 'bob_render_settings_page' ]
		);
	}

	/**
	 * Enqueues the plugin scripts and styles.
	 */
	public function enqueue_scripts() {
        wp_enqueue_script( 'bob-scripts', esc_url($this->assets_url) . 'js/bob-admin.js', array( 'jquery' ), false, true );
		wp_enqueue_style( 'bob-styles', esc_url($this->assets_url) . 'css/bob-admin.css' );
    }
    

	/**
	 * Renders the settings page.
	 */
	public function bob_render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Bob Settings', 'bob' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" href="#bob-settings-general" onclick="showTab('bob-settings-general')"><?php _e( 'General', 'bob' ); ?></a>
				<a class="nav-tab" href="#bob-settings-config" onclick="showTab('bob-settings-config')"><?php _e( 'Bob Config', 'bob' ); ?></a>
			</h2>
			<div id="bob-settings-general" class="bob-settings-tab">
				<p><?php _e( 'Welcome to the Bob plugin!', 'bob' ); ?></p>
			</div>
			<div id="bob-settings-config" class="bob-settings-tab" style="display:none;">
				<?php $this->bob_config->render_bob_config_page(); ?>
			</div>
		</div>
		<?php
	}
}