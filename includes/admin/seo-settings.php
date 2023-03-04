<?php
/**
 * Represents the SEO settings page for the Bob plugin.
 */

 require_once plugin_dir_path( __FILE__ ) . 'bob-seo-plugins.php';

class Bob_SEO_Settings {
    /**
     * Initializes the SEO settings page.
     */
    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Registers the SEO settings for the Bob plugin.
     */
    public function register_settings() {
        register_setting( 'bob-seo-settings-group', 'bob_seo_posts_per_batch', array( $this, 'sanitize_posts_per_batch' ) );
        register_setting( 'bob-seo-settings-group', 'bob_seo_cron_schedule', array( $this, 'sanitize_cron_schedule' ) );
        register_setting( 'bob-seo-settings-group', 'bob_modified_date', array( $this, 'sanitize_bob_modified_date' ) );
        register_setting( 'bob-seo-settings-group', 'bob_seo_post_type', array( $this, 'sanitize_post_type' ) );
        register_setting( 'bob-seo-settings-group', 'bob_seo_order', array( $this, 'sanitize_order' ) );
        register_setting( 'bob-seo-settings-group', 'bob_seo_max_length', array( $this, 'sanitize_seo_max_length' ) );
        register_setting( 'bob-seo-settings-group', 'bob_seo_optimizer_seo_plugin', array( $this, 'sanitize_seo_plugin' ) );

        add_settings_section( 'bob_seo_section', __( 'Bob SEO Settings', 'bob-seo-optimizer' ), [ $this, 'render_seo_settings_section' ], 'bob-seo-optimizer' );
    }

    /**
     * Renders the SEO settings section.
     */
    public function render_seo_settings_section() {
        echo 'Configure your SEO settings below:';
    }

    /**
     * Sanitizes the value for the "posts_per_batch" setting.
     *
     * @param mixed $value The value to sanitize.
     * @return int The sanitized value.
     */
    public function sanitize_posts_per_batch( $value ) {
        return absint( $value );
    }

    /**
     * Sanitizes the value for the "cron_schedule" setting.
     *
     * @param mixed $value The value to sanitize.
     * @return string The sanitized value.
     */
    public function sanitize_cron_schedule( $value ) {
        return sanitize_text_field( $value );
    }

    /**
     * Sanitizes the value for the "bob_modified_date" setting.
     *
     * @param mixed $value The value to sanitize.
     * @return int The sanitized value.
     */
    public function sanitize_bob_modified_date( $value ) {
        return absint( $value );
    }

        /**
     * Sanitizes the value for the "post_type" setting.
     *
     * @param mixed $value The value to sanitize.
     * @return string The sanitized value.
     */
    public function sanitize_post_type( $value ) {
        return sanitize_text_field( $value );
    }

    /**
     * Sanitizes the value for the "order" setting.
     *
     * @param mixed $value The value to sanitize.
     * @return string The sanitized value.
     */
    public function sanitize_order( $value ) {
        return sanitize_text_field( $value );
    }

    /**
     * Sanitizes the value for the "max_length" setting.
     *
     * @param mixed $value The value to sanitize.
     * @return string The sanitized value.
     */
    public function sanitize_seo_max_length( $value ) {
        return sanitize_text_field( $value );
    }

    /**
     * Sanitizes the value for the "bob_seo_optimizer_seo_plugin" setting.
     *
     * @param mixed $value The value to sanitize.
     * @return string The sanitized value.
     */
    public function sanitize_seo_plugin( $value ) {
        $valid_options = array(
            'yoast_seo',
            'rank_math',
            'seopress',
            'all_in_one_seo',
            'the_seo_framework'
        );

        if ( in_array( $value, $valid_options ) ) {
            return $value;
        } else {
            return '';
        }
    }

