<?php declare(strict_types=1);

namespace PAR\Time;

use PAR\Core\Helper\InstanceHelper;
use PAR\Core\ObjectInterface;
use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Exception\InvalidFormatException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A date-based amount of time in the ISO-8601 calendar system, such as '2 years, 3 months and 4 days'.
 */
final class Period implements TemporalAmount, ObjectInterface
{
    /**
     * @var int
     */
    private $years;
    /**
     * @var int
     */
    private $months;

    /**
     * @var int
     */
    private $days;

    /**
     * Obtains an instance of Period from a temporal amount.
     *
     * @param TemporalAmount $amount
     *
     * @return self
     */
    public static function from(TemporalAmount $amount): self
    {
        if ($amount instanceof self) {
            return $amount;
        }

        $period = self::zero();
        foreach ($amount->getUnits() as $unit) {
            $value = $amount->get($unit);

            if (ChronoUnit::YEARS()->equals($unit)) {
                $period = $period->withYears($value);
            }
            if (ChronoUnit::MONTHS()->equals($unit)) {
                $period = $period->withMonths($value);
            }
            if (ChronoUnit::DAYS()->equals($unit)) {
                $period = $period->withDays($value);
            }
        }

        return $period;
    }

    /**
     * Obtains a Period representing a number of years, months and days.
     *
     * @param int $years  The amount of years, may be negative
     * @param int $months The amount of months, may be negative
     * @param int $days   The amount of days, may be negative
     *
     * @return self
     */
    public static function of(int $years, int $months, int $days): self
    {
        return new self($years, $months, $days);
    }

    /**
     * Obtains a Period representing a number of days.
     *
     * The resulting period will have the specified days. The years and months units will be zero.
     *
     * @param int $days The number of days, positive or negative
     *
     * @return self
     */
    public static function ofDays(int $days): self
    {
        return new self(0, 0, $days);
    }

    /**
     * Obtains a Period representing a number of months.
     *
     * The resulting period will have the specified months. The years and days units will be zero.
     *
     * @param int $months The number of months, positive or negative
     *
     * @return self
     */
    public static function ofMonths(int $months): self
    {
        return new self(0, $months, 0);
    }

    /**
     * Obtains a Period representing a number of weeks.
     *
     * The resulting period will be day-based, with the amount of days equal to the number of weeks multiplied by 7. The years and months units will be zero.
     *
     * @param int $weeks
     *
     * @return self
     */
    public static function ofWeeks(int $weeks): self
    {
        return self::ofDays($weeks * 7);
    }

    /**
     * Obtains a Period representing a number of years.
     *
     * The resulting period will have the specified years. The months and days units will be zero.
     *
     * @param int $years The number of years, positive or negative
     *
     * @return self
     */
    public static function ofYears(int $years): self
    {
        return new self($years, 0, 0);
    }

    /**
     * Obtains a Period from a text string such as PnYnMnD.
     *
     * This will parse the string produced by toString() which is based on the ISO-8601 period formats PnYnMnD and PnW.
     *
     * @param string $text The text to parse
     *
     * @return self
     * @throws InvalidFormatException If the text cannot be parsed to a period
     */
    public static function parse(string $text): self
    {
        if (!preg_match('/^(?<signed>-|\+)?P(?!$)(?<years>[-\+]?\d+Y)?(?<months>[-\+]?\d+M)?(?<weeks>[-\+]?\d+W)?(?<days>[-\+]?\d+D)?$/', $text, $matches)) {
            throw InvalidFormatException::of('ISO-8601 period', $text);
        }

        $period = self::zero();
        if ($text === 'P0D') {
            return $period;
        }

        foreach ($matches as $name => $match) {
            if ($match === '' || !is_string($name)) {
                continue;
            }
            switch ($name) {
                case 'years':
                    $period = $period->plusYears((int)$match);
                    break;
                case 'months':
                    $period = $period->plusMonths((int)$match);
                    break;
                case 'weeks':
                    $period = $period->plusDays((int)$match * 7);
                    break;
                case 'days':
                    $period = $period->plusDays((int)$match);
                    break;
                default:
                    break;
            }
        }

        if (isset($matches['signed']) && $matches['signed'] === '-') {
            $period = $period->negated();
        }

        return $period;
    }

    /**
     * Obtains a Period representing zero length.
     *
     * @return self
     */
    public static function zero(): self
    {
        return new self(0, 0, 0);
    }

    /**
     * @inheritDoc
     */
    public function addTo(Temporal $temporal): Temporal
    {
        foreach ($this->getUnits() as $unit) {
            $temporal = $temporal->plus($this->get($unit), $unit);
        }

        return $temporal;
    }

