<?php

namespace PAR\Time;

use DateInterval;
use Exception;
use PAR\Core\ComparableInterface;
use PAR\Core\Exception\ClassMismatchException;
use PAR\Core\Helper\InstanceHelper;
use PAR\Core\ObjectInterface;
use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Exception\DateTimeException;
use PAR\Time\Exception\InvalidFormatException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A time-based amount of time, such as '34.5 seconds'.
 */
final class Duration implements TemporalAmount, ObjectInterface, ComparableInterface
{
    private const DAY_IN_SECONDS = 86400;
    private const HOUR_IN_SECONDS = 3600;
    private const MINUTE_IN_SECONDS = 60;
    private const SECOND_IN_MILLIS = 1000;
    private const SECOND_IN_MICROS = 1000000;
    private const MILLI_IN_MICROS = 1000;

    /**
     * @var int
     */
    private $seconds;

    /**
     * @var int
     */
    private $microSeconds;

    /**
     * Obtains an instance of Duration from a temporal amount.
     *
     * This obtains a duration based on the specified amount. A TemporalAmount represents an amount of time, which may
     * be date-based or time-based, which this factory extracts to a duration.
     *
     * The conversion loops around the set of units from the amount and uses the duration of the unit to calculate the
     * total Duration. Only a subset of units are accepted by this method. The unit must either have an exact duration
     * or be ChronoUnit::DAYS() which is treated as 24 hours. If any other units are found then an exception is thrown.
     *
     * @param TemporalAmount $amount The temporal amount to convert
     *
     * @return Duration
     * @throws UnsupportedTemporalTypeException If the temporal amount contains unsupported units
     */
    public static function from(TemporalAmount $amount): self
    {
        $duration = self::zero();
        $units = $amount->getUnits();
        foreach ($units as $unit) {
            $multiplicand = $amount->get($unit);
            $duration = $duration->plusDuration($unit->getDuration()->multipliedBy($multiplicand));
        }

        return $duration;
    }

    /**
     * Obtains a Duration representing an amount in the specified unit.
     *
     * The parameters represent the two parts of a phrase like '4 Hours'. For example:
     *
     * Duration::of(3, ChronoUnit::SECONDS());
     * Duration::of(12, ChronoUnit::HOURS());
     *
     * Only a subset of units are accepted by this method. The unit must either have an exact duration or be
     * ChronoUnit::DAYS() which is treated a 24 hours. Other units throw an exception.
     *
     * @param int          $amount The amount to represent
     * @param TemporalUnit $unit   The unit for the amount
     *
     * @return Duration
     * @throws UnsupportedTemporalTypeException If the unit is not supported
     */
    public static function of(int $amount, TemporalUnit $unit): self
    {
        if ($unit->isDurationEstimated() && !ChronoUnit::DAYS()->equals($unit)) {
            throw UnsupportedTemporalTypeException::forUnit($unit);
        }

        if ($amount === 0) {
            return self::zero();
        }

        return $unit->getDuration()->multipliedBy($amount);
    }

    /**
     * Obtains a Duration representing the DateInterval.
     *
     * Only a subset of the interval is accepted by this method. If the interval describes one or more years and/or one
     * or more months and exception is thrown.
     *
     * @param DateInterval $interval The DateInterval to convert
     *
     * @return Duration
     * @throws DateTimeException If DateInterval contains unsupported units
     */
    public static function ofDateInterval(DateInterval $interval): self
    {
        if ($interval->y !== 0 || $interval->m !== 0) {
            throw new DateTimeException('Unable to create a duration from a DateInterval with years or months.');
        }

        $seconds = (int)$interval->format('%a') * self::DAY_IN_SECONDS;
        $seconds += (int)$interval->format('%d') * self::DAY_IN_SECONDS;
        $seconds += (int)$interval->format('%h') * self::HOUR_IN_SECONDS;
        $seconds += (int)$interval->format('%m') * self::MINUTE_IN_SECONDS;
        $seconds += (int)$interval->format('%s');

        if ($interval->invert === 1) {
            $seconds *= -1;
        }

        $microSeconds = (int)$interval->format('%f');

        return new self($seconds, $microSeconds);
    }

