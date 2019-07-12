<?php declare(strict_types=1);

namespace PAR\Time;

use DateTimeInterface;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Exception\InvalidArgumentException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalField;

/**
 * A year in the ISO-8601 calendar system, such as 2007.
 */
final class Year implements Temporal
{
    public const MIN_VALUE = -999999999;
    public const MAX_VALUE = 999999999;

    /**
     * @var int
     */
    private $value;

    /**
     * Obtains an instance of DayOfWeek from an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return Year
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        $year = ChronoField::YEAR()->getFromNative($dateTime);

        return self::of($year);
    }

    /**
     * Checks if the year is a leap year, according to the ISO calendar system rules.
     *
     * @param int $year
     *
     * @return bool
     */
    public static function isLeap(int $year): bool
    {
        $dt = Factory::createFromFormat('Y', (string)$year);

        return (int)$dt->format('L') === 1;
    }

    /**
     * Obtains the current year from the system clock in the default time-zone.
     *
     * @return Year
     */
    public static function now(): self
    {
        $now = Factory::now();

        return self::fromNative($now);
    }

    /**
     * Obtains an instance of Year.
     *
     * @param int $year The year to represent
     *
     * @return Year
     * @throws InvalidArgumentException If year is outside of range
     */
    public static function of(int $year): self
    {
        return new self($year);
    }

    /**
     * Obtains an instance of Year from a text string such as 2007.
     *
     * @param string $text The text to parse
     *
     * @return Year
     * @throws InvalidArgumentException If text could not be parsed or value is outside of range
     */
    public static function parse(string $text): self
    {
        Assert::regex($text, '/^[-+]?\d{1,}$/');

        return new self((int)$text);
    }

    private function __construct(int $value)
    {
        Assert::range($value, self::MIN_VALUE, self::MAX_VALUE);

        $this->value = $value;
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
    public function toString(): string
    {
        return (string)$this->value;
    }

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        // TODO: Implement supportsField() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        // TODO: Implement get() method.
        throw UnsupportedTemporalTypeException::forField($field);
    }
}