    /**
     * Renders the SEO settings page.
     */
    public function render_seo_settings_page() {
        $posts_per_batch = get_option( 'bob_seo_posts_per_batch', 5 );
        $cron_schedule = get_option( 'bob_seo_cron_schedule', 'daily' );
        $previous_mod_date = get_option( 'bob_modified_date', 90 );
        $post_type = get_option( 'bob_seo_post_type', 'post' );
        $order = get_option( 'bob_seo_order', 'ASC' );
        $meta_max_length = get_option( 'bob_seo_max_length', 160 );
        $selected_seo_plugin = get_option( 'bob_seo_optimizer_seo_plugin', 'yoast_seo' );
        
        ?>

        <div class="wrap">
            <h1><?php esc_html_e( 'Bob SEO Optimizer Settings', 'bob-seo-optimizer' ); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields( 'bob-seo-settings-group' ); ?>
                <?php do_settings_sections( 'bob-seo-optimizer' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="posts-per-batch"><?php esc_html_e( 'Posts Per Batch:', 'bob-seo-optimizer' ); ?></label>
                            </th>
                            <td>
                                <input name="bob_seo_posts_per_batch" type="number" id="posts-per-batch" value="<?php echo esc_attr( $posts_per_batch ); ?>" class="regular-text">
                                <p class="description"><?php esc_html_e( 'The number of posts to update in a single batch.', 'bob-seo-optimizer' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="cron-schedule"><?php esc_html_e( 'Cron Schedule:', 'bob-seo-optimizer' ); ?></label>
                            </th>
                            <td>
                                <select name="bob_seo_cron_schedule" id="cron-schedule">
                                    <option value="daily" <?php selected( $cron_schedule, 'daily' ); ?>><?php esc_html_e( 'Daily', 'bob-seo-optimizer' ); ?></option>
                                    <option value="twicedaily" <?php selected( $cron_schedule, 'twicedaily' ); ?>><?php esc_html_e( 'Twice Daily', 'bob-seo-optimizer' ); ?></option>
                                </select>
                                <p class="description"><?php esc_html_e( 'The schedule for the cron job that updates SEO data daily.', 'bob-seo-optimizer' ); ?></p>
                            </td>
                        </tr>
                         <tr>
                            <th scope="row">
                                <label for="bob-modified-date"><?php esc_html_e( 'Bob Modified Date:', 'bob-seo-optimizer' ); ?></label>
                            </th>
                            <td>
                                <input name="bob_modified_date" type="number" id="bob-modified-date" value="<?php echo esc_attr( $previous_mod_date ); ?>" class="regular-text">
                                <p class="description"><?php esc_html_e( 'The number of days for which the modified date should be considered for SEO.', 'bob-seo-optimizer' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                        <th scope="row">
                            <label for="post-type"><?php esc_html_e( 'Post Type:', 'bob-seo-optimizer' ); ?></label>
                        </th>
                        <td>
                        <?php $post_types = get_post_types( array( 'public' => true ), 'names' ); ?>
                            <select name="bob_seo_post_type" id="post-type">
                                <?php foreach ( $post_types as $post_type ) : ?>
                                    <?php $selected = ( $post_type == get_option( 'bob_seo_post_type', 'post' ) ) ? 'selected="selected"' : ''; ?>
                                    <option value="<?php echo esc_attr( $post_type ); ?>" <?php echo $selected; ?>><?php echo esc_html( $post_type ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'The post type for which the SEO meta should be optimized.', 'bob-seo-optimizer' ); ?></p>
                        </td>
                        </tr>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="order"><?php esc_html_e( 'Order:', 'bob-seo-optimizer' ); ?></label>
                            </th>
                            <td>
                                <select name="bob_seo_order" id="order">
                                    <option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php esc_html_e( 'Ascending', 'bob-seo-optimizer' ); ?></option>
                                    <option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php esc_html_e( 'Descending', 'bob-seo-optimizer' ); ?></option>
                                </select>
                                <p class="description"><?php esc_html_e( 'The order in which the posts are being sorted.', 'bob-seo-optimizer' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="bob-seo-max-length"><?php esc_html_e( 'Meta Description Length:', 'bob-seo-optimizer' ); ?></label>
                            </th>
                            <td>
                                <input name="bob_seo_max_length" type="number" id="bob-seo-max-length" value="<?php echo esc_attr( $meta_max_length ); ?>" class="regular-text">
                                <p class="description"><?php esc_html_e( 'The meta description length - Can be longer or shorter but around 150 and 160 characters or more..', 'bob-seo-optimizer' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php esc_html_e( 'Select your preferred SEO plugin:', 'bob-seo-optimizer' ); ?>
                            </th>
                            <td>
                                <select name="bob_seo_optimizer_seo_plugin">
                                    <?php
                                    $options = bob_seo_optimizer_plugin_options();
                                    foreach ( $options as $value => $label ) {
                                        ?>
                                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, get_option( 'bob_seo_optimizer_seo_plugin' ) ); ?>><?php echo esc_html( $label ); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                    </tbody>
                </table>
            <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }}