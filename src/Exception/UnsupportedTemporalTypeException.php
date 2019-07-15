<?php

namespace PAR\Time\Exception;

use PAR\Time\Temporal\TemporalField;
use PAR\Time\Temporal\TemporalUnit;

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

    public static function forUnit(TemporalUnit $unit): self
    {
        return new self(
            sprintf(
                'Unsupported unit: %s',
                $unit->toString()
            )
        );
    }
}
