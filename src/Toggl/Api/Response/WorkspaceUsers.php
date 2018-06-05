<?php
/**
 * https://www.toggl.com/api/v8/workspaces/777/workspace_users
 */

namespace Toggl\Api\Response;

class WorkspaceUsers extends \ArrayObject
{
    public function __construct($data)
    {
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Expecting API response to be an array');
        }
        $users = array();
        foreach ($data as $user) {
            if (isset($user['name'])) {
                $users[$user['name']] = new WorkspaceUser($user);
            }
        }
        parent::__construct($users);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $names = array();
        foreach (array_keys($this->getArrayCopy()) as $name) {
            $names[] = '"' . addcslashes($name, '"') . '"';
        }
        return implode(',', $names);
    }
}