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

    public function getWorkspaceProjects($workspaceId, $params = array())
    {
        $defaults = array(
            'workspace_id' => $workspaceId,
            'active' => 'both',
            'actual_hours' => 'false',
        );
        $variables = array_merge($defaults,$params);
        if (!is_numeric($variables['workspace_id'])) {
            $message = sprintf("%s expects 'workspace_id' param to be an integer, but was provided a %s", __METHOD__, gettype($variables['workspace_id']));
            throw new \InvalidArgumentException($message);
        }
        $active = $variables['active'];
        if (!((is_string($active) && in_array($active, array('true','false','both'))) || is_bool($active))) {
            $message = sprintf("%s expects 'active' param to be one of true/false/both, but was provided %s", __METHOD__, $active);
            throw new \InvalidArgumentException($message);
        }
        $actual_hours = $variables['actual_hours'];
        if (!((is_string($actual_hours) && in_array($actual_hours, array('true','false'))) || is_bool( $actual_hours))) {
            $message = sprintf("%s expects 'actual_hours' param to be one of true/false, but was provided a %s", __METHOD__, $actual_hours);
            throw new \InvalidArgumentException($message);
        }
        $data = $this->sendGet('{+base_path}/workspaces/{workspace_id}/projects?active={active}&actual_hours={actual_hours}', $variables);
        return new Response\Projects($data);
    }

    /**
     * PUT https://www.toggl.com/api/v8/projects/{project_id}
     * -d '{"project":{"name":"Changed the name","is_private":false,"cid":123398, "color": "6"}}'
     */
    public function updateProjectData($projectId, $data = array())
    {
        $variables = array(
            'project_id' => $projectId,
        );

        if (!is_numeric($variables['project_id'])) {
            $message = sprintf("%s expects 'project_id' param to be an integer, but was provided a %s", __METHOD__, gettype($variables['project_id']));
            throw new \InvalidArgumentException($message);
        }
        if (empty($data) || !is_array($data)) {
            $message = sprintf("%s expects 'data' to be an array, but was provided a %s", __METHOD__, gettype($data));
            throw new \InvalidArgumentException($message);
        }
        $data = $this->sendPut('{+base_path}/projects/{project_id}', $variables, json_encode($data));
        return new Response\Project($data);
    }
}
