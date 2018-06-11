<?php

namespace Toggl\Reports;

use Toggl\Common\TogglClientAuthPlugin;

class TogglReportsApiClientV2 extends TogglReportsApiClient
{
    const BASE_PATH = '/reports/api/v2';

    /**
     * {@inheritdoc}
     *
     * @return \Toggl\Common\TogglClient
     */
    public static function factory($config = [])
    {
        $required = [
            'authentication_method',
            'authentication_key',
            'authentication_value',
            'base_path',
        ];

        $defaults = [
            'base_url' => self::BASE_URL,
            'base_path' => self::BASE_PATH,
        ];

        if (isset($config['authentication_method']) && $config['authentication_method'] == 'token') {
            $defaults['authentication_value'] = 'api_token';
        }

        $config = self::fromConfig($config, $defaults, $required);
        $client = new static($config);
        $client->setDefaultHeaders(array(
                'Content-Type' => 'application/json; charset=utf-8',
            ));

        $plugin = new TogglClientAuthPlugin($config->get('authentication_key'), $config->get('authentication_value'));
        $client->addSubscriber($plugin);

        return $client;
    }

    public static function isValidWorkspaceId($workspaceId)
    {
        return is_numeric($workspaceId);
    }

    public function getSummaryReport($workspaceId, $params = array())
    {
        $defaults = array(
            'workspace_id' => $workspaceId,
            'user_agent' => 'jonathan.webb@acquia.com',
        );
        $paramString = '{+base_path}/summary?workspace_id={workspace_id}&user_agent={user_agent}';
        foreach(array_keys($params) as $param) {
            $paramString .= sprintf("&%s={%s}", $param, $param);
        }
        $variables = array_merge($defaults,$params);
        if (!self::isValidWorkspaceId($variables['workspace_id'])) {
            $message = sprintf("%s expects 'workspace_id' param to be an integer, but was provided a %s", __METHOD__, gettype($variables['workspace_id']));
            throw new \InvalidArgumentException($message);
        }
        $data = $this->sendGet($paramString, $variables);
        return $data;
    }
}
