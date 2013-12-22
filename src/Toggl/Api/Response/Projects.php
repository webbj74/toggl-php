<?php

namespace Toggl\Api\Response;

use Guzzle\Common\Exception\UnexpectedValueException;

class Projects extends \ArrayObject
{
    public function __construct($data)
    {
        if (!is_array($data)) {
            throw new UnexpectedValueException('Expecting API response to be an array');
        }
        $projects = array();
        foreach ($data as $project) {
            if (isset($project['name']))
                $projects[$project['name']] = new Project($project);
        }
        parent::__construct($projects);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $names = array();
        foreach (array_keys($this) as $name) {
            $names[] = '"' . addcslashes($name, '"');
        }
        return implode(',', $names);
    }
}