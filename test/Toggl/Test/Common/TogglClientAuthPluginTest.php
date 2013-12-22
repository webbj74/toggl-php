<?php

namespace Toggl\Test\Common;

use Toggl\Common\TogglClientAuthPlugin;

class TogglClientAuthPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Toggl\Common\TogglClientAuthPlugin
     */
    public function getAuthPlugin()
    {
        return new TogglClientAuthPlugin('test-username', 'test-password');
    }

    public function testGetters()
    {
        $plugin = $this->getAuthPlugin();
        $this->assertEquals('test-username', $plugin->getUsername());
        $this->assertEquals('test-password', $plugin->getPassword());
    }
}
