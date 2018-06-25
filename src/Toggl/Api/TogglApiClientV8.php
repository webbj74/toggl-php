<?php

namespace Toggl\Api;

use Toggl\Common\TogglClientAuthPlugin;

class TogglApiClientV8 extends TogglApiClient
{
    const BASE_PATH = '/api/v8';

    /**
     * {@inheritdoc}
     *
     * @return \Toggl\Common\TogglClient
     */
    public static function factory($config = [])
    {
        $required = array(
            'authentication_method',
            'authentication_key',
            'authentication_value',
            'base_path',
        );

        $defaults = array(
            'base_uri' => self::BASE_URL,
            'base_path' => self::BASE_PATH,
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'auth' => [],
        );

        if (isset($config['authentication_method']) && $config['authentication_method'] == 'token') {
            $defaults['authentication_value'] = 'api_token';
        }

        $config = $config + $defaults;
        if (array_diff($required, array_keys($config))) {
          throw new \InvalidArgumentException("Config is missing required key(s)." . print_r(array_diff($required, array_keys($config)),1));
        }

        $config['auth'] = [
           $config['authentication_key'],
           $config['authentication_value'],
           'Basic',
        ];
        unset($config['authentication_key'], $config['authentication_value'], $config['authentication_method']);

        $client = new static($config);

        return $client;
    }

    public function me()
    {
        $data = $this->sendGet(self::BASE_PATH . '/me');
        return new Response\Me($data);
    }

    public static function isValidWorkspaceId($workspaceId)
    {
        return is_numeric($workspaceId);
    }

    public function getWorkspaces()
    {
        $data = $this->sendGet(self::BASE_PATH . '/workspaces');
        return new Response\Workspaces($data);
    }

    public function getWorkspaceProjects($workspaceId, $params = array())
    {
        $defaults = array(
            'workspace_id' => $workspaceId,
            'active' => 'both',
            'actual_hours' => 'false',
        );
        $variables = array_merge($defaults, $params);
        if (!self::isValidWorkspaceId($variables['workspace_id'])) {
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

        $path = sprintf("%s/workspaces/%s/projects?active=%s&actual_hours=%s",
            self::BASE_PATH,
            $variables['workspace_id'],
            $variables['active'],
            $variables['actual_hours']
          );
        $data = $this->sendGet($path, $variables);
        return new Response\Projects($data);
    }

    /**
     * Get workspace users
     *
     * @param string|int $workspaceId
     * @return \Toggl\Api\Response\WorkspaceUsers
     * @throws \InvalidArgumentException
     * 
     * @see https://github.com/toggl/toggl_api_docs/blob/master/chapters/workspace_users.md#get-workspace-users
     */
    public function getWorkspaceUsers($workspaceId)
    {
        $variables = array(
            'workspace_id' => $workspaceId
        );

        if (!self::isValidWorkspaceId($variables['workspace_id'])) {
            $message = sprintf("%s expects 'workspace_id' param to be an integer, but was provided a %s", __METHOD__, gettype($variables['workspace_id']));
            throw new \InvalidArgumentException($message);
        }

        $data = $this->sendGet(self::BASE_PATH . '/workspaces/{workspace_id}/workspace_users', $variables);
        return new Response\WorkspaceUsers($data);
    }

    /**
     * Create project
     *
     * @param array $data
     * @return \Toggl\Api\Response\Project
     * @throws \InvalidArgumentException
     *
     * @see https://github.com/toggl/toggl_api_docs/blob/master/chapters/projects.md#create-project
     */
    public function createProject($data)
    {
        if (empty($data) || !is_array($data)) {
            $message = sprintf("%s expects 'data' to be an array, but was provided a %s", __METHOD__, gettype($data));
            throw new \InvalidArgumentException($message);
        }

        // formatting object
        if (empty($data['project'])) {
            $data = array('project' => $data);
        }

        // further validation
        if (empty($data['project']['name']) || empty($data['project']['wid'])) {
            $message = sprintf("%s expects 'data' to contain a name and wid", __METHOD__);
            throw new \InvalidArgumentException($message);
        }

        $data = $this->sendPost(self::BASE_PATH . '/projects', [], json_encode($data));
        return new Response\Project($data);
    }

    /**
     * Update project data
     *
     * @param int $projectId
     * @param array $data
     * @return \Toggl\Api\Response\Project
     * @throws \InvalidArgumentException
     *
     * @see https://github.com/toggl/toggl_api_docs/blob/master/chapters/projects.md#update-project-data
     */
    public function updateProjectData($projectId, $data = [])
    {
        $variables = [
            'project_id' => $projectId,
        ];

        if (!is_numeric($variables['project_id'])) {
            $message = sprintf("%s expects 'project_id' param to be an integer, but was provided a %s", __METHOD__, gettype($variables['project_id']));
            throw new \InvalidArgumentException($message);
        }
        if (empty($data) || !is_array($data)) {
            $message = sprintf("%s expects 'data' to be an array, but was provided a %s", __METHOD__, gettype($data));
            throw new \InvalidArgumentException($message);
        }
        $data = $this->sendPut(self::BASE_PATH .'/projects/'. $projectId, $variables, json_encode($data));
        return new Response\Project($data);
    }
}
