<?php

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
		register_setting( 'bob-openai-settings-group', 'bob-openai-api-key', [ 'sanitize_callback' => 'sanitize_text_field' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-model', [ 'sanitize_callback' => 'sanitize_text_field' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-max-tokens', [ 'sanitize_callback' => 'absint' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-temperature', [ 'sanitize_callback' => 'floatval' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-top-p', [ 'sanitize_callback' => 'floatval' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-frequency-penalty', [ 'sanitize_callback' => 'absint' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-presence-penalty', [ 'sanitize_callback' => 'absint' ] );

		add_settings_section( 'bob-openai-section', __( 'OpenAI API Key', 'bob' ), [ $this, 'render_openai_section' ], 'bob-openai-settings' );
        add_settings_field( 'bob-openai-api-key', __( 'API Key', 'bob' ), [ $this, 'render_openai_api_key_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-model', __( 'Model', 'bob' ), [ $this, 'render_openai_model_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-max-tokens', __( 'Max Tokens', 'bob' ), [ $this, 'render_openai_max_tokens_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-temperature', __( 'Temperature', 'bob' ), [ $this, 'render_openai_temperature_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-top-p', __( 'Top P', 'bob' ), [ $this, 'render_openai_top_p_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-frequency-penalty', __( 'Frequency Penalty', 'bob' ), [ $this, 'render_openai_frequency_penalty_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-presence-penalty', __( 'Presence Penalty', 'bob' ), [ $this, 'render_openai_presence_penalty_field' ], 'bob-openai-settings', 'bob-openai-section' );
	}

	/**
 * Renders the OpenAI section.
 */
public function render_openai_section() {
	esc_html_e( 'Enter your OpenAI API Key and other settings below.', 'bob' );
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
 * Renders the max tokens field.
 */
public function render_openai_max_tokens_field() {
	$max_tokens = get_option( 'bob-openai-max-tokens' );
	echo sprintf( '<input type="number" name="bob-openai-max-tokens" value="%s" />', esc_attr( $max_tokens ) );
}

/**
 * Renders the temperature field.
 */
public function render_openai_temperature_field() {
	$temperature = get_option( 'bob-openai-temperature' );
	echo sprintf( '<input type="number" name="bob-openai-temperature" step="0.01" value="%s" />', esc_attr( $temperature ) );
}

/**
 * Renders the top P field.
 */
public function render_openai_top_p_field() {
	$top_p = get_option( 'bob-openai-top-p' );
	echo sprintf( '<input type="number" name="bob-openai-top-p" step="0.01" value="%s" />', esc_attr( $top_p ) );
}

/**
 * Renders the top frequency penalty field.
 */
public function render_openai_frequency_penalty_field() {
	$top_p = get_option( 'bob-openai-frequency-penalty' );
	echo sprintf( '<input type="number" name="bob-openai-frequency-penalty" step="0.01" value="%s" />', esc_attr( $top_p ) );
}

/**
 * Renders the presence penalty field.
 */
public function render_openai_presence_penalty_field() {
	$presence_penalty = get_option( 'bob-openai-presence-penalty' );
	echo sprintf( '<input type="number" name="bob-openai-presence-penalty" value="%s" />', esc_attr( $presence_penalty ) );
}

/**
 * Renders the OpenAI settings page.
 */
public function render_openai_settings_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'OpenAI Settings', 'bob' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'bob-openai-settings-group' ); ?>
			<?php do_settings_sections( 'bob-openai-settings' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

}