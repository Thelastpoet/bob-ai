<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Get options and saved settings status
$api_key = get_option( 'bob-openai-api-key' );
$settings_saved = $this->bob_settings_saved_notice();
$bob_ai_status = get_option( 'bob_ai_status' );

// Display success notice when settings are saved
if ($settings_saved) {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php esc_html_e( 'Settings saved successfully.', 'bob-ai' ); ?></p>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var settingsSavedEvent = new CustomEvent('settingsSaved');
            document.dispatchEvent(settingsSavedEvent);
        });
    </script>
    <?php
}

// Determine if the "Start Bob AI" button should be disabled
$disabled = empty( $api_key ) || !$settings_saved || $bob_ai_status === 'stopped' ? '' : 'disabled';

$escaped_disabled = esc_attr( $disabled );

// Start Bob AI when the "Start Bob AI" button is clicked
if ( isset( $_POST['start-bob-ai'] ) && $_POST['start-bob-ai'] ) {
    if ( ! wp_next_scheduled( 'bob_optimizer_cron' ) ) {
        $this->seo_optimizer->schedule_seo_update();
    }
}

// Stop Bob AI when the "Stop Bob AI" button is clicked
if ( isset( $_POST['stop-bob-ai'] ) && $_POST['stop-bob-ai'] ) {
    wp_clear_scheduled_hook( 'bob_optimizer_cron' );
}
?>

<!-- Display the welcome message and instructions -->
<div class="bob-general">
    <h2><?php esc_html_e('Welcome to Bob AI', 'bob-ai'); ?></h2>
    <p>
        <?php esc_html_e('Bob AI helps you optimize and update meta descriptions using OpenAI to improve search engine visibility and boost click-through rates.', 'bob-ai'); ?>
    </p>
    <p>
        <?php esc_html_e('Please configure the OpenAI and SEO settings tabs before starting Bob AI.', 'bob-ai'); ?>
    </p>
    <p>
        <?php esc_html_e('Once started, Bob AI will update your articles randomly between 1 and 3 hours. So check back the progress in Bob Stats after 3 hours.', 'bob-ai'); ?>
    </p>
</div>

<form method="post">
    <?php wp_nonce_field( 'bob_meta_generation_nonce', 'bob_meta_generation_nonce_field' );
 ?>
    <div>
        <button id="start-bob-ai" class="button button-primary" name="start-bob-ai" type="submit" <?php echo $escaped_disabled; ?> <?php echo esc_attr($start_button_display); ?>><?php esc_html_e('Start Bob AI', 'bob-ai'); ?></button>
        <button id="stop-bob-ai" class="button button-secondary" name="stop-bob-ai" type="submit" <?php echo esc_attr($stop_button_display); ?>><?php esc_html_e('Stop Bob AI', 'bob-ai'); ?></button>
    </div>
</form>