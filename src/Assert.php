<?php

namespace PAR\Time;

use PAR\Time\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert as BaseAssert;

class Assert extends BaseAssert
{
    /**
     * @param string $message
     *
     * @throws InvalidArgumentException
     */
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidArgumentException($message);
    }
}
