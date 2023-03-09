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
		foreach ( glob( plugin_dir_path( __FILE__ ) . 'includes/*.php' ) as $file ) {
			require_once $file;
		}

		$this->openai        = new Bob_OpenAI();
		$this->seo_optimizer = new Bob_SEO_Optimizer();
		$this->settings      = new Bob_Settings(
			new Bob_OpenAI_Settings(),
			new Bob_SEO_Settings()
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