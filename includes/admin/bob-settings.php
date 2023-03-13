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
		add_action( 'admin_notices', array( $this, 'show_settings_notice' ) );
        add_action( 'admin_init', array( $this, 'clear_settings_notice' ) );
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
     * Returns an array of available SEO plugins.
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
		// Register OpenAI API settings.
		register_setting( 'bob-settings-group', 'bob-openai-api-key', [ $this, 'sanitize_text_field_callback' ] );
		register_setting( 'bob-settings-group', 'bob-openai-model', [ $this, 'sanitize_text_field_callback' ] );
	
		add_settings_section( 'bob-openai-section', esc_html__( 'OpenAI API Key', 'bob' ), [ $this, 'render_openai_section' ], 'bob-settings' );
		add_settings_field( 'bob-openai-api-key', esc_html__( 'API Key', 'bob' ), [ $this, 'render_openai_api_key_field' ], 'bob-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-model', esc_html__( 'Model', 'bob' ), [ $this, 'render_openai_model_field' ], 'bob-settings', 'bob-openai-section' );
	
		// Register SEO settings.
		register_setting( 'bob-settings-group', 'bob_seo_optimizer_seo_plugin', [ $this, 'sanitize_seo_plugin' ] );
	
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
		$description = __( 'Enter your OpenAI API key. You can get one by creating an account at <a href="https://openai.com">openai.com</a>.', 'bob' );
		$tooltip = __( 'Your OpenAI API key is a secret code that identifies your account and allows you to access OpenAI\'s language processing services.', 'bob' );
	
		printf( '<input type="text" name="bob-openai-api-key" value="%s" title="%s" /><br /><span class="description">%s</span>', esc_attr( $api_key ), esc_attr( $tooltip ), $description );
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
	public function bob_render_settings_page() {
		// Check user capability to access the page.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}	

		// Check if settings have been saved
		$settings_saved = get_option( 'bob_settings_saved' );

		if (isset($_POST['submit'])) {
			check_admin_referer('bob-settings-group', 'bob-settings-nonce');

			// Save OpenAI settings
			$api_key = isset($_POST['bob-openai-api-key']) ? sanitize_text_field($_POST['bob-openai-api-key']) : '';
			update_option('bob-openai-api-key', $api_key);

			$model = isset($_POST['bob-openai-model']) ? sanitize_text_field($_POST['bob-openai-model']) : '';
			update_option('bob-openai-model', $model);

			// Save SEO settings
			$seo_plugin = isset($_POST['bob_seo_optimizer_seo_plugin']) ? sanitize_text_field($_POST['bob_seo_optimizer_seo_plugin']) : '';
			update_option('bob_seo_optimizer_seo_plugin', $seo_plugin);

			// Schedule the SEO update
			$this->seo_optimizer->update_seo_data_daily();

			// Display a success message
			add_settings_error('bob-settings-group', 'bob-settings-saved', __('Settings saved.', 'bob'), 'updated');

		}

		settings_errors('bob-settings-group');

		// Set variables for the template
		$seo_plugin_options = self::get_seo_plugin_options();
		$selected_seo_plugin = get_option('bob_seo_optimizer_seo_plugin', 'yoast_seo');
		$openai_api_key = get_option('bob-openai-api-key');
		$openai_model = get_option('bob-openai-model');

		// Load the template file
		ob_start();
		include BOB_PLUGIN_DIR . 'includes/templates/bob-config-page.php';
		echo ob_get_clean();
	}

    /**
     * Shows a notice if the settings have not been saved.
     */
    public function show_settings_notice() {
        if ( ! $this->settings_saved && ! get_transient( 'bob_seo_notice_dismissed' ) ) {
            echo '<div class="notice notice-warning is-dismissible"><p>';
            printf( __( 'Please set your OpenAI API key and <a href="%s">save settings</a> to enable automatic Meta Description optimization.', 'bob-seo-optimizer' ), esc_url( admin_url( 'admin.php?page=bob-settings' ) ) );
            echo '&nbsp;<a href="' . esc_url( add_query_arg( 'bob_seo_notice_dismissed', '1' ) ) . '">' . __( 'Dismiss', 'bob-seo-optimizer' ) . '</a>';
            echo '</p></div>';
        }
    }

    /**
     * Clears the notice when the settings page is loaded.
     */
    public function clear_settings_notice() {
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'bob-settings' && isset( $_GET['bob_seo_notice_dismissed'] ) && $_GET['bob_seo_notice_dismissed'] === '1' ) {
            set_transient( 'bob_seo_notice_dismissed', true );
        }
    }

}