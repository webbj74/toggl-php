<?php

namespace Toggl\Common;

use GuzzleHttp\Psr7\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds HTTP authentication for the TogglClient.
 */
class TogglClientAuthPlugin implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request.before_send' => array('onRequestBeforeSend', -1000)
        );
    }

    /**
     * Request before-send event handler.
     *
     * @param \Symfony\Component\EventDispatcher\EventSubscriberInterface $event
     */
    public function onRequestBeforeSend(EventSubscriberInterface $event)
    {
        $this->setAuth($event['request']);
    }

    /**
     * This method seems silly, but it will be really useful if / when the
     * authentication scheme becomes more complex. Separating it out from the
     * event handler allows us to test this code more easily.
     *
     * @param \GuzzleHttp\Psr7\Request $request
     *
     * @return \GuzzleHttp\Psr7\MessageTrait $trait
     */
    public function setAuth(Request $request)
    {
        return $request->withHeader(
          'Authorization',
          'Basic ' . base64_encode($this->username . ':' . $this->password)
        );
    }
}