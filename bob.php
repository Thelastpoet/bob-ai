<?php
/**
 * Plugin Name: Bob
 * Description: A WordPress plugin that uses OpenAI to update the description of WordPress Taxonomies.
 * Version: 1.0.0
 * Author: Ammanulah Emmanuel
 * Author URI: https://nabaleka.com
 * License: GPLv2 or later
 * Text Domain: bob
 *
 * @package Bob
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The core plugin class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/core.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run_bob() {
    $bob = new Bob_Core();
    $bob->run();
}
run_bob();