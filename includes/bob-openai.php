<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Bob_OpenAI {
    private $api_endpoint = 'https://api.openai.com/v1/completions';
    private $api_key;
    private $http_args;

    /**
     * Initializes the OpenAI class and sets the API key and HTTP request arguments.
     */
    public function __construct() {
        $this->api_key = get_option( 'bob-openai-api-key' );

        $this->http_args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
        );
    }

    /**
     * @param string $prompt The prompt for the API request.
     * @param array  $args   Optional arguments for the API request.
     *
     * @return string|WP_Error The generated text or a WP_Error object on failure.
     */
    public function generate_description( $prompt, $args = array() ) {
        
        $defaults = array(
            'max_tokens' => 256,
            'model' => 'text-davinci-003',
            'temperature' => 0.7,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        );

        // Merge arguments with defaults.
        $args = wp_parse_args( $args, $defaults );

        // Sanitize and validate arguments.
        $max_tokens = absint( $args['max_tokens'] );
        $model = sanitize_text_field( $args['model'] );
        $temperature = floatval( $args['temperature'] );
        $top_p = floatval( $args['top_p'] );
        $frequency_penalty = floatval( $args['frequency_penalty'] );
        $presence_penalty = floatval( $args['presence_penalty'] );

        // Prepare the request body.
        $request_body = array(
            'prompt' => $prompt,
            'max_tokens' => $max_tokens,
            'model' => $model,
            'temperature' => $temperature,
            'top_p' => $top_p,
            'frequency_penalty' => $frequency_penalty,
            'presence_penalty' => $presence_penalty,
        );

        $response = wp_safe_remote_post( $this->api_endpoint, array_merge( $this->http_args, array( 'body' => wp_json_encode( $request_body ) ) ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        // Check if the response was successful.
        $status_code = wp_remote_retrieve_response_code( $response );
        if ( $status_code !== 200 ) {
            return new WP_Error( 'openai_api_error', sprintf( 'Error %d: %s', $status_code, wp_remote_retrieve_response_message( $response ) ) );
        }

        // Get the response body and return the generated text.
        $response_body = json_decode( wp_remote_retrieve_body( $response ), true );
        return $response_body['choices'][0]['text'];
    }
}