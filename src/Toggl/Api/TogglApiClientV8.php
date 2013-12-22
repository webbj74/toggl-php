<?php

namespace Toggl\Api;

use Guzzle\Common\Collection;
use Toggl\Common\TogglClientAuthPlugin;

class TogglApiClientV8 extends TogglApiClient
{
    const BASE_PATH = '/api/v8';

    /**
     * {@inheritdoc}
     *
     * @return \Toggl\Common\TogglClient
     */
    public static function factory($config = array())
    {
        $required = array(
            'authentication_method',
            'authentication_key',
            'authentication_value',
            'base_path',
        );

        $defaults = array(
            'base_url' => self::BASE_URL,
            'base_path' => self::BASE_PATH,
        );

        if (isset($config['authentication_method']) && $config['authentication_method'] == 'token') {
            $defaults['authentication_value'] = 'api_token';
        }

        $config = Collection::fromConfig($config, $defaults, $required);
        $client = new static($config->get('base_url'), $config);
        $client->setDefaultHeaders(array(
                'Content-Type' => 'application/json; charset=utf-8',
            ));

        $plugin = new TogglClientAuthPlugin($config->get('authentication_key'), $config->get('authentication_value'));
        $client->addSubscriber($plugin);

        return $client;
    }

    public function me()
    {
        $data = $this->sendGet('{+base_path}/me');
        return new Response\Me($data);
    }

    public function getWorkspaces()
    {
        $data = $this->sendGet('{+base_path}/workspaces');
        return new Response\Workspaces($data);
    }

}
