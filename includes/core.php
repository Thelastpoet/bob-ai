<?php
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
	 * The instance of the Functions class.
	 *
	 * @var Bob_Functions
	 */
	private $functions;

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
		require_once plugin_dir_path( __FILE__ ) . 'openai.php';
		require_once plugin_dir_path( __FILE__ ) . 'functions.php';
		require_once plugin_dir_path( __FILE__ ) . 'admin/settings.php';
		require_once plugin_dir_path( __FILE__ ) . 'seo-optimization.php';

		// Initialize classes.
		$this->openai        = new Bob_OpenAI();
		$this->functions     = new Bob_Functions();
		$this->settings      = new Bob_Settings();
		$this->seo_optimizer = new Bob_SEO_Optimizer();
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