<?php
/**
 * Class for optimizing SEO data for posts.
 *
 * @package Bob_SEO_Optimizer
 */
class Bob_SEO_Optimizer {

    private $posts_per_batch;
    private $cron_schedule;
    private $previous_mod_date;
    private $post_type;
    private $order;
    private $meta_max_length;
    private $seo_plugin;
    private $language;
	
	/**
     * Initializes the class.
     */
    public function __construct() {
        $this->posts_per_batch = get_option( 'bob_seo_posts_per_batch', 5 );
        $this->cron_schedule = get_option( 'bob_seo_cron_schedule', 'daily' );
        $this->previous_mod_date = get_option( 'bob_modified_date', '' );
        $this->post_type = get_option( 'bob_seo_post_type', 'post' );
        $this->order = get_option( 'bob_seo_order', 'ASC' );
        $this->meta_max_length = get_option( 'bob_seo_max_length', 160 );
        $this->language = $this->detect_language();

        add_action( 'init', array( $this, 'schedule_bob_seo_event' ) );
        add_action( 'bob_seo_optimizer_daily', array( $this, 'update_seo_data_daily' ) );
    }

    public function schedule_bob_seo_event() {
        // Only run once per day
        if ( ! wp_next_scheduled( 'bob_seo_optimizer_daily' ) ) {
            wp_schedule_event( time(), $this->cron_schedule, 'bob_seo_optimizer_daily' );
        }
    }

    /**
     * Get the SEO meta key for the selected plugin.
     *
     * @return string The SEO meta key.
     */
    public function get_seo_meta_key() {
        // Get the SEO meta key from the selected SEO plugin.
        $seo_plugin = get_option( 'bob_seo_optimizer_seo_plugin', 'yoast_seo' );
        switch ( $seo_plugin ) {
            case 'rank_math':
                $seo_meta_key = 'rank_math_description';
                break;
            case 'seopress':
                $seo_meta_key = '_seopress_titles_desc';
                break;
            case 'all_in_one_seo':
                $seo_meta_key = '_aioseop_description';
                break;
            case 'the_seo_framework':
                $seo_meta_key = '_genesis_description';
                break;
            case 'yoast_seo':
            default:
                $seo_meta_key = '_yoast_wpseo_metadesc';
                break;
        }

        return $seo_meta_key;
    }

    /**
     * Detect the language of the blog.
     *
     * @return string The language code.
     */
    public function detect_language() {
        // Get the site language setting.
        $language = get_bloginfo( 'language' );

        // If the language is not set, default to English.
        if ( empty( $language ) ) {
            $language = 'en';
        }

        // Extract the language code from the site language setting.
        $language = substr( $language, 0, 2 );

        return $language;
    }

    public function update_seo_data_daily() {
        global $wpdb;
    
        $posts_skipped = 0;
    
        $query_args = array(
            'post_type' => $this->post_type,
            'orderby' => 'modified',
            'order' => $this->order,
            'posts_per_page' => $this->posts_per_batch,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => $this->get_seo_meta_key(),
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key' => $this->get_seo_meta_key(),
                    'value' => '',
                    'compare' => '=',
                ),
            ),
        );
    
        $where_clause = $wpdb->prepare(
            "AND NOT EXISTS (
                SELECT * FROM {$wpdb->postmeta}
                WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                AND {$wpdb->postmeta}.meta_key = %s
            )",
            $this->get_seo_meta_key()
        );
    
        $query_args['where'] = $where_clause;
    
        $query = new WP_Query( $query_args );
    
        if ( $query->have_posts() ) {
            $post = $query->posts[0];
            $post_id = $post->ID;
    
            // Check if the post was modified more than three months ago.
            $last_modified_date = get_post_meta( $post_id, '_bob_last_modified_date', true );
            $current_time = current_time( 'U' );
            if ( $last_modified_date && strtotime( $last_modified_date ) + $this->previous_mod_date > $current_time ) {
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
        if ( $current_time - $last_modified_time >= $this->previous_mod_date ) {
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
    
        // Get the post title and excerpt.
        $post_title = get_the_title( $post_id );
        $post_excerpt = wp_trim_words( get_the_excerpt( $post_id ), 25, '...' );
    
        // Check if SEO description is empty or not.
        $seo_meta_key = $this->get_seo_meta_key();
        $seo_description = get_post_meta( $post_id, $seo_meta_key, true );
        if ( empty( $seo_description ) ) {
            $seo_description = $post_excerpt;
        }
    
        // Generate a new SEO description.
        $prompt = add_query_arg(
            array(
                'title' => $post_title,
                'excerpt' => $post_excerpt,
                'max_length' => $this->meta_max_length,
                'language' => $language,
            ),
            esc_html__( 'Write an SEO optimized meta description for the following article:', 'bob-seo-optimizer' )
        );
    
        $api_key = get_option( 'bob-openai-api-key' );
        $openai = new Bob_OpenAI();
    
        $new_seo_description = $openai->generate_description( $prompt, $api_key );
    
        // Update the SEO description if it is different from the original.
        if ( $new_seo_description !== $seo_description ) {
            update_post_meta( $post_id, $seo_meta_key, $new_seo_description );
        }
    }
}