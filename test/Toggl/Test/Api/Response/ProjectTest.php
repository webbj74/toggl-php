<?php

namespace Toggl\Test\Api\Response;

use Toggl\Api\Response as ApiResponse;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    public static function getProjectData($workspaceId = 101, $name = "Sample Project")
    {
        return array(
            "id" => 101,
            "wid" => $workspaceId,
            "cid" => 987,
            "name" => $name,
            "billable" => false,
            "is_private" => true,
            "active" => true,
            "at" => "2013-08-28T16:22:21+00:00",
        );
    }

    /**
     * @expectedException \GuzzleHttp\Exception\BadResponseException
     */
    public function testProjectConstructor()
    {
        new ApiResponse\Project("foo");
    }

    public function testProjectConstructorWithAbstractedData()
    {
        $project = new ApiResponse\Project(array('data' => self::getProjectData()));
        $this->assertTrue($project->isPrivate());
    }

    public function testProjectIsPrivate()
    {
        $project = new ApiResponse\Project(self::getProjectData());
        $this->assertTrue($project->isPrivate());
    }
}