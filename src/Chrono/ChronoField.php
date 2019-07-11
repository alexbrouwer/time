<?php

namespace PAR\Time\Chrono;

use DateTimeInterface;
use PAR\Enum\Enum;

/**
 * A standard set of fields.
 *
 * This set of fields provide field-based access to manipulate a date, time or date-time.
 *
 * @method static self DAY_OF_WEEK()
 */
final class ChronoField extends Enum
{
    protected const DAY_OF_WEEK = ['DAYS', 'WEEKS', 'N'];
    protected const DAY_OF_MONTH = ['DAYS', 'MONTHS', 'j'];

    /**
     * @var string
     */
    private $baseUnit;

    /**
     * @var string
     */
    private $rangeUnit;

    /**
     * @var string
     */
    private $format;

    /**
     * @param string $baseUnit
     * @param string $rangeUnit
     * @param string $format
     */
    protected function __construct(string $baseUnit, string $rangeUnit, string $format)
    {
        $this->baseUnit = $baseUnit;
        $this->rangeUnit = $rangeUnit;
        $this->format = $format;
    }

    /**
     * Gets the unit that the field is measured in.
     *
     * The unit of the field is the period that varies within the range. For example, in the field 'MonthOfYear', the
     * unit is 'Months'.
     *
     * @see ChronoField::getRangeUnit
     *
     * @return ChronoUnit
     */
    public function getBaseUnit(): ChronoUnit
    {
        return ChronoUnit::valueOf($this->baseUnit);
    }

    /**
     * Gets the range that the field is bound by.
     *
     * The range of the field is the period that the field varies within. For example, in the field 'MonthOfYear', the
     * range is 'Years'.
     *
     * @see ChronoField::getBaseUnit
     *
     * @return ChronoUnit
     */
    public function getRangeUnit(): ChronoUnit
    {
        return ChronoUnit::valueOf($this->rangeUnit);
    }

    /**
     * @param DateTimeInterface $dateTime
     *
     * @return int
     */
    public function getFromNative(DateTimeInterface $dateTime): int
    {
        return (int)$dateTime->format($this->format);
    }
}
