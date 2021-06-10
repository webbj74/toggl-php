<?php
/**
 * https://api.track.toggl.com/api/v8/workspaces/777/workspace_users
 */

namespace Toggl\Api\Response;

class WorkspaceUser extends \ArrayObject
{

    public function __construct($data)
    {
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Expecting API response to be an array');
        } else {
            parent::__construct($data);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this['name'];
    }
}
