<?php

namespace Toggl\Test\Api;

use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response as HttpResponse;
use Toggl\Api\TogglApiClientV8;
use Toggl\Api\Response as ApiResponse;
use Toggl\Common\TogglClientAuthPlugin;

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
        TogglApiClientV8::factory(array(
                'authentication_method' => 'email',
                'authentication_key' => 'test-username',
                'base_path' => '/api/v8'
            ));
    }

    public function testDontRequireAuthenticationValueForTokenAuth()
    {
        TogglApiClientV8::factory(array(
                'authentication_method' => 'token',
                'authentication_key' => 'test-username',
                'base_path' => '/api/v8'
            ));
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
        $responseData = array(
            'since' => 123456789,
            'data' => array(
                'id' => 123,
                'email' => 'test@example.com',
            ));
        $this->addMockResponse($client, $responseData);
        $me = $client->me();
        $this->assertTrue($me instanceof ApiResponse\Me);
    }

    private function getWorkspaceData($name = "Sample Workspace")
    {
        return array(
            "id" => 3134975,
            "name" => $name,
            "premium" => true,
            "admin" => true,
            "default_hourly_rate" => 50,
            "default_currency" => "USD",
            "only_admins_may_create_projects" => false,
            "only_admins_see_billable_rates" => true,
            "rounding" => 1,
            "rounding_minutes" => 15,
            "at" => "2013-08-28T16:22:21+00:00",
            "logo_url" => "my_logo.png"
        );
    }

    private function getProjectData($workspaceId, $name = "Sample Project")
    {
        return array(
            "id" => 3134975,
            "wid" => $workspaceId,
            "cid" => 987,
            "name" => $name,
            "billable" => false,
            "is_private" => true,
            "active" => true,
            "at" => "2013-08-28T16:22:21+00:00",
        );
    }

    public function testWorkspacesCall()
    {
        $client = $this->getTogglApiClient();
        $responseData = array(
            $this->getWorkspaceData("Sample Workspace 1"),
            $this->getWorkspaceData("Sample Workspace 2")
        );
        $this->addMockResponse($client, $responseData);
        $workspaces = $client->getWorkspaces();
        $this->assertTrue($workspaces instanceof ApiResponse\Workspaces);
        $this->assertTrue($workspaces["Sample Workspace 1"] instanceof ApiResponse\Workspace);
        $this->assertEquals('"Sample Workspace 1","Sample Workspace 2"', "{$workspaces}");
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
        $responseData = array(
            $this->getProjectData($workspaceId, "Sample Project 1"),
            $this->getProjectData($workspaceId, "Sample Project 2")
        );
        $this->addMockResponse($client, $responseData);
        $projects = $client->getWorkspaceProjects($workspaceId);
        $this->assertTrue($projects instanceof ApiResponse\Projects);
        $this->assertTrue($projects["Sample Project 1"] instanceof ApiResponse\Project);
        $this->assertEquals($workspaceId, $projects["Sample Project 1"]['wid']);
        $this->assertEquals('"Sample Project 1","Sample Project 2"', "{$projects}");

    }
}
