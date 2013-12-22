<?php

namespace Toggl\Test\Api\Response;

use Toggl\Api\Response as ApiResponse;

class ProjectsTest extends \PHPUnit_Framework_TestCase
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
     * @expectedException Guzzle\Common\Exception\UnexpectedValueException
     */
    public function testProjectsConstructor()
    {
        new ApiResponse\Projects("foo");
    }
}