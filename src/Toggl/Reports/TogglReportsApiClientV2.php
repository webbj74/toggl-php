<?php

namespace Toggl\Reports;

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
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'auth' => [],
        ];

        if (isset($config['authentication_method']) && $config['authentication_method'] == 'token') {
            $defaults['authentication_value'] = 'api_token';
        }

        $config = $config + $defaults;
        if (array_diff($required, array_keys($config))) {
          throw new \InvalidArgumentException("Config is missing required key(s).");
        }
        $client = new static($config);

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

   /**
    * Validate configuration keys, and add default values where missing.
    *
    * @param array $config
    *   Configuration values to apply.
    * @param array $defaults
    *   Default parameters.
    * @param array $required
    *   Required parameter names.
    *
    * @return array
    *   Configuration array.
    *
    * @throws \InvalidArgumentException if a parameter is missing.
    */
    protected static function fromConfig(
        array $config = [],
        array $defaults = [],
        array $required = []
    ) {
        $data = $config + $defaults;
        if ($missing = array_diff($required, array_keys($data))) {
            throw new \InvalidArgumentException(
                'Config is missing the following keys: ' .
                implode(', ', $missing));
        }

        return $data;
    }

}
