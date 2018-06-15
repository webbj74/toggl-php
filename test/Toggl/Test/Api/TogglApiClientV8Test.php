<?php

namespace Toggl\Test\Api;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as HttpResponse;
use Toggl\Api\TogglApiClientV8;
use Toggl\Api\Response as ApiResponse;
use Toggl\Common\TogglClientAuthPlugin;
use Toggl\Test\Api\Response\MeTest;
use Toggl\Test\Api\Response\ProjectTest;
use Toggl\Test\Api\Response\ProjectsTest;
use Toggl\Test\Api\Response\WorkspacesTest;
use Toggl\Test\Api\Response\WorkspaceUsersTest;


class TogglApiClientV8Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Toggl\Api\TogglApiClientV8
     */
    public function getTogglApiClient()
    {
        return TogglApiClientV8::factory(array(
                'authentication_method' => 'email',
                'authentication_key' => 'test@example.com',
                'authentication_value' => 'api_token',
            ));
    }

    /**
     * Helper function that returns the event listener.
     *
     * @param \Toggl\Api\TogglApiClientV8 $client
     *
     * @return \Toggl\Common\TogglClientAuthPlugin
     *
     * @throws \UnexpectedValueException
     */
    public function getRegisteredAuthPlugin(TogglApiClientV8 $client)
    {
        $listeners = $client->getEventDispatcher()->getListeners('request.before_send');
        foreach ($listeners as $listener) {
            if (isset($listener[0]) && $listener[0] instanceof TogglClientAuthPlugin) {
                return $listener[0];
            }
        }

        throw new \UnexpectedValueException('Expecting subscriber Toggl\Common\TogglClientAuthPlugin to be registered');
    }

    /**
     * @param \Toggl\Api\TogglApiClientV8 $client
     * @param array $responseData
     */
    public function addMockResponse(TogglApiClientV8 $client, array $responseData)
    {
        $mock = new MockPlugin();

        $response = new HttpResponse(200);
        $response->setBody(json_encode($responseData));

        $mock->addResponse($response);
        $client->addSubscriber($mock);
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
        TogglApiClientV8::factory([
                'authentication_method' => 'token',
                'authentication_key' => 'test-username',
                'base_path' => '/api/v8',
            ]);
    }

    public function testHasAuthPlugin()
    {
        $client = $this->getTogglApiClient();
        $hasPlugin = (boolean) $this->getRegisteredAuthPlugin($client);
        $this->assertTrue($hasPlugin);
    }

    public function testMeCall()
    {
        $client = $this->getTogglApiClient();
        $responseData = MeTest::getUserData('test@example.com');
        $this->addMockResponse($client, $responseData);
        $me = $client->me();
        $this->assertTrue($me instanceof ApiResponse\Me);
        $this->assertEquals('test@example.com', "{$me}");
    }

    public function testWorkspacesCall()
    {
        $client = $this->getTogglApiClient();
        $responseData = WorkspacesTest::getWorkspacesData(101, array("Sample Workspace 1","Sample Workspace 2"));
        $this->addMockResponse($client, $responseData);
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
        $this->addMockResponse($client, array());
        $client->getWorkspaceProjects("one");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWorkspaceProjectsCallRequireEnumActive()
    {
        $client = $this->getTogglApiClient();
        $this->addMockResponse($client, array());
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
        $this->addMockResponse($client, array());
        $client->getWorkspaceProjects(1, array(
                'actual_hours' => 'foo'
            ));
    }

    public function testWorkspaceProjectsCall()
    {
        $client = $this->getTogglApiClient();
        $workspaceId = 101;
        $responseData = ProjectsTest::getProjectsData($workspaceId, array("Sample Project 1", "Sample Project 2"));
        $this->addMockResponse($client, $responseData);
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
        $this->addMockResponse($client, array());
        $client->getWorkspaceUsers("one");
    }

    public function testWorkspaceUsersCall()
    {
        $client = $this->getTogglApiClient();
        $workspaceId = 101;
        $responseData = WorkspaceUsersTest::getWorkspaceUsersData($workspaceId, array("Sample Name 1", "Sample Name 2"));
        $this->addMockResponse($client, $responseData);
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
        $this->addMockResponse($client, array());
        $client->createProject("foo");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateProjectCallRequireArrayDataElements()
    {
        $client = $this->getTogglApiClient();
        $this->addMockResponse($client, array());
        $client->createProject(array('foo'));
    }

    public function testCreateProjectCall()
    {
        $workspaceId = 101;
        $responseData = ProjectTest::getProjectData($workspaceId, "Sample Project 1");

        $client = $this->getTogglApiClient();
        $this->addMockResponse($client, $responseData);
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
        $this->addMockResponse($client, array());
        $client->updateProjectData("foo", array('project' => array('is_private' => false)));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateProjectDataCallRequireNonEmptyData()
    {
        $client = $this->getTogglApiClient();
        $this->addMockResponse($client, array());
        $client->updateProjectData(1, array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateProjectDataCallRequireArrayData()
    {
        $client = $this->getTogglApiClient();
        $this->addMockResponse($client, array());
        $client->updateProjectData(1, "foo");
    }

    public function testUpdateProjectDataCall()
    {
        $workspaceId = 101;
        $responseData = ProjectTest::getProjectData($workspaceId, "Sample Project 1");

        $client = $this->getTogglApiClient();
        $this->addMockResponse($client, $responseData);
        $project = $client->updateProjectData(101, array('project' => array('is_private' => false)));
        $this->assertTrue($project instanceof ApiResponse\Project);
    }

}
