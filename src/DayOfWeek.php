<?php declare(strict_types=1);

namespace PAR\Time;

use DateTimeInterface;
use PAR\Enum\Enum;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Exception\InvalidArgumentException;

/**
 * A day-of-week, such as 'Tuesday'.
 *
 * DayOfWeek is an enum representing the 7 days of the week - Monday, Tuesday, Wednesday, Thursday, Friday, Saturday and Sunday.
 *
 * In addition to the textual enum name, each day-of-week has an int value. The int value follows the ISO-8601
 * standard, from 1 (Monday) to 7 (Sunday). It is recommended that applications use the enum rather than the int value
 * to ensure code clarity.
 *
 * **Do not use ordinal() to obtain the numeric representation of Month. Use getValue() instead.**
 *
 * @method static self MONDAY()
 * @method static self TUESDAY()
 * @method static self WEDNESDAY()
 * @method static self THURSDAY()
 * @method static self FRIDAY()
 * @method static self SATURDAY()
 * @method static self SUNDAY()
 */
final class DayOfWeek extends Enum
{
    private const MIN_VALUE = 1;
    private const MAX_VALUE = 7;

    protected const MONDAY = [1];
    protected const TUESDAY = [2];
    protected const WEDNESDAY = [3];
    protected const THURSDAY = [4];
    protected const FRIDAY = [5];
    protected const SATURDAY = [6];
    protected const SUNDAY = [7];

    private const VALUE_MAP = [
        1 => 'MONDAY',
        2 => 'TUESDAY',
        3 => 'WEDNESDAY',
        4 => 'THURSDAY',
        5 => 'FRIDAY',
        6 => 'SATURDAY',
        7 => 'SUNDAY',
    ];

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    protected function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * Gets the day-of-week int value.
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Obtains an instance of DayOfWeek from an int value.
     *
     * @param int $dayOfWeek The day-of-week to represent, from 1 (Monday) to 7 (Sunday)
     *
     * @return DayOfWeek
     * @throws InvalidArgumentException If the day-of-week is invalid
     */
    public static function of(int $dayOfWeek): self
    {
        Assert::range($dayOfWeek, self::MIN_VALUE, self::MAX_VALUE);

        return self::valueOf(self::VALUE_MAP[$dayOfWeek]);
    }

    /**
     * Returns the day-of-week that is the specified number of days after this one.
     *
     * The calculation rolls around the end of the week from Sunday to Monday. The specified period may be negative.
     *
     * @param int $days The days to add, positive or negative
     *
     * @return DayOfWeek
     */
    public function plus(int $days): self
    {
        $currentValue = $this->getValue();
        $newValue = $currentValue + $days;

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
     * Returns the day-of-week that is the specified number of days before this one.
     *
     * The calculation rolls around the start of the year from Monday to Sunday. The specified period may be negative.
     *
     * @param int $days The days to subtract, positive or negative
     *
     * @return DayOfWeek
     */
    public function minus(int $days): self
    {
        return $this->plus($days * -1);
    }

    /**
     * Obtains an instance of DayOfWeek from a native DateTime or DateTimeImmutable object.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return DayOfWeek
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        $dayOfWeek = ChronoField::DAY_OF_WEEK()->getFromNative($dateTime);

        return self::of($dayOfWeek);
    }
}
