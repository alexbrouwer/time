<?php declare(strict_types=1);

namespace PAR\Time;

use DateTimeInterface;
use PAR\Core\ComparableInterface;
use PAR\Core\Exception\ClassMismatchException;
use PAR\Core\ObjectInterface;
use PAR\Enum\EnumMap;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Exception\InvalidDateException;
use PAR\Time\Exception\InvalidFormatException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAccessor;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalField;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A year-month in the ISO-8601 calendar system, such as 2007-12.
 */
final class YearMonth implements Temporal, ObjectInterface, ComparableInterface
{
    /**
     * @var EnumMap|null
     */
    private static $units;

    /**
     * @var EnumMap|null
     */
    private static $fields;

    /**
     * @var int
     */
    private $year;

    /**
     * @var int
     */
    private $month;

    /**
     * Obtains an instance of YearMonth from a temporal object.
     *
     * This obtains a year-month based on the specified temporal. A TemporalAccessor represents an arbitrary set of
     * date and time information, which this factory converts to an instance of YearMonth.
     *
     * The conversion extracts the YEAR and MONTH_OF_YEAR fields. The extraction is only permitted if the temporal object has an ISO chronology, or can be converted to a LocalDate.
     *
     * @param TemporalAccessor $temporal The temporal object to convert
     *
     * @return self
     */
    public static function from(TemporalAccessor $temporal): self
    {
        return new self(
            $temporal->get(ChronoField::YEAR()),
            $temporal->get(ChronoField::MONTH_OF_YEAR())
        );
    }

    /**
     * Obtains the current year-month from the system clock in the default time-zone.
     *
     * @return self
     */
    public static function now(): self
    {
        $now = Factory::now();

        return self::ofNative($now);
    }

    /**
     * Obtains an instance of YearMonth from a year and month.
     *
     * @param int $year  The year to represent
     * @param int $month The month-of-year to represent
     *
     * @return self
     */
    public static function of(int $year, int $month): self
    {
        return new self($year, $month);
    }

    /**
     * Obtains an instance of YearMonth from an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return self
     */
    public static function ofNative(DateTimeInterface $dateTime): self
    {
        return new self(
            ChronoField::YEAR()->getFromNative($dateTime),
            ChronoField::MONTH_OF_YEAR()->getFromNative($dateTime)
        );
    }

    /**
     * Obtains an instance of YearMonth from a text string such as 2007-12.
     *
     * The string must represent a valid year-month. The format must be uuuu-MM.
     *
     * @param string $text The text to parse such as "2007-12"
     *
     * @return self
     * @throws InvalidFormatException If the text cannot be parsed to a duration
     */
    public static function parse(string $text): self
    {
        // TODO implement now()
        return new self(0, 1);
    }

    /**
     * Combines this year-month with a day-of-month to create a LocalDate.
     *
     * This returns a LocalDate formed from this year-month and the specified day-of-month.
     *
     * The day-of-month value must be valid for the year-month.
     *
     * @param int $dayOfMonth The day-of-month to use, from 1 to 31
     *
     * @return LocalDate
     * @throws InvalidDateException If the day-of-month is invalid for the year-month
     */
    public function atDay(int $dayOfMonth): LocalDate
    {
        // TODO implement
        return LocalDate::of(0, 1, 1);
    }

    /**
     * Returns a LocalDate at the end of the month.
     *
     * This returns a LocalDate based on this year-month. The day-of-month is set to the last valid day of the month,
     * taking into account leap years.
     *
     * @return LocalDate
     */
    public function atEndOfMonth(): LocalDate
    {
        // TODO implement
        return LocalDate::of(0, 1, 1);
    }

    /**
     * @inheritDoc
     */
    public function compareTo(ComparableInterface $other): int
    {
        if ($other instanceof self && get_class($other) === static::class) {
            $yearDiff = $this->year - $other->year;
            if ($yearDiff === 0) {
                return $this->month - $other->month;
            }

            return $yearDiff;
        }

        throw ClassMismatchException::expectedInstance($this, $other);
    }

