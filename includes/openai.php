<?php

class Bob_OpenAI {
    public function generate_description( $prompt, $api_key ) {
        $max_tokens = get_option( 'bob-openai-max-tokens' );
        $model = get_option( 'bob-openai-model' );
        $temperature = get_option( 'bob-openai-temperature' );
        $top_p = get_option( 'bob-openai-top-p' );
        $frequency_penalty = get_option( 'bob-openai-frequency-penalty' );
        $presence_penalty = get_option( 'bob-openai-presence-penalty' );

        // Set default values if options are not set
        if ( ! $max_tokens ) {
            $max_tokens = 256;
        }

        if ( ! $model ) {
            $model = 'text-curie-001';
        }

        if ( ! $temperature ) {
            $temperature = 0.7;
        }

        if ( ! $top_p ) {
            $top_p = 1;
        }

        if ( ! $frequency_penalty ) {
            $frequency_penalty = 0;
        }

        if ( ! $presence_penalty ) {
            $presence_penalty = 0;
        }

        $options = [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key
            ],
            'body' => json_encode ([
                'prompt' => $prompt,
                'max_tokens' => intval($max_tokens),
                'model' => $model,
                'temperature' => floatval($temperature),
                'top_p' => floatval($top_p),
                'frequency_penalty' => floatval($frequency_penalty),
                'presence_penalty' => floatval($presence_penalty)
            ])
        ];

        $response = wp_remote_post( 'https://api.openai.com/v1/completions', $options );

        // Check if there was an error with the API request
        if ( is_wp_error( $response ) ) {
            return 'Error: ' . $response->get_error_message();
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        return $body['choices'][0]['text'];
    }
}