<?php
/**
 * Returns an array of available SEO plugins.
 *
 * @return array Available SEO plugins.
 */
function bob_seo_optimizer_plugin_options() {
    $options = array(
        'yoast_seo' => 'Yoast SEO',
        'rank_math' => 'Rank Math',
        'seopress' => 'SEOPress',
        'all_in_one_seo' => 'All in One SEO',
        'the_seo_framework' => 'The SEO Framework'
    );

    return $options;
}

/**
 * Renders the settings page where users can select their preferred SEO plugin.
 */
function bob_seo_optimizer_settings_page() {
    $options = bob_seo_optimizer_plugin_options();
?>
    <div class="wrap">
        <h2><?php esc_html_e( 'SEO Optimizer Settings', 'bob-seo-optimizer' ); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'bob_seo_optimizer_settings' ); ?>
            <?php do_settings_sections( 'bob_seo_optimizer_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Select your current SEO plugin:', 'bob-seo-optimizer' ); ?></th>
                    <td>
                        <select name="bob_seo_optimizer_seo_plugin">
                            <?php foreach ( $options as $value => $label ) { ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, get_option( 'bob_seo_optimizer_seo_plugin' ) ); ?>><?php echo esc_html( $label ); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}