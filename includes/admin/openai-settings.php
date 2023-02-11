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

	public function render_openai_section() {
		echo '<p>' . __( 'Enter your OpenAI API Key and other settings below.', 'bob' ) . '</p>';
	}

	public function render_openai_api_key_field() {
		$api_key = get_option( 'bob-openai-api-key' );
		echo '<input type="text" name="bob-openai-api-key" value="' . esc_attr( $api_key ) . '" />';
	}

	public function render_openai_model_field() {
		$model = get_option( 'bob-openai-model' );
		echo '<input type="text" name="bob-openai-model" value="' . esc_attr( $model ) . '" />';
	}

	public function render_openai_max_tokens_field() {
        $max_tokens = get_option( 'bob-openai-max-tokens' );
		echo '<input type="number" name="bob-openai-max-tokens" value="' . esc_attr( $max_tokens ) . '" />';
	}

	public function render_openai_temperature_field() {
		$temperature = get_option( 'bob-openai-temperature' );
		echo '<input type="number" name="bob-openai-temperature" step="0.01" value="' . esc_attr( $temperature ) . '" />';
	}

	public function render_openai_top_p_field() {
		$top_p = get_option( 'bob-openai-top-p' );
		echo '<input type="number" name="bob-openai-top-p" step="0.01" value="' . esc_attr( $top_p ) . '" />';
	}

	public function render_openai_frequency_penalty_field() {
		$frequency_penalty = get_option( 'bob-openai-frequency-penalty' );
		echo '<input type="number" name="bob-openai-frequency-penalty" value="' . esc_attr( $frequency_penalty ) . '" />';
	}

	public function render_openai_presence_penalty_field() {
		$presence_penalty = get_option( 'bob-openai-presence-penalty' );
		echo '<input type="number" name="bob-openai-presence-penalty" value="' . esc_attr( $presence_penalty ) . '" />';
	}

	/**
	 * Renders the options page
	 */
	public function render_openai_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'OpenAI Settings', 'bob' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'bob-openai-settings-group' ); ?>
                <?php do_settings_sections( 'bob-openai-settings' ); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}