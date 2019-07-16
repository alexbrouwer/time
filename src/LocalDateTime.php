<?php declare(strict_types=1);

namespace PAR\Time;

use PAR\Core\ComparableInterface;
use PAR\Core\ObjectInterface;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalField;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A date-time without a time-zone in the ISO-8601 calendar system, such as 2007-12-03T10:15:30.
 */
final class LocalDateTime implements Temporal, ObjectInterface, ComparableInterface
{
    /**
     * @inheritDoc
     */
    public function compareTo(ComparableInterface $other): int
    {
        // TODO: Implement compareTo() method.
    }

    /**
     * @inheritDoc
     */
    public function equals($other): bool
    {
        // TODO: Implement equals() method.
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        // TODO: Implement toString() method.
    }

    /**
     * @inheritDoc
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): Temporal
    {
        // TODO: Implement plus() method.
    }

    /**
     * @inheritDoc
     */
    public function plusAmount(TemporalAmount $amount): Temporal
    {
        // TODO: Implement plusAmount() method.
    }

    /**
     * @inheritDoc
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit): Temporal
    {
        // TODO: Implement minus() method.
    }

    /**
     * @inheritDoc
     */
    public function minusAmount(TemporalAmount $amount): Temporal
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

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        // TODO: Implement supportsField() method.
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        // TODO: Implement get() method.
    }
}