    /**
     * Obtains a Duration representing a number of standard 24 hour days.
     *
     * The seconds are calculated based on the standard definition of a day, where each day is 86400 seconds which
     * implies a 24 hour day. Then microsecond field is set to zero.
     *
     * @param int $days The number of days, positive or negative
     *
     * @return Duration
     */
    public static function ofDays(int $days): self
    {
        /** @noinspection SummerTimeUnsafeTimeManipulationInspection */
        return new self(self::DAY_IN_SECONDS * $days, 0);
    }

    /**
     * Obtains a Duration representing a number of standard hours.
     *
     * The seconds are calculated based on the standard definition of an hour, where each hour is 3600 seconds. The
     * microsecond field is set to zero.
     *
     * @param int $hours The number of hours, positive or negative
     *
     * @return Duration
     */
    public static function ofHours(int $hours): self
    {
        return new self(self::HOUR_IN_SECONDS * $hours, 0);
    }

    /**
     * Obtains a Duration representing a number of microseconds.
     *
     * The seconds are extracted from the specified microseconds.
     *
     * @param int $micros The number of microseconds, positive or negative
     *
     * @return Duration
     */
    public static function ofMicros(int $micros): self
    {
        return new self(0, $micros);
    }

    /**
     * Obtains a Duration representing a number of milliseconds.
     *
     * The seconds are extracted from the specified milliseconds. The microseconds are calculated based on the standard
     * definition of a millisecond, where each millisecond is 1000 microseconds.
     *
     * @param int $millis The number of milliseconds, positive or negative
     *
     * @return Duration
     */
    public static function ofMillis(int $millis): self
    {
        $seconds = 0;
        if ($millis >= self::SECOND_IN_MILLIS) {
            $seconds = (int)floor($millis / self::SECOND_IN_MILLIS);
            $millis -= $seconds * self::SECOND_IN_MILLIS;
        }

        return new self($seconds, (int)($millis * self::MILLI_IN_MICROS));
    }

    /**
     * Obtains a Duration representing a number of standard minutes.
     *
     * The seconds are calculated based on the standard definition of a minute, where each minute is 60 seconds. The
     * microsecond field is set to zero.
     *
     * @param int $minutes The number of minutes, positive or negative
     *
     * @return Duration
     */
    public static function ofMinutes(int $minutes): self
    {
        return new self(self::MINUTE_IN_SECONDS * $minutes, 0);
    }

    /**
     * Obtains a Duration representing a number of seconds and optionally an adjustment in nanoseconds.
     *
     * @param int $seconds               The number of seconds, positive or negative
     * @param int $microSecondAdjustment The optional microsecond adjustment to the number of seconds, positive or negative
     *
     * @return Duration
     */
    public static function ofSeconds(int $seconds, int $microSecondAdjustment = 0): self
    {
        return new self($seconds, $microSecondAdjustment);
    }

    /**
     * Obtains a Duration from a text string such as PnDTnHnMn.nS.
     *
     * This will parse a textual representation of a duration, including the string produced by toString(). The formats
     * accepted are based on the ISO-8601 duration format PnDTnHnMn.nS with days considered to be exactly 24 hours.
     *
     * See ISO-8601 standard
     *
     * @param string $text The text to parse
     *
     * @return Duration
     * @throws InvalidFormatException If the text cannot be parsed to a duration
     */
    public static function parse(string $text): self
    {
        if (!preg_match(
            '/^(?<signed>-|\+)?P(?!$)(?<days>[-\+]?\d+D)?(T(?=[-\+\d])(?<hours>[-\+]?\d+H)?(?<minutes>[-\+]?\d+M)?(?<seconds>[-\+]?\d+(?:\.\d+)?S)?)?$/',
            $text,
            $matches
        )
        ) {
            throw InvalidFormatException::of('ISO-8601 duration', $text);
        }

        $duration = self::zero();
        if ($text === 'PT0S' || $text === 'P0D') {
            return $duration;
        }

        foreach ($matches as $name => $match) {
            if ($match === '' || !is_string($name)) {
                continue;
            }
            switch ($name) {
                case 'days':
                    $duration = $duration->plusDays((int)$match);
                    break;
                case 'hours':
                    $duration = $duration->plusHours((int)$match);
                    break;
                case 'minutes':
                    $duration = $duration->plusMinutes((int)$match);
                    break;
                case 'seconds':
                    $duration = $duration->plusMillis((int)round((float)$match * 1000));
                    break;
                default:
                    break;
            }
        }

        if (isset($matches['signed']) && $matches['signed'] === '-') {
            $duration = $duration->negated();
        }

        return $duration;
    }

