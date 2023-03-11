<?php

/**
 * Represents the combined settings page for the Bob plugin.
 */

 require_once plugin_dir_path( __FILE__ ) . 'bob-seo-plugins.php';

 class Bob_Config {
	
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_bob_submenu_page' ] );
		add_action( 'admin_init', [ $this, 'bob_register_settings' ] );
	}

	/**
	 * Adds the submenu page for the Bob plugin.
	 */
	public function add_bob_submenu_page() {
		add_submenu_page(
			'options-general.php',
			'Bob Plugin Settings',
			'Bob Settings',
			'manage_options',
			'bob-settings',
			[ $this, 'render_bob_config_page' ]
		);
	}

	/**
	 * Registers the OpenAI API and SEO settings fields and sections.
	 */
	public function bob_register_settings() {
		// Register OpenAI API settings.
		register_setting( 'bob-settings-group', 'bob-openai-api-key', [ $this, 'sanitize_text_field_callback' ] );
		register_setting( 'bob-settings-group', 'bob-openai-model', [ $this, 'sanitize_text_field_callback' ] );

		add_settings_section( 'bob-openai-section', esc_html__( 'OpenAI API Key', 'bob' ), [ $this, 'render_openai_section' ], 'bob-settings' );
		add_settings_field( 'bob-openai-api-key', esc_html__( 'API Key', 'bob' ), [ $this, 'render_openai_api_key_field' ], 'bob-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-model', esc_html__( 'Model', 'bob' ), [ $this, 'render_openai_model_field' ], 'bob-settings', 'bob-openai-section' );

		// Register SEO settings.
		register_setting( 'bob-settings-group', 'bob_seo_optimizer_seo_plugin', 'sanitize_text_field' );
		
		add_settings_section( 'bob-seo-section', __( 'Bob SEO Settings', 'bob-seo-optimizer' ), [ $this, 'render_seo_settings_section' ], 'bob-settings' );
		add_settings_field( 'bob_seo_optimizer_seo_plugin', __( 'Select your preferred SEO plugin:', 'bob-seo-optimizer' ), [ $this, 'render_seo_plugin_field' ], 'bob-settings', 'bob-seo-section' );
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
		echo sprintf( '<input type="text" name="bob-openai-api-key" value="%s" />', esc_attr( $api_key ) );
	}

	/**
	 * Renders the model field.
	 */
	public function render_openai_model_field() {
		$model = get_option( 'bob-openai-model' );
		echo sprintf( '<input type="text" name="bob-openai-model" value="%s" />', esc_attr( $model ) );
	}

	/**
     * Renders the SEO settings section.
     */
    public function render_seo_settings_section() {
        echo 'Configure your SEO settings below:';
    }

    /**
     * Sanitizes the value for the "bob_seo_optimizer_seo_plugin" setting.
     *
     * @param mixed $value The value to sanitize.
     * @return string The sanitized value.
     */
    public function sanitize_seo_plugin( $value ) {
        $valid_options = array(
            'yoast_seo',
            'rank_math',
            'seopress',
            'all_in_one_seo',
            'the_seo_framework'
        );

        if ( in_array( $value, $valid_options ) ) {
            return $value;
        } else {
            return '';
        }
    }

	/**
	 * Renders the settings page.
	 */
	/**
 * Renders the settings page.
 */
public function render_bob_config_page() {
	$selected_seo_plugin = get_option( 'bob_seo_optimizer_seo_plugin', 'yoast_seo' );
	$openai_api_key = get_option( 'bob-openai-api-key' );
	$openai_model = get_option( 'bob-openai-model' );

	if ( isset( $_POST['submit'] ) ) {
		check_admin_referer( 'bob-settings-group', 'bob-settings-nonce' );

		// Save OpenAI settings
		update_option( 'bob-openai-api-key', sanitize_text_field( $_POST['bob-openai-api-key'] ) );
		update_option( 'bob-openai-model', sanitize_text_field( $_POST['bob-openai-model'] ) );

		// Save SEO settings
		update_option( 'bob_seo_optimizer_seo_plugin', sanitize_text_field( $_POST['bob_seo_optimizer_seo_plugin'] ) );
		update_option( 'bob_seo_post_type', sanitize_text_field( $_POST['bob_seo_post_type'] ) );

		echo '<div id="message" class="updated"><p>' . esc_html__( 'Settings saved.', 'bob' ) . '</p></div>';
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Bob Settings', 'bob' ); ?></h1>

		<form method="post" action="<?php echo esc_url( admin_url( 'options-general.php?page=bob-settings' ) ); ?>">
			<?php wp_nonce_field( 'bob-settings-group', 'bob-settings-nonce' ); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'API Key:', 'bob' ); ?></th>
						<td><input type="text" name="bob-openai-api-key" value="<?php echo esc_attr( $openai_api_key ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Model:', 'bob' ); ?></th>
						<td><input type="text" name="bob-openai-model" value="<?php echo esc_attr( $openai_model ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Select your preferred SEO plugin:', 'bob' ); ?></th>
						<td>
							<select name="bob_seo_optimizer_seo_plugin">
								<?php
								$options = bob_seo_optimizer_plugin_options();
								foreach ( $options as $value => $label ) {
									?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $selected_seo_plugin ); ?>><?php echo esc_html( $label ); ?></option>
									<?php
								}
								?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button( esc_html__( 'Save Settings', 'bob' ), 'primary', 'submit' ); ?>
		</form>
	</div>	
	<?php
	}	
}