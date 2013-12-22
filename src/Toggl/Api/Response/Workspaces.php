<?php

namespace Toggl\Api\Response;

use Guzzle\Common\Exception\UnexpectedValueException;

class Workspaces extends \ArrayObject
{
    public function __construct($data)
    {
        if (!is_array($data)) {
            throw new UnexpectedValueException('Expecting API response to be an array');
        }
        $workspaces = array();
        foreach ($data as $workspace) {
            if (isset($workspace['name']))
            $workspaces[$workspace['name']] = new Workspace($workspace);
        }
        parent::__construct($workspaces);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $names = array();
        foreach (array_keys($this->getArrayCopy()) as $name) {
            $names[] = '"' . addcslashes($name, '"') . '"';
        }
        return implode(',', $names);
    }
}