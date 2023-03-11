<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Bob_Core {
	private static $instance;

	private $openai;
	private $settings;
	private $seo_optimizer;

	public function __construct() {
		
		require_once plugin_dir_path( __FILE__ ) . 'bob-openai.php';
		require_once plugin_dir_path( __FILE__ ) . 'bob-optimizer.php';
		require_once plugin_dir_path( __FILE__ ) . '/admin/bob-config.php';
		require_once plugin_dir_path( __FILE__ ) . '/admin/bob-settings.php';		
		
		$this->openai        = new Bob_OpenAI();
		$this->seo_optimizer = new Bob_SEO_Optimizer();
		$this->settings      = new Bob_Settings(
			new Bob_Config()
			
		);
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function run() {
		// Add plugin functionality here.
	}
}