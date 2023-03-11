<?php
/**
 * Plugin Name: Bob
 * Description: A WordPress plugin that optimizes and updates meta descriptions using OpenAI to improve search engine visibility and boost click-through rates.
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