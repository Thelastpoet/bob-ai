<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The core functionality of the plugin.
 *
 * @package Bob
 */

class Bob_Core {
	/**
	 * The single instance of the class.
	 *
	 * @var Bob_Core
	 */
	private static $instance;

	/**
	 * The instance of the OpenAI class.
	 *
	 * @var Bob_OpenAI
	 */
	private $openai;

	/**
	 * The instance of the Settings class.
	 *
	 * @var Bob_Settings
	 */
	private $settings;

	/**
	 * The instance of the SEO Optimizer class.
	 *
	 * @var Bob_SEO_Optimizer
	 */
	private $seo_optimizer;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		// Include required files.
		foreach ( glob( plugin_dir_path( __FILE__ ) . 'includes/*.php' ) as $file ) {
			require_once $file;
		}

		// Initialize classes.
		$this->openai        = new Bob_OpenAI();
		$this->seo_optimizer = new Bob_SEO_Optimizer();
		$this->settings      = new Bob_Settings(
			new Bob_OpenAI_Settings(),
			new Bob_Post_Type_Settings(),
			new Bob_SEO_Settings()
		);
	}

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Bob_Core An instance of the class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Run the plugin.
	 */
	public function run() {
		// Add plugin functionality here.
	}
}