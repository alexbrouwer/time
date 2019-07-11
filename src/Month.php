<?php declare(strict_types=1);

namespace PAR\Time;

use DateTimeInterface;
use PAR\Enum\Enum;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Exception\InvalidArgumentException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\TemporalAccessor;
use PAR\Time\Temporal\TemporalField;

/**
 * A month-of-year, such as 'July'.
 *
 * Month is an enum representing the 12 months of the year -
 * January, February, March, April, May, June, July, August, September, October, November and December.
 *
 * In addition to the textual enum name, each month-of-year has an int value. The int value follows normal usage and
 * the ISO-8601 standard, from 1 (January) to 12 (December). It is recommended that applications use the enum rather
 * than the int value to ensure code clarity.
 *
 * **Do not use ordinal() to obtain the numeric representation of Month. Use getValue() instead.**
 *
 * @method static self JANUARY()
 * @method static self FEBRUARY()
 * @method static self MARCH()
 * @method static self APRIL()
 * @method static self MAY()
 * @method static self JUNE()
 * @method static self JULY()
 * @method static self AUGUST()
 * @method static self SEPTEMBER()
 * @method static self OCTOBER()
 * @method static self NOVEMBER()
 * @method static self DECEMBER()
 */
final class Month extends Enum implements TemporalAccessor
{
    private const MIN_VALUE = 1;
    private const MAX_VALUE = 12;

    protected const APRIL = [4];
    protected const AUGUST = [8];
    protected const DECEMBER = [12];
    protected const FEBRUARY = [2];
    protected const JANUARY = [1];
    protected const JULY = [7];
    protected const JUNE = [6];
    protected const MARCH = [3];
    protected const MAY = [5];
    protected const NOVEMBER = [11];
    protected const OCTOBER = [10];
    protected const SEPTEMBER = [9];

    private const VALUE_MAP = [
        1  => 'JANUARY',
        2  => 'FEBRUARY',
        3  => 'MARCH',
        4  => 'APRIL',
        5  => 'MAY',
        6  => 'JUNE',
        7  => 'JULY',
        8  => 'AUGUST',
        9  => 'SEPTEMBER',
        10 => 'OCTOBER',
        11 => 'NOVEMBER',
        12 => 'DECEMBER',
    ];

    /**
     * @var int
     */
    private $value;

    /**
     * Obtains an instance of DayOfWeek from a native DateTime or DateTimeImmutable object.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return Month
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        $monthOfYear = ChronoField::MONTH_OF_YEAR()->getFromNative($dateTime);

        return self::of($monthOfYear);
    }

    /**
     * @param int $value
     */
    protected function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * Gets the month-of-year int value.
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Obtains an instance of Month from an int value.
     *
     * @param int $month The month-of-year to represent, from 1 (January) to 12 (December)
     *
     * @return Month
     * @throws InvalidArgumentException If the month-of-year is invalid
     */
    public static function of(int $month): self
    {
        Assert::range($month, self::MIN_VALUE, self::MAX_VALUE);

        return self::valueOf(self::VALUE_MAP[$month]);
    }

    /**
     * Returns the month-of-year that is the specified number of quarters after this one.
     *
     * The calculation rolls around the end of the year from December to January. The specified period may be negative.
     *
     * @param int $months The months to add, positive or negative
     *
     * @return Month
     */
    public function plus(int $months): self
    {
        $currentValue = $this->getValue();
        $newValue = $currentValue + $months;

        if ($newValue === 0) {
            $newValue = self::MAX_VALUE;
        }

        $rangeMultiplier = (int)floor($newValue / self::MAX_VALUE);

        if ($newValue < self::MIN_VALUE) {
            $rangeMultiplier *= -1;
            $newValue = ($rangeMultiplier * self::MAX_VALUE) + $newValue;
        }

        if ($newValue > self::MAX_VALUE) {
            $newValue -= $rangeMultiplier * self::MAX_VALUE;
        }

        if ($newValue === $currentValue) {
            return $this;
        }

        return self::of($newValue);
    }

    /**
     * Returns the month-of-year that is the specified number of months before this one.
     *
     * The calculation rolls around the start of the year from January to December. The specified period may be negative.
     *
     * @param int $months The months to subtract, positive or negative
     *
     * @return Month
     */
    public function minus(int $months): self
    {
        return $this->plus($months * -1);
    }

    /**
     * Gets the month corresponding to the first month of this quarter.
     *
     * The year can be divided into four quarters. This method returns the first month of the quarter for the base
     * month. January, February and March return January. April, May and June return April. July, August and
     * September return July. October, November and December return October.
     *
     * @return Month
     */
    public function firstMonthOfQuarter(): self
    {
        if ($this->getValue() >= 10) {
            return self::OCTOBER();
        }

        if ($this->getValue() >= 7) {
            return self::JULY();
        }

        if ($this->getValue() >= 4) {
            return self::APRIL();
        }

        return self::JANUARY();
    }

    /**
     * Gets the length of this month in days.
     *
     * This takes a flag to determine whether to return the length for a leap year or not.
     *
     * February has 28 days in a standard year and 29 days in a leap year. April, June, September and November have 30
     * days. All other months have 31 days.
     *
     * @param bool $leapYear True if the length is required for a leap year
     *
     * @return int
     */
    public function length(bool $leapYear): int
    {
        $days = [
            31,
            $leapYear ? 29 : 28,
            31,
            30,
            31,
            30,
            31,
            31,
            30,
            31,
            30,
            31,
        ];

        return $days[$this->ordinal()];
    }

    /**
     * Gets the day-of-year corresponding to the first day of this month.
     *
     * This returns the day-of-year that this month begins on, using the leap year flag to determine the length of February.
     *
     * @param bool $leapYear True if the length is required for a leap year
     *
     * @return int
     */
    public function firstDayOfYear(bool $leapYear = false): int
    {
        $firstDay = 1;

        foreach (self::values() as $month) {
            /** @var self $month */
            if ($month->compareTo($this) < 0) {
                $firstDay += $month->length($leapYear);
            }
        }

        return $firstDay;
    }

    /**
     * Checks if the specified field is supported.
     *
     * This checks if this month-of-year can be queried for the specified field. If false, then calling the range and
     * get methods will throw an exception.
     *
     * If the field is ChronoField::MONTH_OF_YEAR() then this method returns true. All other ChronoField instances will
     * return false.
     *
     * @param TemporalField $field
     *
     * @return bool
     */
    public function supportsField(TemporalField $field): bool
    {
        return ChronoField::MONTH_OF_YEAR()->equals($field);
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
}
