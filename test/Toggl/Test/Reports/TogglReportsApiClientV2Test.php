<?php

namespace Toggl\Test\Reports;

use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response as HttpResponse;
use Toggl\Reports\TogglReportsApiClientV2;
use Toggl\Api\Response as ApiResponse;
use Toggl\Common\TogglClientAuthPlugin;


class TogglReportsApiClientV2Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Toggl\Reports\TogglReportsApiClientV2
     */
    public function getTogglReportsApiClient()
    {
        return TogglReportsApiClientV2::factory(array(
                'authentication_method' => 'email',
                'authentication_key' => 'test@example.com',
                'authentication_value' => 'api_token',
            ));
    }

    /**
     * Helper function that returns the event listener.
     *
     * @param \Toggl\Reports\TogglReportsApiClientV2 $client
     *
     * @return \Toggl\Common\TogglClientAuthPlugin
     *
     * @throws \UnexpectedValueException
     */
    public function getRegisteredAuthPlugin(TogglReportsApiClientV2 $client)
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
     * @param \Toggl\Reports\TogglReportsApiClientV2 $client
     * @param array $responseData
     */
    public function addMockResponse(TogglReportsApiClientV2 $client, array $responseData)
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
        TogglReportsApiClientV2::factory(array(
                'authentication_method' => 'email',
                'authentication_value' => 'test-password',
                'base_path' => '/reports/api/v2'
            ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequireAuthenticationValueForEmailAuth()
    {
        TogglReportsApiClientV2::factory(array(
                'authentication_method' => 'email',
                'authentication_key' => 'test-username',
                'base_path' => '/reports/api/v2'
            ));
    }

    public function testDontRequireAuthenticationValueForTokenAuth()
    {
        TogglReportsApiClientV2::factory(array(
                'authentication_method' => 'token',
                'authentication_key' => 'test-username',
                'base_path' => '/reports/api/v2'
            ));
    }

    public function testHasAuthPlugin()
    {
        $client = $this->getTogglReportsApiClient();
        $hasPlugin = (boolean) $this->getRegisteredAuthPlugin($client);
        $this->assertTrue($hasPlugin);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSummaryReportCallRequireNumericWorkspaceId()
    {
        $client = $this->getTogglReportsApiClient();
        $this->addMockResponse($client, array());
        $client->getSummaryReport("one");
    }

    public function testSummaryReportCall()
    {
        $client = $this->getTogglReportsApiClient();
        $workspaceId = 101;
        $responseData = array('test' => true); 
        $this->addMockResponse($client, $responseData);
        $projects = $client->getSummaryReport($workspaceId, array());
        $this->assertTrue(is_array($projects));
    }

}
