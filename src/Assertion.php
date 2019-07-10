<?php

namespace PAR\Time;

use Assert\Assertion as BaseAssertion;
use PAR\Time\Exception\InvalidArgumentException;

class Assertion extends BaseAssertion
{
    protected static $exceptionClass = InvalidArgumentException::class;
}
