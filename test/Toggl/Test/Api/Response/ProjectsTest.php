<?php

namespace Toggl\Test\Api\Response;

use PHPUnit\Framework\TestCase;
use Toggl\Api\Response as ApiResponse;

class ProjectsTest extends TestCase
{
    public static function getProjectsData($workspaceId = 101, $names = array("Sample Project 1", "Sample Project 2"))
    {
        $projectsData = array();
        foreach($names as $name) {
            $projectsData[] = ProjectTest::getProjectData($workspaceId, $name);
        }
        return $projectsData;
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testProjectsConstructor()
    {
        new ApiResponse\Projects("foo");
    }
}