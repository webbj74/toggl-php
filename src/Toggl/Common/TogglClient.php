<?php

namespace Toggl\Common;

use GuzzleHttp\Client;

class TogglClient extends Client
{
    const BASE_URL = 'https://www.toggl.com';

    /**
     * Helper method to send a GET request and return parsed JSON.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\ClientException
     */
    public function sendGet($path, $variables = [])
    {
        return json_decode($this->get($path, $variables)->getBody(), TRUE);
    }

    /**
     * Helper method to send a PUT request and return parsed JSON.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     * @param string $body
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\ClientException
     */
    public function sendPut($path, $variables = [], $body)
    {
        $variables['body'] = $body;
        return json_decode($this->put($path, $variables)->getBody(), TRUE);
    }

    /**
     * Helper method to send a POST request and return parsed JSON.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     * @param string $body
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\ClientException
     */
    public function sendPost($path, $variables = [], $body)
    {
        $variables['body'] = $body;
        return json_decode($this->post($path, $variables)->getBody(), TRUE);
    }
}