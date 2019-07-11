<?php declare(strict_types=1);

namespace PAR\Time;

use PAR\Time\Temporal\TemporalAmount;

/**
 * A date-based amount of time in the ISO-8601 calendar system, such as '2 years, 3 months and 4 days'.
 */
final class Period implements TemporalAmount
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
        return 'P0D';
    }
}
