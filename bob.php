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
    $bob = new Bob_Core();
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
    delete_option( 'bob_taxonomy_terms_per_batch' );
    delete_option( 'bob_taxonomy_cron_schedule' );
    delete_option( 'bob_last_modified_date' );
    delete_option( 'bob_modified_terms' );
    delete_option( 'bob_modified_term_ids' );
    wp_clear_scheduled_hook( 'bob_seo_optimizer_daily' );
}
register_deactivation_hook( __FILE__, 'bob_deactivate' );