    /**
     * @inheritDoc
     */
    public function equals($other): bool
    {
        if ($other instanceof self && get_class($other) === static::class) {
            return $this->year === $other->year && $this->month === $other->month;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        if ($this->supportsField($field)) {
            if (ChronoField::YEAR()->equals($field)) {
                return $this->getYear();
            }

            if (ChronoField::MONTH_OF_YEAR()->equals($field)) {
                return $this->getMonthValue();
            }
        }

        throw UnsupportedTemporalTypeException::forField($field);
    }

    /**
     * Gets the month-of-year field using the Month enum.
     *
     * This method returns the enum Month for the month. This avoids confusion as to what int values mean.
     *
     * @return Month
     */
    public function getMonth(): Month
    {
        return Month::of($this->month);
    }

    /**
     * Gets the month-of-year field from 1 to 12.
     *
     * This method returns the month as an int from 1 to 12. Application code is frequently clearer if the enum Month
     * is used by calling getMonth().
     *
     * @return int
     */
    public function getMonthValue(): int
    {
        return $this->month;
    }

    /**
     * Gets the year field.
     *
     * This method returns the primitive int value for the year.
     *
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Checks if this year-month is after the specified year-month.
     *
     * @param YearMonth $other The other year-month to compare to
     *
     * @return bool
     */
    public function isAfter(YearMonth $other): bool
    {
        // TODO: Implement
        return false;
    }

    /**
     * Checks if this year-month is before the specified year-month.
     *
     * @param YearMonth $other The other year-month to compare to
     *
     * @return bool
     */
    public function isBefore(YearMonth $other): bool
    {
        // TODO: Implement
        return false;
    }

    /**
     * Checks if the year is a leap year
     *
     * @see Year::isLeapYear()
     *
     * @return bool
     */
    public function isLeapYear(): bool
    {
        return Year::isLeapYear($this->year);
    }

    /**
     * Checks if the day-of-month is valid for this year-month.
     *
     * @param int $dayOfMonth The day-of-month to validate, from 1 to 31, invalid value returns false
     *
     * @return bool
     */
    public function isValidDay(int $dayOfMonth): bool
    {
        // TODO: Implement
        return false;
    }

    public function lengthOfMonth(): int
    {
        // TODO: Implement
        return 0;
    }

    public function lengthOfYear(): int
    {
        // TODO: Implement
        return 0;
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit): self
    {
        // TODO: Implement minus() method.
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function minusAmount(TemporalAmount $amount): self
    {
        // TODO: Implement minusAmount() method.
        return $this;
    }

    public function minusMonths(int $months): self
    {
        // TODO: Implement
        return $this;
    }

    public function minusYears(int $years): self
    {
        // TODO: Implement
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): self
    {
        // TODO: Implement plus() method.
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function plusAmount(TemporalAmount $amount): self
    {
        // TODO: Implement plusAmount() method.
        return $this;
    }

    public function plusMonths(int $months): self
    {
        // TODO: Implement
        return $this;
    }

    public function plusYears(int $years): self
    {
        // TODO: Implement
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        if ($field instanceof ChronoField) {
            return $this->getFieldMap()->containsKey($field);
        }

        return $field->isSupportedBy($this);
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
        return sprintf('%s-%s', $this->year, $this->month);
    }

    /**
     * Returns a copy of this year-month with the specified field set to a new value.
     *
     * @param TemporalField $field    The field to set in the result
     * @param int           $newValue The new value of the field in the result
     *
     * @return YearMonth
     */
    public function with(TemporalField $field, int $newValue): self
    {
        if ($this->supportsField($field)) {
            if (ChronoField::YEAR()->equals($field)) {
                return $this->withYear($newValue);
            }
            if (ChronoField::MONTH_OF_YEAR()->equals($field)) {
                return $this->withMonth($newValue);
            }
        }

        throw UnsupportedTemporalTypeException::forField($field);
    }

    /**
     * Returns a copy of this YearMonth with the month-of-year altered.
     *
     * @param int $month the month-of-year to set in the returned year-month
     *
     * @return YearMonth
     */
    public function withMonth(int $month): self
    {
        return new self($this->year, $month);
    }

    /**
     * Returns a copy of this YearMonth with the year altered.
     *
     * @param int $year The year to set in the returned year-month
     *
     * @return YearMonth
     */
    public function withYear(int $year): self
    {
        return new self($year, $this->month);
    }

    /**
     * @return EnumMap
     */
    private function getUnitMap(): EnumMap
    {
        if (!static::$units) {
            $map = EnumMap::for(ChronoUnit::class, 'bool', false);

            $supported = [
                ChronoUnit::YEARS(),
                ChronoUnit::DECADES(),
                ChronoUnit::CENTURIES(),
                ChronoUnit::MILLENNIA(),
            ];

            static::$units = EnumMapHelper::putAllTrue($map, $supported);
        }

        return static::$units;
    }

    /**
     * @return EnumMap
     */
    private function getFieldMap(): EnumMap
    {
        if (!static::$fields) {
            $map = EnumMap::for(ChronoField::class, 'bool', false);

            $supported = [
                ChronoField::YEAR(),
                ChronoField::MONTH_OF_YEAR(),
            ];

            static::$fields = EnumMapHelper::putAllTrue($map, $supported);
        }

        return static::$fields;
    }

    /**
     * @param int $year
     * @param int $month
     */
    private function __construct(int $year, int $month)
    {
        $this->year = Year::of($year)->getValue();
        $this->month = Month::of($month)->getValue();
    }
}
