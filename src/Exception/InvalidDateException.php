<?php

namespace PAR\Time\Exception;

class InvalidDateException extends InvalidArgumentException
{
    public static function of(int $year, int $month, int $dayOfMonth): self
    {
        return new self(
            sprintf('Invalid date: %d-%d-%d', $year, $month, $dayOfMonth)
        );
    }
}
