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
    public function testWorkspaceUsersConstructorRequiresArray()
    {
        new ApiResponse\WorkspaceUsers("foo");
    }

    public function testWorkspaceUsersConstructor()
    {
        $users = new ApiResponse\WorkspaceUsers(self::getWorkspaceUsersData(101, array("Sample User 1", "Sample User 2")));
        $this->assertTrue($users instanceof ApiResponse\WorkspaceUsers);
        foreach($users as $user) {
            $this->assertTrue($user instanceof ApiResponse\WorkspaceUser);
        }
        $this->assertEquals('"Sample User 1","Sample User 2"', "{$users}");
    }

}