<?php

namespace Toggl\Api\Response;

use Guzzle\Common\Exception\UnexpectedValueException;

class Project extends \ArrayObject
{
    public function __construct($data)
    {
        if (!is_array($data)) {
            throw new UnexpectedValueException('Expecting API response to be an array');
        }
        else {
            parent::__construct($data);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this['name'];
    }

    public function isPrivate()
    {
        return $this['is_private'];
    }
}