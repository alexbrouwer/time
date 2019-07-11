<?php

namespace PAR\Time;

use PAR\Core\ComparableInterface;
use PAR\Time\Temporal\TemporalAmount;

/**
 * A time-based amount of time, such as '34.5 seconds'.
 */
final class Duration implements TemporalAmount, ComparableInterface
{
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
}
