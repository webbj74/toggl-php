<?php

namespace Toggl\Test\Api\Response;

use Toggl\Api\Response as ApiResponse;

class WorkspaceTest extends \PHPUnit_Framework_TestCase
{
    public static function getWorkspaceData($workspaceId = 101, $name = "Sample Workspace")
    {
        return array(
            "id" => $workspaceId,
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

    /**
     * @expectedException Guzzle\Common\Exception\UnexpectedValueException
     */
    public function testWorkspaceConstructor()
    {
        new ApiResponse\Workspace("foo");
    }

}
