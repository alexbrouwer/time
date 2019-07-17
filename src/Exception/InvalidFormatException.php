<?php

namespace PAR\Time\Exception;

class InvalidFormatException extends InvalidArgumentException
{
    public static function of(string $expectedType, string $actual): self
    {
        return new self(
            sprintf(
                'Invalid %s string, got %s',
                $expectedType,
                $actual
            )
        );
    }
}
