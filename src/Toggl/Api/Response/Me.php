<?php

namespace Toggl\Api\Response;

use Guzzle\Common\Exception\UnexpectedValueException;

class Me extends \ArrayObject
{
    public function __construct($data)
    {
        if (!is_array($data)) {
            throw new UnexpectedValueException('Expecting response to /me to be an array');
        }

        // Flatten the V8 response in a sane way
        if(isset($data['data'])) {
            if (isset($data['since'])) {
                $data['data']['since'] = $data['since'];
            }
            parent::__construct($data['data']);
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
        return $this['email'];
    }
}