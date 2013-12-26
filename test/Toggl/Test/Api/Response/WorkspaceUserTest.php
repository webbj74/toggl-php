<?php

namespace Toggl\Test\Api\Response;

use Toggl\Api\Response as ApiResponse;

class WorkspaceUserTest extends \PHPUnit_Framework_TestCase
{
    public static function getWorkspaceUserData($workspaceId = 101, $name = "Sample User", $active = true)
    {
        $user = array(
            "id" => 101,
            "uid" => 201,
            "wid" => $workspaceId,
            "admin" => false,
            "active" => $active,
            "email" => "user@noreply.local",
            "at" => "2013-08-28T16:22:21+00:00",
            "name" => $name,
            "invite_url" => "https://toggl.com/user/accept_invitation?code=deadbeef0123456789acbdef"
        );
        if ($active) {
            unset($user['invite_url']);
        }
        return $user;
    }

    /**
     * @expectedException Guzzle\Common\Exception\UnexpectedValueException
     */
    public function testProjectConstructor()
    {
        new ApiResponse\WorkspaceUser("foo");
    }

}