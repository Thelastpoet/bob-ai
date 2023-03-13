<?php
/**
 * Plugin Name: Bob
 * Plugin URI: https://nabaleka.com
 * Description: A WordPress plugin that optimizes and updates meta descriptions using OpenAI to improve search engine visibility and boost click-through rates.
 * Version: BOB_VERSION
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

/**
 * Deletes all plugin data on deactivation.
 *
 * @since 1.0.0
 */
function bob_deactivate() {
    // Delete any options added by the plugin
    delete_option( 'bob_openai_api_key' );
    wp_clear_scheduled_hook( 'bob_seo_optimizer' );
}
register_deactivation_hook( __FILE__, 'bob_deactivate' );

/**
 * Schedules the SEO optimization event on activation.
 *
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'bob_schedule_seo_update' );

function bob_schedule_seo_update() {
    $optimizer = new Bob_SEO_Optimizer();
    $optimizer->update_seo_data_daily();
}