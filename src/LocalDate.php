<?php declare(strict_types=1);

namespace PAR\Time;

use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalField;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A date without a time-zone in the ISO-8601 calendar system, such as 2007-12-03.
 */
final class LocalDate implements Temporal
{
    /**
     * Obtains an instance of LocalDate from a year, month and day.
     *
     * @param Year  $year       The year to represent
     * @param Month $month      The month-of-year to represent
     * @param int   $dayOfMonth The day-of-month to represent
     *
     * @return LocalDate
     */
    public static function of(Year $year, Month $month, int $dayOfMonth): self
    {
    }

    /**
     * Obtains an instance of LocalDate from a year and day-of-year.
     *
     * @param Year $year      The year to represent
     * @param int  $dayOfYear The day-of-year to represent, from 1 to 366
     *
     * @return LocalDate
     */
    public static function ofYearDay(Year $year, int $dayOfYear): self
    {
        Assert::range($dayOfYear, 1, $year->length());
    }

    /**
     * @inheritDoc
     */
    public function equals($other): bool
    {
        // TODO: Implement equals() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        // TODO: Implement toString() method.
        return '0000-00-00';
    }

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        // TODO: Implement supportsField() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        // TODO: Implement get() method.
        throw UnsupportedTemporalTypeException::forField($field);
    }

    /**
     * @inheritDoc
     */
    public function plus(int $amountToAdd, TemporalUnit $unit)
    {
        // TODO: Implement plus() method.
    }

    /**
     * @inheritDoc
     */
    public function plusAmount(TemporalAmount $amount)
    {
        // TODO: Implement plusAmount() method.
    }

    /**
     * @inheritDoc
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit)
    {
        // TODO: Implement minus() method.
    }

    /**
     * @inheritDoc
     */
    public function minusAmount(TemporalAmount $amount)
    {
        // TODO: Implement minusAmount() method.
    }

    /**
     * @inheritDoc
     */
    public function supportsUnit(TemporalUnit $unit): bool
    {
        // TODO: Implement supportsUnit() method.
    }

}
