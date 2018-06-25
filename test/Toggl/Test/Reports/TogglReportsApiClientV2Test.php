<?php

namespace Toggl\Test\Reports;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as HttpResponse;
use PHPUnit\Framework\TestCase;
use Toggl\Reports\TogglReportsApiClientV2;


class TogglReportsApiClientV2Test extends TestCase
{
    /**
     * @param array $responseData
     * @return \Toggl\Reports\TogglReportsApiClientV2
     */
    public function getTogglReportsApiClient($responseData = [])
    {
        $responseData = json_encode($responseData);
        $mock = new MockHandler([
          new HttpResponse(200, [], $responseData),
        ]);
        $handler = HandlerStack::create($mock);

        return TogglReportsApiClientV2::factory([
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
        TogglReportsApiClientV2::factory([
                'authentication_method' => 'email',
                'authentication_value' => 'test-password',
                'base_path' => '/reports/api/v2'
            ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequireAuthenticationValueForEmailAuth()
    {
        TogglReportsApiClientV2::factory([
                'authentication_method' => 'email',
                'authentication_key' => 'test-username',
                'base_path' => '/reports/api/v2'
            ]);
    }

    public function testDontRequireAuthenticationValueForTokenAuth()
    {
        TogglReportsApiClientV2::factory([
                'authentication_method' => 'token',
                'authentication_key' => 'test-username',
                'base_path' => '/reports/api/v2'
            ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSummaryReportCallRequireNumericWorkspaceId()
    {
        $client = $this->getTogglReportsApiClient();
        $client->getSummaryReport("one");
    }

    public function testSummaryReportCall()
    {
        $responseData = ['test' => true];
        $client = $this->getTogglReportsApiClient($responseData);
        $workspaceId = 101;
        $projects = $client->getSummaryReport($workspaceId, []);
        $this->assertTrue(is_array($projects));
    }
}

