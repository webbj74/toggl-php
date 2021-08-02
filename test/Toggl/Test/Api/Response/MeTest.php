<?php

namespace Toggl\Test\Api\Response;

use PHPUnit\Framework\TestCase;
use Toggl\Api\Response as ApiResponse;

class MeTest extends TestCase
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

    public function testMeConstructorRequireArray()
    {
        $this->expectException(\UnexpectedValueException::class);
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
