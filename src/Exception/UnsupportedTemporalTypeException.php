<?php

namespace PAR\Time\Exception;

use PAR\Time\Temporal\TemporalField;

final class UnsupportedTemporalTypeException extends DateTimeException
{
    public static function forField(TemporalField $field): self
    {
        return new self(
            sprintf(
                'Unsupported field: %s',
                $field->toString()
            )
        );
    }
}
