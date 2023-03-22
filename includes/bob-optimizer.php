<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once BOB_PLUGIN_DIR . 'includes/bob-meta-checker.php';
require_once BOB_PLUGIN_DIR . 'includes/bob-openai.php';

/**
 * Class for optimizing SEO meta description for posts.
 *
 * @package Bob_SEO_Optimizer
 */
class Bob_SEO_Optimizer {
    private $posts_per_batch;
    private $previous_mod_date;
    private $post_type;
    private $order;
    private $meta_max_length;
    private $meta_checker;
    	
	/**
     * Initializes the class.
     */
    public function __construct() {
        $this->initialize_properties();
        $this->register_hooks();
    }

    private function initialize_properties() {
        $this->posts_per_batch = 1;
        $this->previous_mod_date = 0 * DAY_IN_SECONDS;
        $this->post_type = 'post';
        $this->order = 'ASC';
        $this->meta_max_length = 160;
        $this->meta_checker = new Bob_Meta_Checker();
    }
    
    private function register_hooks() {
        add_action( 'bob_optimizer_cron', array( $this, 'update_seo_data_daily' ) );
    }    

    public function update_seo_data_daily() {
        $args = array(
            'post_type' => $this->post_type,
            'orderby' => 'modified',
            'order' => $this->order,
            'posts_per_page' => $this->posts_per_batch,
        );
    
        $query = new WP_Query( $args );
    
        $posts_skipped = 0;
    
        while ( $query->have_posts() ) {
            $query->the_post();
    
            $post_id = get_the_ID();
    
            $meta_query = array(
                'relation' => 'OR',
                array(
                    'key' => $this->meta_checker->get_meta_key( $post_id ),
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key' => $this->meta_checker->get_meta_key( $post_id ),
                    'value' => '',
                    'compare' => '=',
                ),
            );
    
            $args['meta_query'] = $meta_query;
    
            $query = new WP_Query( $args );
    
            // Check if the post was modified more than three months ago.
            $last_modified_time = get_post_modified_time( 'U', true, $post_id );
            $current_time = current_time( 'U' );
            if ( $current_time - $last_modified_time >= $this->previous_mod_date ) {
                // Update the modified time for the post.
                $this->update_post_modified_time( $post_id );
    
                // Update the SEO data for the post.
                $this->update_seo_data( $post_id );
            } else {
                continue;
            }
    
            if ( ++$posts_skipped >= $this->posts_per_batch ) {
                break;
            }
        }
    
        wp_reset_postdata();
    
        // Set the cron job to run again randomly between 1 and 3 hours.
        $this->schedule_seo_update();
    }

    public function schedule_seo_update() {
		// Schedule the event to run between 1 and 3 hours later.
		$next_scheduled_time = time() + rand( 3600, 10800 ); // Random delay between 1 and 3 hours
		wp_schedule_single_event( $next_scheduled_time, 'bob_optimizer_cron' );
	}

    public function save_stats($post_id, $meta_description) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bob_ai_stats';
        $word_count = str_word_count($meta_description);

        $wpdb->insert(
            $table_name,
            [
                'post_id' => $post_id,
                'meta_description' => $meta_description,
                'word_count' => $word_count,
                'updated_at' => current_time('mysql')
            ],
            ['%d', '%s', '%d', '%s']
        );
    }

    /**
     * Updates the modified time for the post.
     */
    public function update_post_modified_time( $post_id ) {
        $last_modified_time = get_post_modified_time( 'U', true, $post_id );
        $current_time = current_time( 'U' );
        if ( $current_time - $last_modified_time >= $this->previous_mod_date ) {
            $post = get_post( $post_id );
            $previous_author_id = get_the_author_meta( 'ID', $post->post_author );
    
            $post_data = array(
                'ID' => $post_id,
                'post_author' => $previous_author_id,
            );
    
            wp_update_post( $post_data );
            update_post_meta( $post_id, '_bob_last_modified_date', date( 'Y-m-d' ) );
        }
    }    

    /**
     * Updates Meta data for the post.
     */
    public function update_seo_data( $post_id ) {
        
        // Get the post title and excerpt.
        $post_title = get_the_title();
        $post_excerpt = wp_trim_words( get_the_excerpt(), 25, '...' );
        
        // Check if Meta description is empty or not.
        $seo_meta_key = $this->meta_checker->get_meta_key( $post_id );
        $seo_description = get_post_meta( $post_id, $seo_meta_key, true );
        if ( empty( $seo_description ) )  {
            $seo_description = $post_excerpt;
        }

        // Generate a new Meta description.
        $prompt = add_query_arg(
            array(
                'title' => $post_title,
                'excerpt' => $post_excerpt,
                'max_length' => $this->meta_max_length,
            ),
            __( 'Write an SEO optimized meta description for the following article:', 'bob-seo-optimizer' )
        );

        $api_key = get_option( 'bob-openai-api-key' );
        if ( $api_key ) {
            $openai = new Bob_OpenAI();
            $new_seo_description = $openai->generate_description( $prompt, $api_key );

            // Update the Meta description if it is different from the original.
            if ( $new_seo_description !== $seo_description ) {
                update_post_meta( $post_id, $seo_meta_key, $new_seo_description );
                $this->save_stats($post_id, $new_seo_description);
            }
        }
    }
}    