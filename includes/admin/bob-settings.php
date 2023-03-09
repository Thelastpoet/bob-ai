<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Represents the settings page for the Bob plugin.
 */
class Bob_Settings {

	/**
	 * The instance of the OpenAI settings class.
	 *
	 * @var Bob_OpenAI_Settings
	 */
	private $openai_settings;

	/**
	 * Creates a new instance of the Bob_Settings class.
	 *
	 * @param Bob_OpenAI_Settings $openai_settings The OpenAI settings object.
	 */
	public function __construct( Bob_OpenAI_Settings $openai_settings ) {
		$this->openai_settings = $openai_settings;

		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
		add_action( 'admin_init', [ $this->openai_settings, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
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
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Enqueues the plugin scripts and styles.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'bob-scripts', plugins_url( '../assets/js/bob-admin.js', __FILE__ ), [ 'jquery' ], false, true );
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
			</h2>

			<div id="bob-settings-general" class="bob-settings-tab">
				<p><?php esc_html_e( 'Welcome to the Bob plugin! This plugin uses OpenAI to update the description of WordPress Taxonomies. Please use the other tabs to configure the plugin settings.', 'bob' ); ?></p>
			</div>

			<div id="bob-settings-openai" class="bob-settings-tab" style="display:none;">
				<?php $this->openai_settings->render_openai_settings_page(); ?>
			</div>
		</div>
		<?php
	}

}