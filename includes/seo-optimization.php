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
        add_action( 'init', array( $this, 'schedule_bob_seo_event' ) );
        add_action( 'bob_seo_optimizer_daily', array( $this, 'update_seo_data_daily' ) );
    }

    public function schedule_bob_seo_event() {
        // Only run once per day
        if ( ! wp_next_scheduled( 'bob_seo_optimizer_daily' ) ) {
            wp_schedule_event( time(), self::CRON_SCHEDULE, 'bob_seo_optimizer_daily' );
        }
    }

    public function update_seo_data_daily() {
        global $wpdb;
    
        $posts_skipped = 0;
    
        $query_args = array(
            'post_type' => 'post',
            'orderby' => 'modified',
            'order' => 'ASC',
            'posts_per_page' => 1,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_yoast_wpseo_metadesc',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key' => '_yoast_wpseo_metadesc',
                    'value' => '',
                    'compare' => '=',
                ),
            ),
        );
    
        $where_clause = $wpdb->prepare(
            "AND NOT EXISTS (
                SELECT * FROM {$wpdb->postmeta}
                WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                AND {$wpdb->postmeta}.meta_key = '_yoast_wpseo_metadesc'
            )"
        );
    
        $query_args['where'] = $where_clause;
    
        $query = new WP_Query( $query_args );
    
        if ( $query->have_posts() ) {
            $post = $query->posts[0];
            $post_id = $post->ID;
    
            // Check if the post was modified more than three months ago.
            $last_modified_date = get_post_meta( $post_id, '_bob_last_modified_date', true );
            $current_time = current_time( 'U' );
            if ( $last_modified_date && strtotime( $last_modified_date ) + self::THREE_MONTHS_IN_SECONDS > $current_time ) {
                $posts_skipped++;
            } else {
                // Update the modified time for the post.
                $this->update_post_modified_time( $post_id );
    
                // Update the SEO data for the post.
                $this->update_seo_data( $post_id );
            }
        }
    
        wp_reset_postdata();
    
        // Reschedule the event to run again later in the day.
        $next_scheduled_time = time() + rand( 3600, 10800 ); // Random delay between 1 and 3 hours
    	wp_schedule_single_event( $next_scheduled_time, 'bob_seo_optimizer_daily' );
    }

    /**
     * Updates the modified time for the post if it has been more than three months since the post was last modified.
     */
    public function update_post_modified_time( $post_id ) {
        $last_modified_time = get_post_modified_time( 'U', true, $post_id );
        $current_time = current_time( 'U' );
        if ( $current_time - $last_modified_time >= self::THREE_MONTHS_IN_SECONDS ) {
            $post = get_post( $post_id );
            $previous_author_id = get_the_author_meta( 'ID', $post->post_author );
            
            if ( $previous_author_id ) {
                wp_update_post( array(
                    'ID' => $post_id,
                    'post_author' => $previous_author_id,
                    'post_modified' => current_time( 'mysql' ),
                    'post_modified_gmt' => current_time( 'mysql', 1 ),
                ) );
                update_post_meta( $post_id, '_bob_last_modified_date', date( 'Y-m-d' ) );
            }
        }
    }

    /**
     * Updates the SEO data for the post.
     */
    public function update_seo_data( $post_id ) {
        if ( get_post_type( $post_id ) !== 'post' ) {
            return;
        }
		
		// Get the current SEO description for the post
        $seo_description = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );

        // Get the post title and excerpt.
        $post_title = get_the_title( $post_id );
                
        $post_excerpt = wp_trim_words( get_the_excerpt( $post_id ), 25, '...' );

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
    }
}