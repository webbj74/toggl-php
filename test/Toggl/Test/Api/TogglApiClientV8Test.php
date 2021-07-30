<?php

namespace Toggl\Test\Api;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as HttpResponse;
use PHPUnit\Framework\TestCase;
use Toggl\Api\TogglApiClientV8;
use Toggl\Api\Response as ApiResponse;
use Toggl\Test\Api\Response\MeTest;
use Toggl\Test\Api\Response\ProjectTest;
use Toggl\Test\Api\Response\ProjectsTest;
use Toggl\Test\Api\Response\WorkspacesTest;
use Toggl\Test\Api\Response\WorkspaceUsersTest;


class TogglApiClientV8Test extends TestCase
{
    /**
     * @param array $responseData
     *
     * @return \Toggl\Api\TogglApiClientV8
     */
    public function getTogglApiClient($responseData = [])
    {
        $responseData = json_encode($responseData);
        $mock = new MockHandler([
          new HttpResponse(200, [], $responseData),
        ]);
        $handler = HandlerStack::create($mock);

        return TogglApiClientV8::factory([
                'authentication_method' => 'email',
                'authentication_key' => 'test@example.com',
                'authentication_value' => 'api_token',
                'handler' => $handler,
            ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequireAuthenticationKey()
    {
        TogglApiClientV8::factory(array(
                'authentication_method' => 'email',
                'authentication_value' => 'test-password',
                'base_path' => '/api/v8'
            ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequireAuthenticationValueForEmailAuth()
    {
        TogglApiClientV8::factory([
                'authentication_method' => 'email',
                'authentication_key' => 'test-username',
                'base_path' => '/api/v8'
            ]);
    }

    public function testDontRequireAuthenticationValueForTokenAuth()
    {
        $this->assertInstanceOf(TogglApiClientV8::class,
            TogglApiClientV8::factory([
                'authentication_method' => 'token',
                'authentication_key' => 'test-username',
                'base_path' => '/api/v8',
            ])
        );
    }

    public function testMeCall()
    {
        $responseData = MeTest::getUserData('test@example.com');
        $client = $this->getTogglApiClient($responseData);
        $me = $client->me();
        $this->assertTrue($me instanceof ApiResponse\Me);
        $this->assertEquals('test@example.com', "{$me}");
    }

    public function testWorkspacesCall()
    {
        $responseData = WorkspacesTest::getWorkspacesData(101, array("Sample Workspace 1","Sample Workspace 2"));
        $client = $this->getTogglApiClient($responseData);
        $workspaces = $client->getWorkspaces();
        $this->assertTrue($workspaces instanceof ApiResponse\Workspaces);
        $this->assertTrue($workspaces["Sample Workspace 1"] instanceof ApiResponse\Workspace);
        $this->assertEquals('"Sample Workspace 1","Sample Workspace 2"', "{$workspaces}");
        $this->assertEquals('Sample Workspace 1', "{$workspaces["Sample Workspace 1"]}");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWorkspaceProjectsCallRequireNumericWorkspaceId()
    {
        $client = $this->getTogglApiClient();
        $client->getWorkspaceProjects("one");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWorkspaceProjectsCallRequireEnumActive()
    {
        $client = $this->getTogglApiClient();
        $client->getWorkspaceProjects(1, array(
                'active' => 'foo'
            ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWorkspaceProjectsCallRequireEnumActualHours()
    {
        $client = $this->getTogglApiClient();
        $client->getWorkspaceProjects(1, array(
                'actual_hours' => 'foo'
            ));
    }

    public function testWorkspaceProjectsCall()
    {

        $workspaceId = 101;
        $responseData = ProjectsTest::getProjectsData($workspaceId, array("Sample Project 1", "Sample Project 2"));
        $client = $this->getTogglApiClient($responseData);
        $projects = $client->getWorkspaceProjects($workspaceId);
        $this->assertTrue($projects instanceof ApiResponse\Projects);
        $this->assertTrue($projects["Sample Project 1"] instanceof ApiResponse\Project);
        $this->assertEquals($workspaceId, $projects["Sample Project 1"]['wid']);
        $this->assertEquals('"Sample Project 1","Sample Project 2"', "{$projects}");
        $this->assertEquals('Sample Project 1', "{$projects["Sample Project 1"]}");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWorkspaceUsersCallRequireNumericWorkspaceId()
    {
        $client = $this->getTogglApiClient();
        $client->getWorkspaceUsers("one");
    }

    public function testWorkspaceUsersCall()
    {

        $workspaceId = 101;
        $responseData = WorkspaceUsersTest::getWorkspaceUsersData($workspaceId, array("Sample Name 1", "Sample Name 2"));
        $client = $this->getTogglApiClient($responseData);
        $users = $client->getWorkspaceUsers($workspaceId);
        $this->assertTrue($users instanceof ApiResponse\WorkspaceUsers);
        $this->assertTrue($users["Sample Name 1"] instanceof ApiResponse\WorkspaceUser);
        $this->assertEquals($workspaceId, $users["Sample Name 1"]['wid']);
        $this->assertEquals('"Sample Name 1","Sample Name 2"', "{$users}");
        $this->assertEquals('Sample Name 1', "{$users["Sample Name 1"]}");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateProjectCallRequireArrayData()
    {
        $client = $this->getTogglApiClient();
        $client->createProject("foo");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateProjectCallRequireArrayDataElements()
    {
        $client = $this->getTogglApiClient();
        $client->createProject(array('foo'));
    }

    public function testCreateProjectCall()
    {
        $workspaceId = 101;
        $responseData = ProjectTest::getProjectData($workspaceId, "Sample Project 1");
        $client = $this->getTogglApiClient($responseData);
        $project = $client->createProject(array(
                'wid' => $workspaceId,
                'name' => 'Sample Project 1'
            ));
        $this->assertTrue($project instanceof ApiResponse\Project);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateProjectDataCallRequireNumericProject()
    {
        $client = $this->getTogglApiClient();
        $client->updateProjectData("foo", array('project' => array('is_private' => false)));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateProjectDataCallRequireNonEmptyData()
    {
        $client = $this->getTogglApiClient();
        $client->updateProjectData(1, array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateProjectDataCallRequireArrayData()
    {
        $client = $this->getTogglApiClient();
        $client->updateProjectData(1, "foo");
    }

    public function testUpdateProjectDataCall()
    {
        $workspaceId = 101;
        $responseData = ProjectTest::getProjectData($workspaceId, "Sample Project 1");
        $client = $this->getTogglApiClient($responseData);
        $project = $client->updateProjectData(101, array('project' => array('is_private' => false)));
        $this->assertTrue($project instanceof ApiResponse\Project);
    }

}
