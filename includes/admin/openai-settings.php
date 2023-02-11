<?php

class Bob_OpenAI_Settings {
    private static $instance;

    public function __construct() {
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register_settings() {
        register_setting( 'bob-settings-group', 'bob-openai-api-key' );
        register_setting( 'bob-settings-group', 'bob-openai-model' );
        register_setting( 'bob-settings-group', 'bob-openai-temperature' );
        register_setting( 'bob-settings-group', 'bob-openai-top_p' );
        register_setting( 'bob-settings-group', 'bob-openai-frequency_penalty' );
        register_setting( 'bob-settings-group', 'bob-openai-presence_penalty' );

        add_settings_section( 'bob-openai-section', __( 'OpenAI API Key', 'bob' ), array( $this, 'render_openai_section' ), 'bob-settings' );
        add_settings_field( 'bob-openai-api-key', __( 'API Key', 'bob' ), array( $this, 'render_openai_api_key_field' ), 'bob-settings', 'bob-openai-section' );
        add_settings_field( 'bob-openai-model', __( 'Model', 'bob' ), array( $this, 'render_openai_model_field' ), 'bob-settings', 'bob-openai-section' );
        add_settings_field( 'bob-openai-temperature', __( 'Temperature', 'bob' ), array( $this, 'render_openai_temperature_field' ), 'bob-settings', 'bob-openai-section' );
        add_settings_field( 'bob-openai-top_p', __( 'Top P', 'bob' ), array( $this, 'render_openai_top_p_field' ), 'bob-settings', 'bob-openai-section' );
        add_settings_field( 'bob-openai-frequency_penalty', __( 'Frequency Penalty', 'bob' ), array( $this, 'render_openai_frequency_penalty_field' ), 'bob-settings', 'bob-openai-section' );
        add_settings_field( 'bob-openai-presence_penalty', __( 'Presence Penalty', 'bob' ), array( $this, 'render_openai_presence_penalty_field' ), 'bob-settings', 'bob-openai-section' );
    }

    public function render_openai_section() {
        echo '<p>' . __( 'Enter your OpenAI API Key and other settings below.', 'bob' ) . '</p>';
        }
        
        public function render_openai_api_key_field() {
        echo '<input type="text" name="bob-openai-api-key" value="' . get_option( 'bob-openai-api-key' ) . '" />';
        }
        
        public function render_openai_model_field() {
        echo '<input type="text" name="bob-openai-model" value="' . get_option( 'bob-openai-model', 'text-curie-001' ) . '" />';
        }
        
        public function render_openai_temperature_field() {
        echo '<input type="text" name="bob-openai-temperature" value="' . get_option( 'bob-openai-temperature', 0.7 ) . '" />';
        }
        
        public function render_openai_top_p_field() {
        echo '<input type="text" name="bob-openai-top-p" value="' . get_option( 'bob-openai-top-p', 1 ) . '" />';
        }
        
        public function render_openai_frequency_penalty_field() {
        echo '<input type="text" name="bob-openai-frequency-penalty" value="' . get_option( 'bob-openai-frequency-penalty', 0 ) . '" />';
        }
        
        public function render_openai_presence_penalty_field() {
        echo '<input type="text" name="bob-openai-presence-penalty" value="' . get_option( 'bob-openai-presence-penalty', 0 ) . '" />';
        }
    }
        
Bob_OpenAI_Settings::get_instance();