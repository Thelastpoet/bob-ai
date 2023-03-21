<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Represents the settings page for the Bob plugin.
 */

class Bob_Settings {
	private $openai;

    private $seo_optimizer;
	
	public function __construct() {		
		require_once BOB_PLUGIN_DIR . 'includes/bob-openai.php';
        require_once BOB_PLUGIN_DIR . 'includes/bob-optimizer.php';

        $this->openai = new Bob_OpenAI();
        $this->seo_optimizer = new Bob_SEO_Optimizer();

		add_action( 'admin_menu', [ $this, 'bob_add_settings_page' ] );
		add_action( 'admin_init', [ $this, 'bob_register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );		
	}

	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'bob-admin-js', BOB_PLUGIN_URL . 'assets/js/bob-admin.js', array( 'jquery' ), BOB_VERSION, true );
		wp_enqueue_style( 'bob-admin-css', BOB_PLUGIN_URL . 'assets/css/bob-admin.css', array(), BOB_VERSION );
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
     * vailable SEO plugins.
     *
     * @return array Available SEO plugins.
     */
    public static function get_seo_plugin_options() {
        $options = array(
            'yoast_seo' => 'Yoast SEO',
            'rank_math' => 'Rank Math',
            'seopress' => 'SEOPress',
            'all_in_one_seo' => 'All in One SEO',
            'the_seo_framework' => 'The SEO Framework'
        );

        return $options;
    }

	/**
	 * Registers the OpenAI API and SEO settings fields and sections.
	 */
	public function bob_register_settings() {
		add_action( 'admin_notices', [ $this, 'bob_settings_saved_notice' ] );

		// Register OpenAI API settings.
		register_setting( 'bob-settings-group', 'bob-openai-api-key', [ $this, 'sanitize_text_field_callback' ] );
		register_setting( 'bob-settings-group', 'bob-openai-model', [ $this, 'sanitize_text_field_callback' ] );
	
		add_settings_section( 'bob-openai-section', esc_html__( 'OpenAI API Key', 'bob' ), [ $this, 'render_openai_section' ], 'bob-settings' );
		add_settings_field( 'bob-openai-api-key', esc_html__( 'API Key', 'bob' ), [ $this, 'render_openai_api_key_field' ], 'bob-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-model', esc_html__( 'Model', 'bob' ), [ $this, 'render_openai_model_field' ], 'bob-settings', 'bob-openai-section' );
	
		// Register SEO settings.
		register_setting( 'bob-settings-group', 'bob_seo_optimizer_seo_plugin', [ $this, 'sanitize_seo_plugin' ] );
	
		add_settings_section( 'bob-seo-section', __( 'Bob SEO Settings', 'bob-seo-optimizer' ), [ $this, 'render_seo_settings_section' ], 'bob-settings' );
		add_settings_field( 'bob_seo_optimizer_seo_plugin', __( 'Select your current SEO plugin from the list:', 'bob-seo-optimizer' ), [ $this, 'render_seo_plugin_field' ], 'bob-settings', 'bob-seo-section' );
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
		$description = sprintf( __( 'Enter your OpenAI API key. You can get one by creating an account at %s.', 'bob' ), '<a href="https://openai.com">openai.com</a>' );
		$tooltip = esc_attr__( 'Your OpenAI API key is a secret code that identifies your account and allows you to access OpenAI\'s language processing services.', 'bob' );
	
		printf( '<div class="bob-tooltip-container"><input type="password" name="bob-openai-api-key" value="%s" /><span class="bob-tooltip">%s</span><button id="bob-api-key-toggle" type="button">%s</button></div><br /><span class="description">%s</span>', esc_attr( $api_key ), esc_attr( $tooltip ), esc_html__( 'Show', 'bob' ), $description );
	}

	/**
	 * Renders the model field.
	 */
	public function render_openai_model_field() {
		$models = array(
			'text-davinci-003' => 'Davinci 003',
			'text-davinci-002' => 'Davinci 002',
			'text-curie-001' => 'Curie 001',
			'text-babbage-001' => 'Babbage 001',
			'text-ada-001' => 'Ada 001',
			'davinci' => 'Davinci',
			'curie' => 'Curie',
			'babbage' => 'Babbage',
			'ada' => 'Ada',
		);

		$selected_model = get_option('bob-openai-model', 'text-davinci-003');

		echo '<select name="bob-openai-model">';
		foreach ($models as $key => $value) {
			$selected = ($key == $selected_model) ? 'selected' : '';
			echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($value) . '</option>';
		}
		echo '</select>';
	}

	/**
     * Renders the SEO settings section.
     */
    public function render_seo_settings_section() {
        echo esc_html__( 'Configure your SEO settings below:' );
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
            return false;
        }
    }
	
	/**
	 * Renders the SEO plugin field.
	 */
	public function render_seo_plugin_field() {
		$seo_plugin_options = self::get_seo_plugin_options();
		$selected_seo_plugin = get_option('bob_seo_optimizer_seo_plugin', 'yoast_seo');

		echo '<select name="bob_seo_optimizer_seo_plugin">';
		foreach ($seo_plugin_options as $key => $value) {
			$selected = ($key == $selected_seo_plugin) ? 'selected' : '';
			echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($value) . '</option>';
		}
		echo '</select>';
	}

	public function bob_settings_saved_notice() {
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
			$timestamp = wp_next_scheduled( 'bob_optimizer_cron' );

			if ( false === $timestamp ) {
				$this->seo_optimizer->schedule_seo_update();
			}
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Settings saved successfully.', 'bob' ); ?></p>
			</div>
			<?php
		}
	}
	
	/**
	 * Renders the settings page.
	 */
	public function bob_render_settings_page() {
		// Check user capability to access the page.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
	
		settings_errors( 'bob-settings-group' );
		?>
		<div class="wrap bob-settings-wrap">
			<h1><?php esc_html_e( 'Bob Settings', 'bob' ); ?></h1>
	
			<form method="post" action="options.php">
				<?php settings_fields( 'bob-settings-group' ); ?>
				<?php do_settings_sections( 'bob-settings' ); ?>
				<?php wp_nonce_field( 'bob-settings-group', 'bob-settings-nonce' ); ?>
				<?php submit_button( __( 'Save Settings', 'bob' ), 'primary', 'submit', ); ?>
			</form>
		</div>
		<?php
	}
}