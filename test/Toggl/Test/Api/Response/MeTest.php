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
     * @expectedException \GuzzleHttp\Exception\BadResponseException
     */
    public function testMeConstructorRequireArray()
    {
        new ApiResponse\Me("foo");
    }

    public function testMeConstructor()
    {
        $data = self::getUserData();
        $me = new ApiResponse\Me($data);
        $this->assertTrue($me instanceof ApiResponse\Me);
    }

    public function testMeConstructorFlatArray()
    {
        $data = self::getUserData();
        $me = new ApiResponse\Me($data['data']);
        $this->assertTrue($me instanceof ApiResponse\Me);
    }
}
