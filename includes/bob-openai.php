<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class OpenAIGenerator
{
    private $api_key;
    private $model;
    private $max_tokens;
    private $temperature;
    private $top_p;
    private $frequency_penalty;
    private $presence_penalty;
    private $http_args;

    public function __construct()
    {
        $this->api_key = get_option('bob-openai-api-key');
        $this->model = get_option('bob-openai-model', 'text-davinci-003');
        $this->max_tokens = (int)get_option('bob-openai-max-tokens', 37);
        $this->temperature = (float)get_option('bob-openai-temperature', 0.7);
        $this->top_p = (float)get_option('bob-openai-top-p', 1.0);
        $this->frequency_penalty = (float)get_option('bob-openai-frequency-penalty', 0.0);
        $this->presence_penalty = (float)get_option('bob-openai-presence-penalty', 0.0);

        $this->http_args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
            'timeout' => 200,
        );
    }

    private function get_api_url($model)
    {
        if ($model === 'gpt-3.5-turbo' || $model === 'gpt-4') {
            return 'https://api.openai.com/v1/chat/completions';
        } else {
            return 'https://api.openai.com/v1/completions';
        }
    }

    public function generate_description($prompt, $args = array())
{
    $api_url = $this->get_api_url($this->model);

    if ($this->model === 'gpt-3.5-turbo' || $this->model === 'gpt-4') {
        $request_body['messages'] = array(
            array("role" => "user", "content" => $prompt)
        );
    } else {
        $request_body['prompt'] = $prompt;
    }

    $defaults = array(
        'max_tokens' => $this->max_tokens,
        'model' => $this->model,
        'temperature' => $this->temperature,
        'top_p' => $this->top_p,
        'frequency_penalty' => $this->frequency_penalty,
        'presence_penalty' => $this->presence_penalty,
    );

    $args = wp_parse_args($args, $defaults);

    $request_body = array_merge($request_body, $args);

    $response = wp_safe_remote_post($api_url, array_merge($this->http_args, array('body' => wp_json_encode($request_body))));

    if (is_wp_error($response)) {
        return $response->get_error_message();
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);
    
    if (isset($response_body['choices'][0]['text'])) {
        return $response_body['choices'][0]['text'];
    } elseif (isset($response_body['choices'][0]['message']['content'])) {
        return $response_body['choices'][0]['message']['content'];
    }
}

}