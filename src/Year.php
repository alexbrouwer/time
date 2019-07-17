<?php declare(strict_types=1);

namespace PAR\Time;

use DateTimeInterface;
use PAR\Core\ComparableInterface;
use PAR\Core\Exception\ClassMismatchException;
use PAR\Core\ObjectInterface;
use PAR\Enum\EnumMap;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Exception\InvalidArgumentException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAccessor;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalField;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A year in the ISO-8601 calendar system, such as 2007.
 */
final class Year implements Temporal, ObjectInterface, ComparableInterface
{
    public const MIN_VALUE = -999999999;
    public const MAX_VALUE = 999999999;

    /**
     * @var EnumMap|null
     */
    private static $units;

    /**
     * @var int
     */
    private $value;

    /**
     * Obtains an instance of Year from a temporal object.
     *
     * This obtains a year based on the specified temporal. A TemporalAccessor represents an arbitrary set of date and
     * time information, which this factory converts to an instance of Year.
     *
     * The conversion extracts the year field. The extraction is only permitted if the temporal object has an ISO
     * chronology, or can be converted to a LocalDate.
     *
     * @param TemporalAccessor $temporal The temporal object to convert
     *
     * @return self
     */
    public static function from(TemporalAccessor $temporal): self
    {
        return new self(
            $temporal->get(ChronoField::YEAR())
        );
    }

    /**
     * Checks if the year is a leap year, according to the ISO calendar system rules.
     *
     * @param int $year
     *
     * @return bool
     */
    public static function isLeapYear(int $year): bool
    {
        if ($year === 0) {
            return false;
        }
        $dt = Factory::createDate($year);

        return (int)$dt->format('L') === 1;
    }

    /**
     * Obtains the current year from the system clock in the default time-zone.
     *
     * @return self
     */
    public static function now(): self
    {
        $now = Factory::now();

        return self::ofNative($now);
    }

    /**
     * Obtains an instance of Year.
     *
     * @param int $year The year to represent
     *
     * @return self
     * @throws InvalidArgumentException If year is outside of range
     */
    public static function of(int $year): self
    {
        return new self($year);
    }

    /**
     * Obtains an instance of DayOfWeek from an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return self
     */
    public static function ofNative(DateTimeInterface $dateTime): self
    {
        $year = ChronoField::YEAR()->getFromNative($dateTime);

        return self::of($year);
    }

    /**
     * Obtains an instance of Year from a text string such as 2007.
     *
     * @param string $text The text to parse
     *
     * @return self
     * @throws InvalidArgumentException If text could not be parsed or value is outside of range
     */
    public static function parse(string $text): self
    {
        Assert::regex($text, '/^[-+]?\d{1,}$/');

        return new self((int)$text);
    }

    /**
     * Combines this year with a day-of-year to create a LocalDate.
     *
     * @param int $dayOfYear The day-of-year to use, from 1 to 365-366
     *
     * @return LocalDate
     */
    public function atDay(int $dayOfYear): LocalDate
    {
        return LocalDate::ofYearDay($this->getValue(), $dayOfYear);
    }

    /**
     * Combines this year with a month to create a YearMonth.
     *
     * @param Month $month The month-of-year to use
     *
     * @return YearMonth
     */
    public function atMonth(Month $month): YearMonth
    {
        return YearMonth::of($this->getValue(), $month->getValue());
    }

    /**
     * @inheritDoc
     */
    public function compareTo(ComparableInterface $other): int
    {
        if ($other instanceof self && get_class($other) === static::class) {
            return $this->value - $other->value;
        }

        throw ClassMismatchException::expectedInstance($this, $other);
    }

    /**
     * @inheritDoc
     */
    public function equals($other): bool
    {
        if ($other instanceof self && get_class($other) === static::class) {
            return $this->value === $other->value;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        if ($this->supportsField($field)) {
            return $this->getValue();
        }

        throw UnsupportedTemporalTypeException::forField($field);
    }

    /**
     * Gets the year value.
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Checks if this year is after the specified year.
     *
     * @param Year $year The other year to compare to
     *
     * @return bool
     */
    public function isAfter(Year $year): bool
    {
        return $this->compareTo($year) > 0;
    }

    /**
     * Checks if this year is before the specified year.
     *
     * @param Year $year The other year to compare to
     *
     * @return bool
     */
    public function isBefore(Year $year): bool
    {
        return $this->compareTo($year) < 0;
    }

    /**
     * Checks if the year is a leap year, according to the ISO calendar system rules.
     *
     * @see Year::isLeapYear
     *
     * @return bool
     */
    public function isLeap(): bool
    {
        return static::isLeapYear($this->value);
    }

    /**
     * Gets the length of this year in days.
     *
     * @return int 365 or 366
     */
    public function length(): int
    {
        return $this->isLeap() ? 366 : 365;
    }

    /**
     * @inheritDoc
     *
     * @return self
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
     * Returns a copy of this Year with the specified number of years subtracted.
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
     *
     * @return self
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): self
    {
        if (!$this->supportsUnit($unit)) {
            throw UnsupportedTemporalTypeException::forUnit($unit);
        }

        $years = $amountToAdd;
        if (ChronoUnit::DECADES()->equals($unit)) {
            $years *= 10;
        }

        if (ChronoUnit::CENTURIES()->equals($unit)) {
            $years *= 100;
        }

        if (ChronoUnit::MILLENNIA()->equals($unit)) {
            $years *= 1000;
        }

        return new self($this->value + $years);
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
     * Returns a copy of this Year with the specified number of years added.
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
     * Checks if the specified field is supported.
     *
     * Supported:
     * - ChronoField::YEAR()
     *
     * @param TemporalField $field The field to check
     *
     * @return bool
     */
    public function supportsField(TemporalField $field): bool
    {
        return ChronoField::YEAR()->equals($field);
    }

    /**
     * @inheritDoc
     */
    public function supportsUnit(TemporalUnit $unit): bool
    {
        if ($unit instanceof ChronoUnit) {
            return $this->getUnitMap()->containsKey($unit);
        }

        return $unit->isSupportedBy($this);
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return (string)$this->value;
    }

    /**
     * @return EnumMap
     */
    private function getUnitMap(): EnumMap
    {
        if (!self::$units) {
            $map = EnumMap::for(ChronoUnit::class, 'boolean', false);
            $map->put(ChronoUnit::YEARS(), true);
            $map->put(ChronoUnit::DECADES(), true);
            $map->put(ChronoUnit::CENTURIES(), true);
            $map->put(ChronoUnit::MILLENNIA(), true);
            self::$units = $map;
        }

        return self::$units;
    }

    /**
     * @param int $value
     */
    private function __construct(int $value)
    {
        Assert::range($value, self::MIN_VALUE, self::MAX_VALUE);

        $this->value = $value;
    }
}
