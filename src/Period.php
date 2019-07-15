<?php declare(strict_types=1);

namespace PAR\Time;

use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A date-based amount of time in the ISO-8601 calendar system, such as '2 years, 3 months and 4 days'.
 */
final class Period implements TemporalAmount
{
    /**
     * Obtains an instance of Period from a temporal amount.
     *
     * @param TemporalAmount $amount
     *
     * @return Period
     */
    public static function from(TemporalAmount $amount): self
    {
        if ($amount instanceof self) {
            return $amount;
        }

        $period = self::zero();
        foreach ($amount->getUnits() as $unit) {
            if (ChronoUnit::YEARS()->equals($unit)) {
                $period = $period->withYears($amount->get($unit));
            }
            if (ChronoUnit::MONTHS()->equals($unit)) {
                $period = $period->withMonths($amount->get($unit));
            }
            if (ChronoUnit::DAYS()->equals($unit)) {
                $period = $period->withDays($amount->get($unit));
            }
        }

        return $period;
    }

    public static function zero(): self
    {
        return new self(0, 0, 0);
    }

    private function __construct(int $years, int $months, int $days)
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
        return 'P0D';
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
        // TODO: Implement addTo() method.
    }

    /**
     * @inheritDoc
     */
    public function subtractFrom(Temporal $temporal): Temporal
    {
        // TODO: Implement subtractFrom() method.
    }

    /**
     * @inheritDoc
     */
    public function getUnits(): array
    {
        // TODO: Implement getUnits() method.
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalUnit $unit): int
    {
        // TODO: Implement get() method.
    }
}
