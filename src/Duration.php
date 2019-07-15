<?php

namespace PAR\Time;

use PAR\Core\ComparableInterface;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A time-based amount of time, such as '34.5 seconds'.
 */
final class Duration implements TemporalAmount, ComparableInterface
{
    public static function of(int $seconds, int $micros = 0): self
    {

    }

    /**
     * Determines if this object equals provided value.
     *
     * @param mixed $other The other value to compare with.
     *
     * @return bool
     */
    public function equals($other): bool
    {
//         TODO actual implementation
//        if ($other instanceof self && get_class($other) === static::class) {
//            return $this->value === $other->value;
//        }

        return false;
    }

    public function toString(): string
    {
        // TODO actual implementation
        return 'PT0S';
    }

    public function compareTo(ComparableInterface $other): int
    {
        // TODO: Implement compareTo() method.
        return 0;
    }

    public function isNegative(): bool
    {
        // TODO: Implement isNegative() method.
        return false;
    }

    public function negated()
    {
        // TODO: Implement negated() method.
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addTo(Temporal $temporal): Temporal
    {
        if ($this->isZero()) {
            return $temporal;
        }

        foreach ($this->getUnits() as $unit) {
            $temporal = $temporal->plus($this->get($unit), $unit);
        }

        return $temporal;
    }

    /**
     * Returns the list of units uniquely defining the value of this TemporalAmount. The list of TemporalUnits is
     * defined by the implementation class. The list is a snapshot of the units at the time getUnits is called and is
     * not mutable. The units are ordered from longest duration to the shortest duration of the unit.
     *
     * @return TemporalUnit[]
     */
    public function getUnits(): array
    {
        // TODO: Implement getUnits() method.
    }

    /**
     * Returns the value of the requested unit. The units returned from getUnits() uniquely define the value of the
     * TemporalAmount. A value must be returned for each unit listed in getUnits.
     *
     * Supported units are:
     * - ChronoUnit::SECONDS()
     * - ChronoUnit::MICROS()
     *
     * @param TemporalUnit $unit
     *
     * @return int
     * @throws UnsupportedTemporalTypeException If the unit is not supported
     */
    public function get(TemporalUnit $unit): int
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritDoc
     */
    public function subtractFrom(Temporal $temporal)
    {
        // TODO: Implement subtractFrom() method.
    }

    public function isZero(): bool
    {
        return false;
    }

    public function multipliedBy(int $multiplier): self
    {
        return $this;
    }
}