    /**
     * Obtains a Duration representing zero length.
     *
     * @return Duration
     */
    public static function zero(): self
    {
        return new self(0, 0);
    }

    /**
     * Returns a copy of this duration with a positive length.
     *
     * @return Duration
     */
    public function abs(): self
    {
        if ($this->isNegative()) {
            return new self($this->seconds * -1, $this->microSeconds);
        }

        return $this;
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
    public function compareTo(ComparableInterface $other): int
    {
        if ($other instanceof self && get_class($other) === static::class) {
            $comparedSeconds = $this->seconds <=> $other->seconds;
            if ($comparedSeconds === 0) {
                return $this->microSeconds <=> $other->microSeconds;
            }

            return $comparedSeconds;
        }

        throw ClassMismatchException::expectedInstance($this, $other);
    }

    /**
     * Returns a copy of this duration divided by the specified value.
     *
     *
     * @param int $divisor The value to divide the duration by, positive or negative
     *
     * @return Duration
     */
    public function dividedBy(int $divisor): self
    {
        if ($divisor === 0 || $this->isZero()) {
            return $this;
        }

        $newSeconds = $this->seconds / $divisor;
        $seconds = floor($newSeconds);
        $microsOfSeconds = ($newSeconds - $seconds) * self::SECOND_IN_MICROS;

        // We will loose precision here
        $newMicros = $this->microSeconds / $divisor;

        return new self(
            (int)$seconds,
            (int)($newMicros + $microsOfSeconds)
        );
    }

    /**
     * @inheritDoc
     */
    public function equals($other): bool
    {
        if ($other instanceof self && get_class($other) === static::class) {
            return $this->seconds === $other->seconds && $this->microSeconds === $other->microSeconds;
        }

        return false;
    }

    /**
     * Gets the value of the requested unit.
     *
     * This returns a value for each of the two supported units, SECONDS and MICROS. All other units throw an exception.
     *
     * @param TemporalUnit $unit The TemporalUNit for which to return the value
     *
     * @return int
     * @throws UnsupportedTemporalTypeException If the unit is not supported
     */
    public function get(TemporalUnit $unit): int
    {
        $this->assertUnit($unit);

        if (ChronoUnit::MICROS()->equals($unit)) {
            return $this->getMicroSeconds();
        }

        return $this->getSeconds();
    }

    /**
     * Gets the number of microseconds within the second in this duration.
     *
     * This part is a value between 0 and 999.999 that is an adjustment to the length in seconds.
     *
     * @return int
     */
    public function getMicroSeconds(): int
    {
        return $this->microSeconds;
    }

    /**
     * Gets the number of seconds in this duration.
     *
     * @return int
     */
    public function getSeconds(): int
    {
        return $this->seconds;
    }

    /**
     * Gets the set of units supported by this duration.
     *
     * The supported units are SECONDS, and MICROS. They are returned in the order seconds, micros.
     *
     * This set can be used in conjunction with get(TemporalUnit) to access the entire state of the duration.
     *
     * @return TemporalUnit[]
     */
    public function getUnits(): array
    {
        return [
            ChronoUnit::SECONDS(),
            ChronoUnit::MICROS(),
        ];
    }

    /**
     * Checks if this duration is negative, excluding zero.
     *
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->seconds < 0;
    }

    /**
     * Checks if this duration is zero length.
     *
     * @return bool
     */
    public function isZero(): bool
    {
        return $this->equals(self::zero());
    }

    /**
     * Returns a copy of this duration with the specified duration subtracted.
     *
     * @param int          $amountToSubtract The amount to subtract, measured in terms of the unit, positive or negative
     * @param TemporalUnit $unit             The unit that the amount is measured in, must have an exact duration
     *
     * @return Duration
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit): self
    {
        return $this->plus($amountToSubtract * -1, $unit);
    }

    /**
     * Returns a copy of this duration with the specified duration in standard 24 hour days subtracted.
     *
     * The number of days is multiplied by 86400 to obtain the number of seconds to subtract. This is based on the
     * standard definition of a day as 24 hours.
     *
     * @param int $days The days to subtract, positive or negative
     *
     * @return Duration
     */
    public function minusDays(int $days): self
    {
        return $this->plusDays($days * -1);
    }

    /**
     * Returns a copy of this duration with the specified duration subtracted.
     *
     * @param Duration $duration The Duration to subtract, positive or negative
     *
     * @return Duration
     */
    public function minusDuration(Duration $duration): self
    {
        return $this->plusDuration($duration->negated());
    }

    /**
     * Returns a copy of this duration with the specified duration in hours subtracted.
     *
     * The number of hours is multiplied by 3600 to obtain the number of seconds to subtract.
     *
     * @param int $hours The hours to subtract, positive or negative
     *
     * @return Duration
     */
    public function minusHours(int $hours): self
    {
        return $this->plusHours($hours * -1);
    }

    /**
     * Returns a copy of this duration with the specified duration in microseconds subtracted.
     *
     * @param int $micros The microseconds to subtract, positive or negative
     *
     * @return Duration
     */
    public function minusMicros(int $micros): self
    {
        return $this->plusMicros($micros * -1);
    }

    /**
     * Returns a copy of this duration with the specified duration in milliseconds subtracted.
     *
     * @param int $millis The milliseconds to subtract, positive or negative
     *
     * @return Duration
     */
    public function minusMillis(int $millis): self
    {
        return $this->plusMillis($millis * -1);
    }

    /**
     * Returns a copy of this duration with the specified duration in minutes subtracted.
     *
     * The number of hours is multiplied by 60 to obtain the number of seconds to subtract.
     *
     * @param int $minutes The minutes to subtract, positive or negative
     *
     * @return Duration
     */
    public function minusMinutes(int $minutes): self
    {
        return $this->plusMinutes($minutes * -1);
    }

    /**
     * Returns a copy of this duration with the specified duration in seconds subtracted.
     *
     * @param int $seconds The seconds to subtract, positive or negative
     *
     * @return Duration
     */
    public function minusSeconds(int $seconds): self
    {
        return $this->plusSeconds($seconds * -1);
    }

    /**
     * Returns a copy of this duration multiplied by the value.
     *
     * @param int $multiplicand The value to multiply the duration by, positive or negative
     *
     * @return Duration
     */
    public function multipliedBy(int $multiplicand): self
    {
        return self::ofSeconds(
            $this->seconds * $multiplicand,
            $this->microSeconds * $multiplicand
        );
    }

    /**
     * Returns a copy of this duration with the length negated.
     *
     * This method swaps the sign of the total length of this duration. For example, PT1.3S will be returned as
     * PT-1.3S and vice versa.
     *
     * @return Duration
     */
    public function negated(): self
    {
        return new self($this->seconds * -1, $this->microSeconds);
    }

    /**
     * Returns a copy of this duration with the specified duration added.
     *
     * @param int          $amountToAdd The amount to add, measured in terms of the unit, positive or negative
     * @param TemporalUnit $unit        The unit that the amount is measured in, must have an exact duration
     *
     * @return Duration
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): self
    {
        $this->assertUnit($unit);

        $changed = $this;
        if (ChronoUnit::SECONDS()->equals($unit)) {
            $changed = $changed->plusSeconds($amountToAdd);
        }
        if (ChronoUnit::MICROS()->equals($unit)) {
            $changed = $changed->plusMicros($amountToAdd);
        }

        return $changed;
    }

    /**
     * Returns a copy of this duration with the specified duration in standard 24 hour days added.
     *
     * The number of days is multiplied by 86400 to obtain the number of seconds to add. This is based on the standard
     * definition of a day as 24 hours.
     *
     * @param int $days The days to add, positive or negative
     *
     * @return Duration
     */
    public function plusDays(int $days): self
    {
        return self::ofDays($this->toDays() + $days);
    }

    /**
     * Returns a copy of this duration with the specified duration added.
     *
     * @param Duration $duration The duration to add, positive or negative
     *
     * @return Duration
     */
    public function plusDuration(Duration $duration): self
    {
        $changed = $this;
        foreach ($duration->getUnits() as $unit) {
            $changed = $changed->plus($duration->get($unit), $unit);
        }

        return $changed;
    }

    /**
     * Returns a copy of this duration with the specified duration in hours added.
     *
     * @param int $hours The hours to add, positive or negative
     *
     * @return Duration
     */
    public function plusHours(int $hours): self
    {
        return self::ofHours($this->toHours() + $hours);
    }

    /**
     * Returns a copy of this duration with the specified duration in microseconds added.
     *
     * @param int $micros The microseconds to add, positive or negative
     *
     * @return Duration
     */
    public function plusMicros(int $micros): self
    {
        return self::ofMicros($this->toMicros() + $micros);
    }

    /**
     * Returns a copy of this duration with the specified duration in milliseconds added.
     *
     * @param int $millis The milliseconds to add, positive or negative
     *
     * @return Duration
     */
    public function plusMillis(int $millis): self
    {
        return self::ofMillis($this->toMillis() + $millis);
    }

    /**
     * Returns a copy of this duration with the specified duration in minutes added.
     *
     * @param int $minutes The minutes to add, positive or negative
     *
     * @return Duration
     */
    public function plusMinutes(int $minutes): self
    {
        return self::ofMinutes($this->toMinutes() + $minutes);
    }

    /**
     * Returns a copy of this duration with the specified duration in seconds added.
     *
     * @param int $seconds The seconds to add, positive or negative
     *
     * @return Duration
     */
    public function plusSeconds(int $seconds): self
    {
        return self::ofSeconds($this->toSeconds() + $seconds);
    }

    /**
     * Subtracts this duration from the specified temporal object.
     *
     * This returns a temporal object of the same observable type as the input with this duration subtracted.
     *
     * @param Temporal $temporal The temporal object tot adjust
     *
     * @return Temporal
     */
    public function subtractFrom(Temporal $temporal): Temporal
    {
        foreach ($this->getUnits() as $unit) {
            $amount = $this->get($unit);
            $temporal = $temporal->minus($amount, $unit);
        }

        return $temporal;
    }

    /**
     * Returns native DateInterval for this duration.
     *
     * @return DateInterval
     * @throws DateTimeException If Duration could not be transformed
     */
    public function toDateInterval(): DateInterval
    {
        try {
            $interval = DateInterval::createFromDateString($this->getSeconds() . ' seconds');
            $micros = $this->getMicroSeconds();
            if ($micros > 0) {
                $interval->f = $micros / self::SECOND_IN_MICROS;
            }

            return $interval;
        } catch (Exception $e) {
            // TODO implement custom exception
            throw new DateTimeException(
                sprintf(
                    'Cannot transform %s@%s to \DateInterval',
                    self::class,
                    $this->toString()
                ),
                0,
                $e
            );
        }
    }

    /**
     * Gets the number of days in this duration.
     *
     * This returns the total number of days in the duration by dividing the number of seconds by 86400. This is based on the standard definition of a day as 24 hours.
     *
     * @return int
     */
    public function toDays(): int
    {
        return (int)floor($this->toHours() / 24);
    }

    /**
     * Gets the number of days in this duration.
     *
     * This returns the total number of days in the duration by dividing the number of seconds by 86400. This is based on the standard definition of a day as 24 hours.
     *
     * @return int
     */
    public function toDaysPart(): int
    {
        return $this->toDays();
    }

    /**
     * Gets the number of hours in this duration.
     *
     * This returns the total number of hours in the duration by dividing the number of seconds by 3600.
     *
     * @return int
     */
    public function toHours(): int
    {
        return (int)floor($this->toMinutes() / 60);
    }

    /**
     * Extracts the number of hours part in the duration.
     *
     * This returns the number of remaining hours when dividing toHours() by hours in a day. This is based on the
     * standard definition of a day as 24 hours.
     *
     * @return int
     */
    public function toHoursPart(): int
    {
        return $this->toHours() - ($this->toDays() * 24);
    }

    /**
     * Converts this duration to the total length in microseconds.
     *
     * If this duration is too large to fit in an int microseconds, then an exception is thrown.
     *
     * @return int
     */
    public function toMicros(): int
    {
        return ($this->seconds * self::SECOND_IN_MICROS) + $this->microSeconds;
    }

    /**
     * Get the microseconds part within seconds of the duration.
     *
     * @return int
     */
    public function toMicrosPart(): int
    {
        return $this->toMicros() - ($this->toMillis() * 1000);
    }

    /**
     * Converts this duration to the total length in milliseconds.
     *
     * If this duration is too large to fit in an int milliseconds, then an exception is thrown.
     *
     * If this duration has greater than millisecond precision, then the conversion will drop any excess precision
     * information as though the amount in microsecond was subject to integer division by one thousand.
     *
     * @return int
     */
    public function toMillis(): int
    {
        return (int)($this->toMicros() / self::MILLI_IN_MICROS);
    }

    /**
     * Extracts the number of milliseconds part of the duration.
     *
     * This returns the milliseconds part by dividing the number of microseconds by 1,000.
     *
     * @return int
     */
    public function toMillisPart(): int
    {
        return $this->toMillis() - ($this->toSeconds() * 1000);
    }

    /**
     * Gets the number of minutes in this duration.
     *
     * This returns the total number of minutes in the duration by dividing the number of seconds by 60.
     *
     * @return int
     */
    public function toMinutes(): int
    {
        return (int)floor($this->toSeconds() / 60);
    }

    /**
     * Extracts the number of minutes part in the duration.
     *
     * This returns the number of remaining minutes when dividing toMinutes() by minutes in an hour. This is based on the standard definition of an hour as 60 minutes.
     *
     * @return int
     */
    public function toMinutesPart(): int
    {
        return $this->toMinutes() - ($this->toHours() * 60);
    }

    /**
     * @return int
     */
    public function toSeconds(): int
    {
        return $this->seconds;
    }

    /**
     * Extracts the number of seconds part in the duration.
     *
     * This returns the remaining seconds when dividing toSeconds() by seconds in a minute. This is based on the standard definition of a minute as 60 seconds.
     *
     * @return int
     */
    public function toSecondsPart(): int
    {
        return $this->toSeconds() - ($this->toMinutes() * 60);
    }

    /**
     * A string representation of this duration using ISO-8601 seconds based representation, such as PT8H6M12.345S.
     *
     * The format of the returned string will be PTnHnMnS, where n is the relevant hours, minutes or seconds part of
     * the duration. Any fractional seconds are placed after a decimal point in the seconds section. If a section has a
     * zero value, it is omitted. The hours, minutes and seconds will all have the same sign.
     *
     * Examples:
     *
     * "20.345 seconds"                 -- "PT20.345S
     * "15 minutes" (15 * 60 seconds)   -- "PT15M"
     * "10 hours" (10 * 3600 seconds)   -- "PT10H"
     * "2 days" (2 * 86400 seconds)     -- "PT48H"
     *
     * Note that multiples of 24 hours are not output as days to avoid confusion with Period.
     *
     * @return string
     */
    public function toString(): string
    {
        if ($this->isZero()) {
            return 'PT0S';
        }

        $parts = [
            $this->isNegative() ? '-' : '',
            'P',
        ];

        $absSeconds = $this->abs()->getSeconds();
        $microSeconds = $this->getMicroSeconds();

        if ($absSeconds >= self::DAY_IN_SECONDS) {
            $days = (int)floor($absSeconds / self::DAY_IN_SECONDS);
            $absSeconds -= self::DAY_IN_SECONDS * $days;
            $parts[] = sprintf('%dD', $days);
        }

        if ($absSeconds > 0 || $microSeconds > 0) {
            $parts[] = 'T';
        }

        if ($absSeconds >= self::HOUR_IN_SECONDS) {
            $hours = (int)floor($absSeconds / self::HOUR_IN_SECONDS);
            $absSeconds -= self::HOUR_IN_SECONDS * $hours;
            $parts[] = sprintf('%dH', $hours);
        }

        if ($absSeconds >= self::MINUTE_IN_SECONDS) {
            $minutes = (int)floor($absSeconds / self::MINUTE_IN_SECONDS);
            $absSeconds -= self::MINUTE_IN_SECONDS * $minutes;
            $parts[] = sprintf('%dM', $minutes);
        }

        if ($microSeconds > 0) {
            $absSeconds += $microSeconds / self::SECOND_IN_MICROS;
        }

        if ($absSeconds > 0) {
            $parts[] = sprintf('%sS', $absSeconds);
        }

        return implode('', $parts);
    }

    /**
     * Returns a copy of this duration with the specified micro-of-second.
     *
     * This returns a duration with the specified micro-of-second, retaining the seconds part of this duration.
     *
     * @param int $microSeconds The micro-of-second to represent, may be negative
     *
     * @return Duration
     */
    public function withMicroSeconds(int $microSeconds): self
    {
        return new self($this->seconds, $microSeconds);
    }

    /**
     * Returns a copy of this duration with the specified amount of seconds.
     *
     * This returns a duration with the specified seconds, retaining the micro-of-second part of this duration.
     *
     * @param int $seconds The seconds to represent, may be negative
     *
     * @return Duration
     */
    public function withSeconds(int $seconds): self
    {
        return new self($seconds, $this->microSeconds);
    }

    private function assertUnit(TemporalUnit $unit): void
    {
        if (!InstanceHelper::isAnyOf($unit, $this->getUnits())) {
            throw UnsupportedTemporalTypeException::forUnit($unit);
        }
    }

    /**
     * Will alter values of seconds and microSeconds in order to ensure that the stored microsecond is in the
     * 0 to 999.999 range. For example, the following will result in exactly the same duration:
     *
     * new Duration(3, 1);
     * new Duration(4, -999999);
     * new Duration(2, 1000001);
     *
     * @param int $seconds      The number of seconds, positive or negative
     * @param int $microSeconds The microSecond adjustment to the number of seconds, positive of negative
     */
    private function __construct(int $seconds, int $microSeconds)
    {

        if ($microSeconds < (self::SECOND_IN_MICROS * -1)) {
            $overflow = (int)floor(($microSeconds * -1) / self::SECOND_IN_MICROS);
            $seconds -= $overflow;
            $microSeconds += $overflow * self::SECOND_IN_MICROS;
        }

        if ($microSeconds < 0) {
            --$seconds;
            $microSeconds = self::SECOND_IN_MICROS - ($microSeconds * -1);
        }

        if ($microSeconds >= self::SECOND_IN_MICROS) {
            $overflow = (int)floor($microSeconds / self::SECOND_IN_MICROS);
            $seconds += $overflow;
            $microSeconds -= $overflow * self::SECOND_IN_MICROS;
        }

        $this->seconds = $seconds;
        $this->microSeconds = (int)$microSeconds;
    }
}
