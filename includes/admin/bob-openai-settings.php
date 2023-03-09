<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Bob_OpenAI_Settings
 *
 * Handles the settings page for the OpenAI API
 */
class Bob_OpenAI_Settings {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Registers the settings fields and sections
	 */
	public function register_settings() {
		register_setting( 'bob-openai-settings-group', 'bob-openai-api-key', [ $this, 'sanitize_text_field_callback' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-model', [ $this, 'sanitize_text_field_callback' ] );

		add_settings_section( 'bob-openai-section', esc_html__( 'OpenAI API Key', 'bob' ), [ $this, 'render_openai_section' ], 'bob-openai-settings' );
		add_settings_field( 'bob-openai-api-key', esc_html__( 'API Key', 'bob' ), [ $this, 'render_openai_api_key_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-model', esc_html__( 'Model', 'bob' ), [ $this, 'render_openai_model_field' ], 'bob-openai-settings', 'bob-openai-section' );
	}

	/**
	 * Sanitize text field input
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string The sanitized value.
	 */
	public function sanitize_text_field_callback( $value ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Renders the OpenAI section.
	 */
	public function render_openai_section() {
		echo esc_html__( 'Enter your OpenAI API Key and other settings below.', 'bob' );
	}

	/**
	 * Renders the API key field.
	 */
	public function render_openai_api_key_field() {
		$api_key = get_option( 'bob-openai-api-key' );
		echo sprintf( '<input type="password" name="bob-openai-api-key" value="%s" />', esc_attr( $api_key ) );
	}

	/**
	 * Renders the model field.
	 */
	public function render_openai_model_field() {
		$model = get_option( 'bob-openai-model' );
		echo sprintf( '<input type="text" name="bob-openai-model" value="%s" />', esc_attr( $model ) );
	}

	/**
	 * Renders the OpenAI settings page.
	 */
	public function render_openai_settings_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'OpenAI Settings', 'bob' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'bob-openai-settings-group' ); ?>
				<?php do_settings_sections( 'bob-openai-settings' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}