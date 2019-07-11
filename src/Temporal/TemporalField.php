<?php

namespace PAR\Time\Temporal;

use DateTimeInterface;
use PAR\Core\ObjectInterface;

interface TemporalField extends ObjectInterface
{
    /**
     * Gets the unit that the field is measured in.
     *
     * The unit of the field is the period that varies within the range. For example, in the field 'MonthOfYear', the
     * unit is 'Months'.
     *
     * @see TemporalField::getRangeUnit
     *
     * @return TemporalUnit
     */
    public function getBaseUnit();

    /**
     * Gets the range that the field is bound by.
     *
     * The range of the field is the period that the field varies within. For example, in the field 'MonthOfYear', the
     * range is 'Years'.
     *
     * @see TemporalField::getBaseUnit
     *
     * @return TemporalUnit
     */
    public function getRangeUnit();

    /**
     * Obtains the value of current field from a native DateTimeInterface object.
     *
     * @param DateTimeInterface $dateTime The DateTimeInterface object to obtain value from
     *
     * @return int
     */
    public function getFromNative(DateTimeInterface $dateTime): int;

    /**
     * Gets the range of valid values for the field.
     *
     * @return ValueRange
     */
    public function range(): ValueRange;

    /**
     * Checks if this field is supported by the temporal object.
     *
     * @param TemporalAccessor $temporalAccessor
     *
     * @return bool
     */
    public function isSupportedBy(TemporalAccessor $temporalAccessor): bool;
}
