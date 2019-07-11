<?php

namespace PAR\Time;

use PAR\Time\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert as BaseAssert;

class Assert extends BaseAssert
{
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidArgumentException($message);
    }
}
