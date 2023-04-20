<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Bob_Settings {
	private $openai;
    private $seo_optimizer;
	private $bob_stats;
	
	public function __construct() {	
		require_once BOB_PLUGIN_DIR . 'stats/bob-stats.php';	
		require_once BOB_PLUGIN_DIR . 'includes/bob-openai.php';
        require_once BOB_PLUGIN_DIR . 'includes/bob-optimizer.php';
		
        $this->openai = new OpenAIGenerator();
		$this->bob_stats = new Bob_Stats();
        $this->seo_optimizer = new Bob_SEO_Optimizer();

		add_action( 'admin_menu', [ $this, 'bob_add_settings_page' ] );
		add_action( 'admin_init', [ $this, 'bob_register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );	
		
		add_action('wp_ajax_start_bob_ai', [$this, 'ajax_start_bob_ai']);
    	add_action('wp_ajax_stop_bob_ai', [$this, 'ajax_stop_bob_ai']);
	}

	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'bob-admin', BOB_PLUGIN_URL . 'assets/js/bob-admin.js', array( 'jquery' ), BOB_VERSION, true );
		wp_enqueue_script( 'bob-general', BOB_PLUGIN_URL . 'assets/js/bob-general.js', array( 'jquery' ), BOB_VERSION, true );
		wp_enqueue_style( 'bob-admin', BOB_PLUGIN_URL . 'assets/css/bob-admin.css', array(), BOB_VERSION );

		$bob_ai_status = get_option( 'bob_ai_status', 'stopped' );
		$js_data = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'bobAiStatus' => $bob_ai_status
		);

		wp_localize_script( 'bob-general', 'bobData', $js_data );
	}

	public function ajax_start_bob_ai() {
		check_ajax_referer('bob_meta_generation_nonce');
	
		if (!wp_next_scheduled('bob_optimizer_cron')) {
			$this->seo_optimizer->schedule_seo_update();
			update_option('bob_ai_status', 'running');
		}
	
		wp_send_json_success();
	}
	
	public function ajax_stop_bob_ai() {
		check_ajax_referer('bob_meta_generation_nonce');
	
		wp_clear_scheduled_hook('bob_optimizer_cron');
		update_option('bob_ai_status', 'stopped');
	
		wp_send_json_success();
	}

	public function bob_add_settings_page() {
		add_menu_page(
			__( 'Bob Settings', 'bob-ai' ),
			__( 'bob-ai', 'bob-ai' ),
			'manage_options',
			'bob-settings',
			[ $this, 'bob_render_settings_page' ]
		);

		add_submenu_page(
			'bob-settings',
			__('Bob Stats', 'bob-ai'),
			__('Bob Stats', 'bob-ai'),
			'manage_options',
			'bob-stats',
			[$this->bob_stats, 'bob_render_stats_page']
		);
	}

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
	public function bob_register_settings() {
		add_action( 'admin_notices', [ $this, 'bob_settings_saved_notice' ] );

		// Register OpenAI API settings.
		register_setting( 'bob-openai-settings-group', 'bob-openai-api-key', [ $this, 'sanitize_text_field_callback' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-model', [ $this, 'sanitize_text_field_callback' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-max-tokens', [ $this, 'sanitize_integer_field_callback' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-temperature', [ $this, 'sanitize_float_field_callback' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-top-p', [ $this, 'sanitize_float_field_callback' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-frequency-penalty', [ $this, 'sanitize_float_field_callback' ] );
		register_setting( 'bob-openai-settings-group', 'bob-openai-presence-penalty', [ $this, 'sanitize_float_field_callback' ] );
	
		add_settings_section( 'bob-openai-section', esc_html__( 'OpenAI API Key', 'bob-ai' ), [ $this, 'render_openai_section' ], 'bob-openai-settings' );
		add_settings_field( 'bob-openai-api-key', esc_html__( 'API Key', 'bob-ai' ), [ $this, 'render_openai_api_key_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-model', esc_html__( 'Model', 'bob-ai' ), [ $this, 'render_openai_model_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-max-tokens', esc_html__( 'Max Tokens', 'bob-ai' ), [ $this, 'render_openai_max_tokens_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-temperature', esc_html__( 'Temperature', 'bob-ai' ), [ $this, 'render_openai_temperature_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-top-p', esc_html__( 'Top P', 'bob-ai' ), [ $this, 'render_openai_top_p_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-frequency-penalty', esc_html__( 'Frequency Penalty', 'bob-ai' ), [ $this, 'render_openai_frequency_penalty_field' ], 'bob-openai-settings', 'bob-openai-section' );
		add_settings_field( 'bob-openai-presence-penalty', esc_html__( 'Presence Penalty', 'bob-ai' ), [ $this, 'render_openai_presence_penalty_field' ], 'bob-openai-settings', 'bob-openai-section' );

		// Register SEO settings.
		register_setting( 'bob-seo-settings-group', 'bob_seo_optimizer_seo_plugin', [ $this, 'sanitize_seo_plugin' ] );
		register_setting( 'bob-seo-settings-group', 'bob_seo_optimizer_posts_per_batch', [ $this, 'sanitize_integer_field_callback' ] );
		register_setting( 'bob-seo-settings-group', 'bob_seo_optimizer_previous_mod_date', [ $this, 'sanitize_integer_field_callback' ] );
		register_setting( 'bob-seo-settings-group', 'bob_seo_optimizer_post_type', [ $this, 'sanitize_text_field_callback' ] );
		register_setting( 'bob-seo-settings-group', 'bob_seo_optimizer_order', [ $this, 'sanitize_text_field_callback' ] );
		register_setting( 'bob-seo-settings-group', 'bob_seo_optimizer_meta_max_length', [ $this, 'sanitize_integer_field_callback' ] );
	
		add_settings_section( 'bob-seo-section', esc_html__( 'Bob SEO Settings', 'bob-ai' ), [ $this, 'render_seo_settings_section' ], 'bob-seo-settings' );
		add_settings_field( 'bob_seo_optimizer_seo_plugin', esc_html__( 'Select your current SEO plugin from the list:', 'bob-ai' ), [ $this, 'render_seo_plugin_field' ], 'bob-seo-settings', 'bob-seo-section' );
		add_settings_field( 'bob_seo_optimizer_posts_per_batch', esc_html__( 'Enter the number of posts to process per batch:', 'bob-ai' ), [ $this, 'render_posts_per_batch_field' ], 'bob-seo-settings', 'bob-seo-section' );
		add_settings_field( 'bob_seo_optimizer_previous_mod_date', esc_html__( 'Enter the number of days since the post was last modified to be eligible for optimization:', 'bob-ai' ), [ $this, 'render_previous_mod_date_field' ], 'bob-seo-settings', 'bob-seo-section' );
		add_settings_field( 'bob_seo_optimizer_post_type', esc_html__( 'Select the post type to optimize:', 'bob-ai' ), [ $this, 'render_post_type_field' ], 'bob-seo-settings', 'bob-seo-section' );
		add_settings_field( 'bob_seo_optimizer_order', esc_html__( 'Select the order of posts to optimize:', 'bob-ai' ), [ $this, 'render_order_field' ], 'bob-seo-settings', 'bob-seo-section' );
		add_settings_field( 'bob_seo_optimizer_meta_max_length', esc_html__( 'Enter the maximum length for meta tags:', 'bob-ai' ), [ $this, 'render_meta_max_length_field' ], 'bob-seo-settings', 'bob-seo-section' );
	}
	public function sanitize_text_field_callback( $value ) {
		return sanitize_text_field( $value );
	}

	public function sanitize_integer_field_callback( $value ) {
		return intval( $value );
	}
	
	public function sanitize_float_field_callback( $value ) {
		return floatval( $value );
	}

	public function render_openai_section() {
		echo esc_html__( 'Enter your OpenAI API Key and choose a Model to get started.', 'bob-ai' );
	}

	public function render_openai_api_key_field() {
		$api_key = get_option( 'bob-openai-api-key' );
		$description = sprintf( __( 'Enter your OpenAI API key. You can get one by creating an account at %s.', 'bob-ai' ), '<a href="https://beta.openai.com/signup/" target="_blank">openai.com</a>' );
		$tooltip = esc_attr__( 'Your OpenAI API key is a secret code that identifies your account and allows you to access OpenAI\'s language processing services.', 'bob-ai' );
	
		printf( '<div class="bob-tooltip-container"><input type="password" name="bob-openai-api-key" value="%s" autocomplete="off" /><span class="bob-tooltip">%s</span><button id="bob-api-key-toggle" type="button">%s</button></div><br /><span class="description">%s</span>', esc_attr( $api_key ), esc_attr( $tooltip ), esc_html__( 'Show', 'bob-ai' ), $description );
	}

	public function render_openai_model_field() {
		$models = array(
			'gpt-4'             => 'GPT-4',
			'gpt-3.5-turbo'    => 'GPT-3.5 Turbo',
			'text-davinci-003' => 'Davinci 003',
			'text-babbage-001' => 'Babbage 001',
			'text-ada-001' => 'Ada 001',
		);
	
		$selected_model = get_option('bob-openai-model', 'text-davinci-003');
	
		echo '<select name="bob-openai-model" id="bob-openai-model">';
		foreach ($models as $key => $value) {
			$selected = ($key == $selected_model) ? 'selected' : '';
			echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($value) . '</option>';
		}
		echo '</select>';
	}	

	public function render_openai_max_tokens_field() {
		$max_tokens = get_option('bob-openai-max-tokens', 37);
		$description = sprintf(__('Set the Max token value between 37 and 39 for optimal results. Note that the generated description may be longer than the recommended length, but it is still acceptable.', 'bob-ai'));
		printf('<input type="number" name="bob-openai-max-tokens" value="%d" min="1" /><br /><span class="description bob-admin-desc">%s</span>', $max_tokens, $description);
	}
	
	public function render_openai_temperature_field() {
		$temperature = get_option('bob-openai-temperature', 0.7);
		$description = sprintf(__('The temperature value determines the randomness of the output. A higher value (e.g., 1.0) makes the output more random, while a lower value (e.g., 0.1) makes it more focused and deterministic.', 'bob-ai'));
		printf('<input type="number" name="bob-openai-temperature" value="%.2f" min="0.0" step="0.01" /><br /><span class="description bob-admin-desc">%s</span>', $temperature, $description);
	}
	
	public function render_openai_top_p_field() {
		$top_p = get_option('bob-openai-top-p', 1.0);
		$description = sprintf(__('The top_p value is used for nucleus sampling. It selects the highest probability tokens whose cumulative probability mass is equal to or below the given value. This provides a dynamic balance between diversity and focus in the output.', 'bob-ai'));
		printf('<input type="number" name="bob-openai-top-p" value="%.2f" min="0.0" step="0.01" /><br /><span class="description bob-admin-desc">%s</span>', $top_p, $description);
	}
	
	public function render_openai_frequency_penalty_field() {
		$frequency_penalty = get_option('bob-openai-frequency-penalty', 0.0);
		$description = sprintf(__('The frequency penalty adjusts the likelihood of tokens based on their frequency in the training data. Positive values make rare tokens more likely, while negative values make common tokens more likely.', 'bob-ai'));
		printf('<input type="number" name="bob-openai-frequency-penalty" value="%.2f" min="-2.0" max="2.0" step="0.01" /><br /><span class="description bob-admin-desc">%s</span>', $frequency_penalty, $description);
	}
	
	public function render_openai_presence_penalty_field() {
		$presence_penalty = get_option('bob-openai-presence-penalty', 0.0);
		$description = sprintf(__('The presence penalty adjusts the likelihood of tokens based on their presence in the generated text so far. Positive values make tokens less likely to be repeated, while negative values make tokens more likely to be repeated.', 'bob-ai'));
		printf('<input type="number" name="bob-openai-presence-penalty" value="%.2f" min="-2.0" max="2.0" step="0.01" /><br /><span class="description bob-admin-desc">%s</span>', $presence_penalty, $description);
	}

    public function render_seo_settings_section() {
        echo esc_html__( 'Configure your SEO settings below:' );
    }
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
            return 'yoast_seo';
        }
    }

	public function render_seo_plugin_field() {
		$seo_plugin_options = self::get_seo_plugin_options();
		$selected_seo_plugin = get_option('bob_seo_optimizer_seo_plugin', 'yoast_seo');

		echo '<select name="bob_seo_optimizer_seo_plugin" id="bob-seo-optimizer-seo-plugin">';
		foreach ($seo_plugin_options as $key => $value) {
			$selected = ($key == $selected_seo_plugin) ? 'selected' : '';
			echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($value) . '</option>';
		}
		echo '</select>';
	}

	public function bob_settings_saved_notice() {
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
			return true;
		}
		return false;
	}	

	public function render_posts_per_batch_field() {
		$posts_per_batch = get_option( 'bob_seo_optimizer_posts_per_batch', 1 );
		printf( '<input type="number" name="bob_seo_optimizer_posts_per_batch" value="%d" min="1" />', $posts_per_batch );
	}
	
	public function render_previous_mod_date_field() {
		$previous_mod_days = get_option( 'bob_seo_optimizer_previous_mod_date', 30 );
		printf( '<input type="number" name="bob_seo_optimizer_previous_mod_date" id="bob_seo_optimizer_previous_mod_date" value="%d" min="0" />', $previous_mod_days );
	}
	
	public function render_post_type_field() {
		$post_type = get_option( 'bob_seo_optimizer_post_type', 'post' );
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		$excluded_types = [ 'attachment', 'customize_changeset', 'custom_css', 'oembed_cache' ];
	
		echo '<select name="bob_seo_optimizer_post_type" id="bob-seo-optimizer-post-type">';
		foreach ( $post_types as $key => $value ) {
			if ( in_array( $key, $excluded_types ) ) {
				continue;
			}
			$selected = ( $key == $post_type ) ? 'selected' : '';
			echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $value->labels->name ) . '</option>';
		}
		echo '</select>';
	}
	
	public function render_order_field() {
		$order = get_option( 'bob_seo_optimizer_order', 'asc' );
		$order_options = [
			'asc' => __( 'Oldest first', 'bob-ai' ),
			'desc' => __( 'Newest first', 'bob-ai' ),
		];
	
		echo '<select name="bob_seo_optimizer_order" id="bob-seo-optimizer-order">';
		foreach ( $order_options as $key => $value ) {
			$selected = ( $key == $order ) ? 'selected' : '';
			echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $value ) . '</option>';
		}
		echo '</select>';
	}
	
	public function render_meta_max_length_field() {
		$meta_max_length = get_option( 'bob_seo_optimizer_meta_max_length', 160 );
		printf( '<input type="number" name="bob_seo_optimizer_meta_max_length" value="%d" min="1" />', $meta_max_length );
	}

	public function bob_render_settings_page() {
		// Check user capability to access the page.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
	
		?>
		<div class="wrap bob-settings-wrap">
			<h2><?php _e( 'Bob Settings', 'bob-ai' ); ?></h2>
			<div class="bob-tabs-container">
				<h2 class="nav-tab-wrapper">
					<a href="#general_settings_section" class="nav-tab"><?php _e( 'General', 'bob-ai' ); ?></a>
					<a href="#openai_settings_section" class="nav-tab"><?php _e( 'OpenAI', 'bob-ai' ); ?></a>
					<a href="#seo_settings_section" class="nav-tab"><?php _e( 'SEO', 'bob-ai' ); ?></a>
					<a href="#help_documents_section" class="nav-tab"><?php _e( 'Documentation', 'bob-ai' ); ?></a>
				</h2>
				<div class="bob-settings-content">
					<div id="general_settings_section" class="bob-settings-tab">
						<?php include BOB_PLUGIN_DIR . 'admin/bob-general.php'; ?>
					</div>
							<div id="openai_settings_section" class="bob-settings-tab">
								<form method="post" action="options.php">
									<?php 
									settings_fields( 'bob-openai-settings-group' );                          
									do_settings_sections( 'bob-openai-settings', 'bob-openai-section' );
									?>
									<input type="submit" name="submit" id="submit-openai" class="button button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>">
								</form>								
							</div>
							<div id="seo_settings_section" class="bob-settings-tab">
								<form method="post" action="options.php">
									<?php 
									settings_fields( 'bob-seo-settings-group' );                          
									do_settings_sections( 'bob-seo-settings', 'bob-seo-section' );
									?>
									<input type="submit" name="submit" id="submit-seo" class="button button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>">
								</form>
							</div>						
					<div id="help_documents_section" class="bob-settings-tab">
						<h2><?php _e('Documentation', 'bob-ai'); ?></h2>
						<?php include BOB_PLUGIN_DIR . 'admin/bob-doc.php'; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}	
}