    /**
     * @inheritDoc
     */
    public function equals($other): bool
    {
        if ($other instanceof self && get_class($other) === static::class) {
            return $this->years === $other->years
                && $this->months === $other->months
                && $this->days === $other->days;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalUnit $unit): int
    {
        $this->assertUnit($unit);

        if (ChronoUnit::YEARS()->equals($unit)) {
            return $this->getYears();
        }

        if (ChronoUnit::MONTHS()->equals($unit)) {
            return $this->getMonths();
        }

        return $this->getDays();
    }

    /**
     * Gets the amount of days of this period.
     *
     * @return int
     */
    public function getDays(): int
    {
        return $this->days;
    }

    /**
     * Gets the amount of months of this period.
     *
     * @return int
     */
    public function getMonths(): int
    {
        return $this->months;
    }

    /**
     * @inheritDoc
     */
    public function getUnits(): array
    {
        return [
            ChronoUnit::YEARS(),
            ChronoUnit::MONTHS(),
            ChronoUnit::DAYS(),
        ];
    }

    /**
     * Gets the amount of years of this period.
     *
     * @return int
     */
    public function getYears(): int
    {
        return $this->years;
    }

    /**
     * Checks if any of the three units of this period are negative.
     *
     * This checks whether the years, months or days units are less than zero.
     *
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->years < 0 || $this->months < 0 || $this->days < 0;
    }

    /**
     * Checks if all three units of this period are zero.
     *
     * A zero period has the value zero for the years, months and days units.
     *
     * @return bool
     */
    public function isZero(): bool
    {
        return $this->equals(self::zero());
    }

    /**
     * Returns a copy of this period with the specified period subtracted.
     *
     * This operates separately on the years, months and days. No normalization is performed.
     *
     * For example, "1 year, 6 months and 3 days" minus "2 years, 2 months and 2 days" returns "-1 years, 4 months and 1 day".
     *
     * @param TemporalAmount $amount The amount to subtract
     *
     * @return Period
     */
    public function minusAmount(TemporalAmount $amount): self
    {
        if (!$amount instanceof self) {
            $amount = self::from($amount);
        }

        $changed = $this;
        foreach ($amount->getUnits() as $unit) {
            $this->assertUnit($unit);
            $value = $amount->get($unit);

            if (ChronoUnit::YEARS()->equals($unit)) {
                $changed = $changed->minusYears($value);
            }
            if (ChronoUnit::MONTHS()->equals($unit)) {
                $changed = $changed->minusMonths($value);
            }
            if (ChronoUnit::DAYS()->equals($unit)) {
                $changed = $changed->minusDays($value);
            }
        }

        return $changed;
    }

    /**
     * Returns a copy of this period with the specified days subtracted.
     *
     * This subtracts the amount from the days unit in a copy of this period. The years and months units are
     * unaffected. For example, "1 year, 6 months and 3 days" minus 2 days returns "1 year, 6 months and 1 day".
     *
     * @param int $days The days to subtract, positive or negative
     *
     * @return Period
     */
    public function minusDays(int $days): self
    {
        return $this->plusDays($days * -1);
    }

    /**
     * Returns a copy of this period with the specified months subtracted.
     *
     * This subtracts the amount from the months unit in a copy of this period. The years and days units are unaffected.
     * For example, "1 year, 6 months and 3 days" minus 2 months returns "1 year, 4 months and 3 days".
     *
     * @param int $months The months to subtract, positive or negative
     *
     * @return Period
     */
    public function minusMonths(int $months): self
    {
        return $this->plusMonths($months * -1);
    }

    /**
     * Returns a copy of this period with the specified years subtracted.
     *
     * @param int $years The years to subtract, positive or negative
     *
     * @return Period
     */
    public function minusYears(int $years): self
    {
        return $this->plusYears($years * -1);
    }

    /**
     * Returns a new instance with each element in this period multiplied by the specified value.
     *
     * @param int $multiplier
     *
     * @return Period
     */
    public function multipliedBy(int $multiplier): self
    {
        return new self(
            $this->years * $multiplier,
            $this->months * $multiplier,
            $this->days * $multiplier
        );
    }

    /**
     * Returns a new instance with each amount in this period negated.
     *
     * This returns a period with each of the years, months and days units individually negated. For example, a period
     * of "2 years, -3 months and 4 days" will be negated to "-2 years, 3 months and -4 days". No normalization is
     * performed.
     *
     * @return Period
     */
    public function negated(): self
    {
        return new self(
            $this->years * -1,
            $this->months * -1,
            $this->days * -1
        );
    }

    /**
     * Returns a copy of this period with the years and months normalized.
     *
     * This normalizes the years and months units, leaving the days unit unchanged. The months unit is adjusted to have
     * an absolute value less than 12, with the years unit being adjusted to compensate. For example, a period of
     * "1 Year and 15 months" will be normalized to "2 years and 3 months".
     *
     * The sign of the years and months units will be the same after normalization. For example, a period of
     * "1 year and -25 months" will be normalized to "-1 year and -1 month".
     *
     * @return Period
     */
    public function normalized(): self
    {
        if ($this->months >= 12) {
            $overflow = (int)floor($this->months / 12);
            $years = $this->years + $overflow;
            $months = $this->months - ($overflow * 12);

            return new self($years, $months, $this->days);
        }

        return $this;
    }

    /**
     * Returns a copy of this period with the specified period added.
     *
     * This operates separately on the years, months and days. No normalization is performed.
     *
     * For example, "1 year, 6 months and 3 days" plus "2 years, 2 months and 2 days" returns "3 years, 8 months and 5 days".
     *
     * @param TemporalAmount $amount The amount to add
     *
     * @return Period
     */
    public function plusAmount(TemporalAmount $amount): self
    {
        if (!$amount instanceof self) {
            $amount = self::from($amount);
        }

        $changed = $this;
        foreach ($amount->getUnits() as $unit) {
            $this->assertUnit($unit);
            $value = $amount->get($unit);

            if (ChronoUnit::YEARS()->equals($unit)) {
                $changed = $changed->plusYears($value);
            }
            if (ChronoUnit::MONTHS()->equals($unit)) {
                $changed = $changed->plusMonths($value);
            }
            if (ChronoUnit::DAYS()->equals($unit)) {
                $changed = $changed->plusDays($value);
            }
        }

        return $changed;
    }

    /**
     * Returns a copy of this period with the specified days added.
     *
     * This adds the amount to the days unit in a copy of this period. The years and months units are unaffected.
     * For example, "1 year, 6 months and 3 days" plus 2 days returns "1 year, 6 months and 5 days".
     *
     * @param int $days The days to add, positive or negative
     *
     * @return Period
     */
    public function plusDays(int $days): self
    {
        return $this->withDays($this->days + $days);
    }

    /**
     * Returns a copy of this period with the specified months added.
     *
     * This adds the amount to the months unit in a copy of this period. The years and days units are unaffected.
     * For example, "1 year, 6 months and 3 days" plus 2 months returns "1 year, 8 months and 3 days".
     *
     * @param int $months The months to add, positive or negative
     *
     * @return Period
     */
    public function plusMonths(int $months): self
    {
        return $this->withMonths($this->months + $months);
    }

    /**
     * Returns a copy of this period with the specified years added.
     *
     * This adds the amount to the years unit in a copy of this period. The months and days units are unaffected.
     * For example, "1 year, 6 months and 3 days" plus 2 years returns "3 years, 6 months and 3 days".
     *
     * @param int $years The years to add, positive or negative
     *
     * @return Period
     */
    public function plusYears(int $years): self
    {
        return $this->withYears($this->years + $years);
    }

    /**
     * @inheritDoc
     */
    public function subtractFrom(Temporal $temporal): Temporal
    {
        foreach ($this->getUnits() as $unit) {
            $temporal = $temporal->minus($this->get($unit), $unit);
        }

        return $temporal;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        $allNegative = $this->years < 0 && $this->months < 0 && $this->days < 0;

        $parts = [
            $allNegative ? '-' : '',
            'P',
        ];

        if ($this->years !== 0) {
            $parts[] = sprintf('%dY', $allNegative ? $this->years * -1 : $this->years);
        }
        if ($this->months !== 0) {
            $parts[] = sprintf('%dM', $allNegative ? $this->months * -1 : $this->months);
        }
        if ($this->days !== 0) {
            $parts[] = sprintf('%dD', $allNegative ? $this->days * -1 : $this->days);
        }
        if ($this->isZero()) {
            $parts[] = '0D';
        }

        return implode('', $parts);
    }

    /**
     * Gets the total number of months in this period.
     *
     * This returns the total number of months in the period by multiplying the number of years by 12 and adding the number of months.
     *
     * @return int
     */
    public function toTotalMonths(): int
    {
        return $this->getMonths() + ($this->getYears() * 12);
    }

    /**
     * Returns a copy of this period with the specified amount of days.
     *
     * This sets the amount of the days unit in a copy of this period. The years and months units are unaffected.
     *
     * @param int $days The days to represent, may be negative
     *
     * @return Period
     */
    public function withDays(int $days): self
    {
        return new self($this->years, $this->months, $days);
    }

    /**
     * Returns a copy of this period with the specified amount of months.
     *
     * This sets the amount of the months unit in a copy of this period. The years and days units are unaffected.
     *
     * @param int $months The months to represent, may be negative
     *
     * @return Period
     */
    public function withMonths(int $months): self
    {
        return new self($this->years, $months, $this->days);
    }

    /**
     * Returns a copy of this period with the specified amount of years.
     *
     * This sets the amount of the years unit in a copy of this period. The months and days units are unaffected.
     *
     * @param int $years The years to represent, may be negative
     *
     * @return Period
     */
    public function withYears(int $years): self
    {
        return new self($years, $this->months, $this->days);
    }

    private function assertUnit(TemporalUnit $unit): void
    {
        if (!InstanceHelper::isAnyOf($unit, $this->getUnits())) {
            throw UnsupportedTemporalTypeException::forUnit($unit);
        }
    }

    /**
     * @param int $years
     * @param int $months
     * @param int $days
     */
    private function __construct(int $years, int $months, int $days)
    {
        $this->years = $years;
        $this->months = $months;
        $this->days = $days;
    }
}
