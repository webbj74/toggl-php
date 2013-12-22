<?php

namespace Toggl\Test\Api\Response;

use Toggl\Api\Response as ApiResponse;

class MeTest extends \PHPUnit_Framework_TestCase
{
    public static function getUserData($email = 'test@example.com')
    {
        return array(
            'since' => 123456789,
            'data' => array(
                'id' => 123,
                'email' => $email,
            ));
    }

    /**
     * @expectedException Guzzle\Common\Exception\UnexpectedValueException
     */
    public function testMeConstructor()
    {
        new ApiResponse\Workspace("foo");
    }

}
