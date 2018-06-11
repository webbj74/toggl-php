<?php

namespace Toggl\Test\Api\Response;

use Toggl\Api\Response as ApiResponse;

class WorkspacesTest extends \PHPUnit_Framework_TestCase
{
    public static function getWorkspacesData($workspaceId = 101, $names = array("Sample Workspace 1", "Sample Workspace 2"))
    {
        $workspacesData = array();
        foreach($names as $name) {
            $workspacesData[] = WorkspaceTest::getWorkspaceData($workspaceId++, $name);
        }
        return $workspacesData;
    }

    /**
     * @expectedException GuzzleHttp\Exception\BadResponseException
     */
    public function testWorkspacesConstructor()
    {
        new ApiResponse\Workspaces("foo");
    }
}