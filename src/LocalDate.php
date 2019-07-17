<?php declare(strict_types=1);

namespace PAR\Time;

use DateTimeImmutable;
use DateTimeInterface;
use PAR\Core\ComparableInterface;
use PAR\Core\Exception\ClassMismatchException;
use PAR\Core\Helper\InstanceHelper;
use PAR\Core\ObjectInterface;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Exception\InvalidDateException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalField;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A date without a time-zone in the ISO-8601 calendar system, such as 2007-12-03.
 */
final class LocalDate implements Temporal, ObjectInterface, ComparableInterface
{
    /**
     * @var int
     */
    private $year;

    /**
     * @var int
     */
    private $month;

    /**
     * @var int
     */
    private $dayOfMonth;

    /**
     * Obtains an instance of LocalDate from a year, month and day.
     *
     * @param int $year       The year to represent
     * @param int $month      The month-of-year to represent
     * @param int $dayOfMonth The day-of-month to represent
     *
     * @return self
     */
    public static function of(int $year, int $month, int $dayOfMonth): self
    {
        return new self($year, $month, $dayOfMonth);
    }

    /**
     * Obtains an instance of LocalDate from an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return self
     */
    public static function ofNative(DateTimeInterface $dateTime): self
    {
        return new self(
            ChronoField::YEAR()->getFromNative($dateTime),
            ChronoField::MONTH_OF_YEAR()->getFromNative($dateTime),
            ChronoField::DAY_OF_MONTH()->getFromNative($dateTime)
        );
    }

    /**
     * Obtains an instance of LocalDate from a year and day-of-year.
     *
     * @param int $year      The year to represent
     * @param int $dayOfYear The day-of-year to represent, from 1 to 366
     *
     * @return self
     */
    public static function ofYearDay(int $year, int $dayOfYear): self
    {
        Assert::range($dayOfYear, 1, Year::of($year)->length());

        $firstDayOfYear = new self($year, 1, 1);

        return $firstDayOfYear->plusDays($dayOfYear);
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $dayOfMonth
     */
    public function __construct(int $year, int $month, int $dayOfMonth)
    {
        $this->year = Year::of($year)->getValue();
        $this->month = Month::of($month)->getValue();

        if (!Factory::isValidDate($year, $month, $dayOfMonth)) {
            throw InvalidDateException::of($year, $month, $dayOfMonth);
        }

        $this->dayOfMonth = $dayOfMonth;
    }

    /**
     * @inheritDoc
     */
    public function compareTo(ComparableInterface $other): int
    {
        if ($other instanceof self && get_class($other) === static::class) {
            $yearDiff = $this->year - $other->year;
            if ($yearDiff !== 0) {
                return $yearDiff;
            }

            $monthDiff = $this->month - $other->month;
            if ($monthDiff !== 0) {
                return $monthDiff;
            }

            return $this->dayOfMonth - $other->dayOfMonth;
        }

        throw ClassMismatchException::expectedInstance($this, $other);
    }

    /**
     * @inheritDoc
     */
    public function equals($other): bool
    {
        if ($other instanceof self && get_class($other) === static::class) {
            return $this->year === $other->year
                && $this->month === $other->month
                && $this->dayOfMonth === $other->dayOfMonth;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        if ($this->supportsField($field)) {
            // TODO implement get()
        }

        throw UnsupportedTemporalTypeException::forField($field);
    }

    /**
     * @inheritDoc
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit): self
    {
        return $this->plus($amountToSubtract * -1, $unit);
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function minusAmount(TemporalAmount $amount): self
    {
        $temporal = $amount->subtractFrom($this);

        /** @var self $temporal */
        return $temporal;
    }

    /**
     * Returns a copy of this LocalDate with the specified number of years subtracted.
     *
     * @param int $years The years to subtract, may be negative
     *
     * @return self
     */
    public function minusYears(int $years): self
    {
        return $this->minus($years, ChronoUnit::YEARS());
    }

    /**
     * @inheritDoc
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): self
    {
        if (!$this->supportsUnit($unit)) {
            throw UnsupportedTemporalTypeException::forUnit($unit);
        }

        if ($amountToAdd === 0) {
            return $this;
        }

        $interval = $unit->getDuration()
            ->multipliedBy($amountToAdd)
            ->toDateInterval();

        $dt = $this->toNative();

        return self::ofNative($dt->add($interval));
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function plusAmount(TemporalAmount $amount): self
    {
        $temporal = $amount->addTo($this);

        /** @var self $temporal */
        return $temporal;
    }

    /**
     * Returns a copy of this LocalDate with the specified number of days added.
     *
     * @param int $days The days to add, may be negative
     *
     * @return self
     */
    public function plusDays(int $days): self
    {
        return $this->plus($days, ChronoUnit::DAYS());
    }

    /**
     * Returns a copy of this LocalDate with the specified number of years added.
     *
     * @param int $years The years to add, may be negative
     *
     * @return self
     */
    public function plusYears(int $years): self
    {
        return $this->plus($years, ChronoUnit::YEARS());
    }

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        return $field->isDateBased();
    }

    /**
     * @inheritDoc
     */
    public function supportsUnit(TemporalUnit $unit): bool
    {
        return $unit->isDateBased()
            && InstanceHelper::isAnyOf(
                $unit,
                [
                    ChronoUnit::DAYS(),
                    ChronoUnit::WEEKS(),
                    ChronoUnit::MONTHS(),
                    ChronoUnit::YEARS(),
                    ChronoUnit::DECADES(),
                    ChronoUnit::CENTURIES(),
                    ChronoUnit::MILLENNIA(),
                ]
            );
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return sprintf('%d-%02d-%02d', $this->year, $this->month, $this->dayOfMonth);
    }

    private function toNative(): DateTimeImmutable
    {
        return Factory::createDate($this->year, $this->month, $this->dayOfMonth);
    }
}
