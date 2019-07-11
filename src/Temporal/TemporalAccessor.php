<?php

namespace PAR\Time\Temporal;

use PAR\Core\ObjectInterface;
use PAR\Time\Exception\UnsupportedTemporalTypeException;

interface TemporalAccessor extends ObjectInterface
{
    /**
     * Checks if the specified field is supported.
     *
     * @param TemporalField $field The field to check
     *
     * @return bool
     */
    public function supportsField(TemporalField $field): bool;

    /**
     * Gets the value of the specified field as an int.
     *
     * @param TemporalField $field The field to get
     *
     * @return int
     * @throws UnsupportedTemporalTypeException
     */
    public function get(TemporalField $field): int;
}
