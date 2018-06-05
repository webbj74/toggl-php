<?php

namespace Toggl\Api\Response;

class Workspace extends \ArrayObject
{
    public function __construct($data)
    {
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Expecting API response to be an array');
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
}