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
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function sendGet($path, $variables = array())
    {
        return $this->get(array($path, $variables))->send()->json();
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
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function sendPut($path, $variables = array(), $body)
    {
        return $this->put(array($path, $variables), null, $body)->send()->json();
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
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function sendPost($path, $variables = array(), $body)
    {
        return $this->post(array($path, $variables), null, $body)->send()->json();
    }
}