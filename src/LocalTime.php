<?php declare(strict_types=1);

namespace PAR\Time;

use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalField;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A time without a time-zone in the ISO-8601 calendar system, such as 10:15:30.
 */
final class LocalTime implements Temporal
{
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
        return '00:00:00';
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
