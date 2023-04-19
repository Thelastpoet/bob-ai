<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$api_key = get_option( 'bob-openai-api-key' );
$settings_saved = $this->bob_settings_saved_notice();
$bob_ai_status = get_option( 'bob_ai_status' );

if ($settings_saved) {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php esc_html_e( 'Settings saved successfully.', 'bob' ); ?></p>
    </div>
    <?php
}

$disabled = empty( $api_key ) || !$settings_saved || $bob_ai_status === 'stopped' ? '' : 'disabled';

$escaped_disabled = esc_attr( $disabled );

if ( isset( $_POST['start-bob-ai'] ) && $_POST['start-bob-ai'] ) {
    if ( ! wp_next_scheduled( 'bob_optimizer_cron' ) ) {
        $this->seo_optimizer->schedule_seo_update();
    }
}

if ( isset( $_POST['stop-bob-ai'] ) && $_POST['stop-bob-ai'] ) {
    wp_clear_scheduled_hook( 'bob_optimizer_cron' );
}
?>

<div class="bob-general">
    <h2><?php esc_html_e('Welcome to Bob AI', 'bob'); ?></h2>
    <p>
        <?php esc_html_e('Bob AI helps you optimize and update meta descriptions using OpenAI to improve search engine visibility and boost click-through rates.', 'bob'); ?>
    </p>
    <p>
        <?php esc_html_e('Please configure the OpenAI and SEO settings tabs before starting Bob AI.', 'bob'); ?>
    </p>
</div>

<form method="post">
    <?php wp_nonce_field( 'bob_meta_generation_nonce' ); ?>
    <div>
        <button id="start-bob-ai" class="button button-primary" name="start-bob-ai" type="submit" <?php echo $escaped_disabled; ?> <?php echo esc_attr($start_button_display); ?>><?php esc_html_e('Start Bob AI', 'bob'); ?></button>
        <button id="stop-bob-ai" class="button button-secondary" name="stop-bob-ai" type="submit" <?php echo esc_attr($stop_button_display); ?>><?php esc_html_e('Stop Bob AI', 'bob'); ?></button>
    </div>
</form>