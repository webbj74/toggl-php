<?php

namespace Toggl\Common;

use Guzzle\Service\Client;

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
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function sendGet($path, $variables = array())
    {
        return $this->get(array($path, $variables))->send()->json();
    }

}