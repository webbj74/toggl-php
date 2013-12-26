<?php

namespace Toggl\Test\Api\Response;

use Toggl\Api\Response as ApiResponse;

class WorkspaceUsersTest extends \PHPUnit_Framework_TestCase
{
    public static function getWorkspaceUsersData($workspaceId = 101, $names = array("Sample User 1", "Sample User 2"))
    {
        $usersData = array();
        foreach($names as $name) {
            $usersData[] = WorkspaceUserTest::getWorkspaceUserData($workspaceId, $name);
        }
        return $usersData;
    }

    /**
     * @expectedException Guzzle\Common\Exception\UnexpectedValueException
     */
    public function testProjectsConstructor()
    {
        new ApiResponse\WorkspaceUsers("foo");
    }
}