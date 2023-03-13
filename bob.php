<?php

/**
 * Plugin Name: Bob
 * Description: A WordPress plugin that optimizes and updates meta descriptions using OpenAI to improve search engine visibility and boost click-through rates.
 * Version: 1.0.0
 * Author: Ammanulah Emmanuel
 * Author URI: https://nabaleka.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: bob
 *
 * @package Bob
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define bob constants.
define( 'BOB_VERSION', '1.0.0' );
define( 'BOB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BOB_PLUGIN_URL', plugin_dir_path( __FILE__ ) );

/**
 * The core plugin class.
 */
if ( is_admin() ) {
    require_once BOB_PLUGIN_DIR . 'includes/bob_core.php';
}

/**
 * Begins execution of the plugin.
 *
 * @since 1.1.0
 */
function run_bob() {
    $bob = Bob_Core::get_instance();
    $bob->run();
}
run_bob();