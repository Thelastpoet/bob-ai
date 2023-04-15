<?php

/**
 * Plugin Name: Bob AI
 * Description: A WordPress plugin that optimizes and updates meta descriptions using OpenAI to improve search engine visibility and boost click-through rates.
 * Version: 1.0.0
 * Author: Ammanulah Emmanuel
 * Author URI: https://nabaleka.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: bob
 *
 * @package Bob
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

// Define bob constants.
define( 'BOB_VERSION', '1.0.0' );
define( 'BOB_PLUGIN_FILE', __FILE__ );
define( 'BOB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BOB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core plugin class.
 */
require_once BOB_PLUGIN_DIR . 'bob-core.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.1.0
 */
function run_bob() {
    $bob = Bob_Core::get_instance( BOB_PLUGIN_FILE );
    $bob->run();
}

run_bob();