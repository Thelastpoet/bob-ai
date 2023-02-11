<?php

class Bob_Functions {
    public function __construct() {
        // Include the OpenAI class file
        require_once plugin_dir_path( __FILE__ ) . 'openai.php';

        // Initialize the OpenAI class
        $this->openai = new Bob_OpenAI();

        // Add the update_post_description function as an action to be triggered when the WordPress environment is loaded
        add_action( 'wp', array( $this, 'update_post_description' ) );
    }

    public function update_post_description() {
        // Check if the current page is a single post
        if ( is_single() ) {
            // Get the current post type
            $post_type = get_post_type();
            // Get the post type setting from the options
            $post_type_setting = get_option( 'bob-post-type' );
            // Get the taxonomy setting from the options
            $taxonomy_setting = get_option( 'bob-taxonomy' );

            // Check if the post type exists and is equal to the post type setting and if the taxonomy exists and is equal to the taxonomy setting
            if ( post_type_exists( $post_type ) && $post_type == $post_type_setting && taxonomy_exists( $taxonomy_setting ) ) {
                // Get the current post object
                $post = get_post();
                // Get the terms for the current post and the selected taxonomy
                $terms = get_the_terms( $post->ID, $taxonomy_setting );

                // Check if the terms exist and are not an error
                if ( $terms && ! is_wp_error( $terms ) ) {
                    // Get the OpenAI API key
                    $api_key = get_option( 'bob-openai-api-key' );

                    // Loop through the terms
                    foreach ( $terms as $term ) {
                        // Check if the term description doesn't exist
                        if ( ! $this->term_description_exists( $term->term_id, $taxonomy_setting ) ) {
                            // Create the prompt for the OpenAI API
                            $prompt = sprintf( 'Provide an accurate description for %s %s', $taxonomy_setting, $term->name );
                            // Generate the description using the OpenAI API
                            $description = $this->openai->generate_description( $prompt, $api_key );
                            // Update the term with the generated description
                            $update_result = wp_update_term( $term->term_id, $taxonomy_setting, array(
                                'description' => $description,
                            ) );
                        }
                    }
                }
            }
        }
    }

    // Function to check if the term description exists
    public function term_description_exists( $term_id, $taxonomy ) {
        $term_description = term_description( $term_id, $taxonomy );
        return ! empty( $term_description );
    }
}

// Initialize the Bob_Functions class
$bob = new Bob_Functions();