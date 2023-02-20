<?php

class Bob_SEO_Optimizer {
    public function __construct() {
        add_action( 'wp', array( $this, 'update_seo_data' ) );
    }

    public function update_seo_data() {
        if ( ! is_singular() ) {
            return;
        }

        $post_id = get_the_ID();
        $seo_title = get_post_meta( $post_id, '_yoast_wpseo_title', true );
        $seo_description = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );

        // Get the post title and excerpt
        $post_title = get_the_title( $post_id );
        $post_excerpt = wp_trim_words( get_the_excerpt( $post_id ), 20, '...' );
        
        // Check if SEO title and description are empty or not
        if ( empty( $seo_title ) ) {
            $seo_title = $post_title;
        }

        if ( empty( $seo_description ) ) {
            $seo_description = $post_excerpt;
        }

        // Generate a new SEO title and description
        $prompt = 'Write an SEO title and meta description for the following article: ' . $post_title . ' ' . $post_excerpt;
        $api_key = get_option( 'bob-openai-api-key' );
        $new_seo_title = Bob_OpenAI::generate_description( $prompt, $api_key );
        $new_seo_description = Bob_OpenAI::generate_description( $prompt, $api_key );

        // Update the SEO title and description if they are different from the original
        if ( $new_seo_title !== $seo_title ) {
            update_post_meta( $post_id, '_yoast_wpseo_title', $new_seo_title );
        }

        if ( $new_seo_description !== $seo_description ) {
            update_post_meta( $post_id, '_yoast_wpseo_metadesc', $new_seo_description );
        }

        // Update the post modified date
        $last_update_date = get_post_meta( $post_id, 'bob_seo_last_update_date', true );
        $current_date = current_time( 'mysql' );
        $time_diff = strtotime( $current_date ) - strtotime( $last_update_date );
        $days_diff = round( $time_diff / DAY_IN_SECONDS );

        if ( $days_diff >= 200 ) {
            wp_update_post( array(
                'ID' => $post_id,
                'post_modified' => $current_date,
                'post_modified_gmt' => get_gmt_from_date( $current_date )
            ) );

            update_post_meta( $post_id, 'bob_seo_last_update_date', $current_date );
        }
    }
}