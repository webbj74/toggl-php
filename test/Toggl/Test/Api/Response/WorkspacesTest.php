<?php

namespace Toggl\Test\Api\Response;

use PHPUnit\Framework\TestCase;
use Toggl\Api\Response as ApiResponse;

class WorkspacesTest extends TestCase
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
     * @expectedException \UnexpectedValueException
     */
    public function testWorkspacesConstructor()
    {
        new ApiResponse\Workspaces("foo");
    }
}