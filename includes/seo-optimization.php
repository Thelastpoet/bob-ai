<?php
/**
 * Class for optimizing SEO data for posts.
 *
 * @package Bob_SEO_Optimizer
 */
class Bob_SEO_Optimizer {

    const POSTS_PER_BATCH = 5;
    const CRON_SCHEDULE = 'daily';
    const THREE_MONTHS_IN_SECONDS = 60 * 60 * 24 * 30 * 3;

    /**
     * Initializes the class.
     */
    public function __construct() {

    }

    /**
     * Checks if the post is singular and updates the SEO data if needed.
     */
    public function maybe_update_seo_data() {
        if ( ! is_singular() ) {
            return;
        }

        $post_id = get_the_ID();
        $seo_description = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );

        // Get the post title and excerpt.
        $post_title = get_the_title( $post_id );
        $post_excerpt = wp_trim_words( get_the_excerpt( $post_id ), 20, '...' );

        // Check if SEO description is empty or not.
        if ( empty( $seo_description ) ) {
            $seo_description = $post_excerpt;
        }

        // Generate a new SEO description.
        $prompt = add_query_arg(
            array(
                'title' => $post_title,
                'excerpt' => $post_excerpt,
                'max_length' => 180,
            ),
            esc_html__( 'Write an SEO optimized meta description for the following article:', 'bob-seo-optimizer' )
        );

        $api_key = get_option( 'bob-openai-api-key' );
        $openai = new Bob_OpenAI();
        $new_seo_description = $openai->generate_description( $prompt, $api_key );

        // Update the SEO description if it is different from the original.
        if ( $new_seo_description !== $seo_description ) {
            update_post_meta( $post_id, '_yoast_wpseo_metadesc', $new_seo_description );
        }

        // Update the modified date.
        $last_modified_time = get_the_modified_time( 'U', $post_id );
        $current_time = current_time( 'U' );
        if ( $current_time - $last_modified_time >= self::THREE_MONTHS_IN_SECONDS ) {
            wp_update_post( array(
                'ID' => $post_id,
                'post_modified' => current_time( 'mysql' ),
                'post_modified_gmt' => current_time( 'mysql', 1 ),
            ) );
            update_post_meta( $post_id, '_bob_last_modified_date', date( 'F jS, Y' ) );
            update_post_meta( $post_id, '_bob_last_modified_time', date( 'h:i a' ) );
        }
    }
    
    /**
 * Optimizes the SEO data for posts.
 */
public function optimize_posts() {
    add_action( 'wp', array( $this, 'maybe_update_seo_data' ), 10 );

    $posts_updated = 0;
    $posts_skipped = 0;

    $query_args = array(
        'post_type'      => 'post',
        'meta_query'     => array(
            array(
                'key'     => '_yoast_wpseo_metadesc',
                'value'   => '',
                'compare' => '=',
            ),
        ),
        'orderby'        => 'modified',
        'order'          => 'ASC',
        'posts_per_page' => self::POSTS_PER_BATCH,
    );

    $posts = new WP_Query( $query_args );

    if ( $posts->have_posts() ) {
        while ( $posts->have_posts() ) {
            $posts->the_post();

            $post_id = get_the_ID();
            $last_modified_date = get_post_meta( $post_id, '_bob_last_modified_date', true );

            if ( $last_modified_date ) {
                $current_time = current_time( 'U' );
                $last_modified_timestamp = strtotime( $last_modified_date );
                $time_since_last_modified = $current_time - $last_modified_timestamp;

                if ( $time_since_last_modified < self::THREE_MONTHS_IN_SECONDS ) {
                    $posts_skipped++;
                    continue;
                }
            }

            $this->maybe_update_seo_data();

            $posts_updated++;

            if ( $posts_updated >= self::POSTS_PER_BATCH ) {
                break;
            }
        }
    }

    wp_reset_query();

    // If we updated less than self::POSTS_PER_BATCH posts, we must have updated all of them.
    if ( $posts_updated < self::POSTS_PER_BATCH ) {
        $this->unschedule_cron();
    }
}

/**
 * Schedules the cron job for optimizing the SEO data.
 */
public function schedule_cron() {
    if ( ! wp_next_scheduled( 'bob_seo_optimizer_cron' ) ) {
        wp_schedule_event( time(), self::CRON_SCHEDULE, 'bob_seo_optimizer_cron' );
    }
}

/**
 * Unschedules the cron job for optimizing the SEO data.
 */
public function unschedule_cron() {
    wp_clear_scheduled_hook( 'bob_seo_optimizer_cron' );
}

}

// Create a new instance of the SEO optimizer class.
$seo_optimizer = new Bob_SEO_Optimizer();

// Schedule the SEO optimization cron job.
$seo_optimizer->schedule_cron();

// Register the cron job.
add_action( 'bob_seo_optimizer_cron', array( $seo_optimizer, 'optimize_posts' ) );
