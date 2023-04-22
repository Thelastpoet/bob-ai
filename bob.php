<?php

/**
 * Plugin Name: Bob AI
 * Description: Update your WordPress meta descriptions and improve your On-Page SEO with Bob AI..
 * Author: Ammanulah Emmanuel
 * Author URI: https://nabaleka.com
 * Version: 1.0.1
 * Requires at least: 5.9
 * Requires PHP: 7.2.5
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.html
 * 
 * Text Domain: bob-ai
 * 
 * @package Bob-AI
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'BOB_VERSION', '1.0.1' );
define( 'BOB_PLUGIN_FILE', __FILE__ );
define( 'BOB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BOB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core bob file.
 */
require_once BOB_PLUGIN_DIR . 'bob-core.php';

/**
 * Begin bob execution.
 *
 * @since 1.0.0
 */
function run_bob() {
    $bob = Bob_Core::get_instance( BOB_PLUGIN_FILE );
    $bob->run();
}

run_bob();