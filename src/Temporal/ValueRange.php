<?php

namespace PAR\Time\Temporal;

use PAR\Core\ObjectInterface;
use PAR\Time\Assertion;
use PAR\Time\Exception\InvalidArgumentException;

class ValueRange implements ObjectInterface
{
    /**
     * @var int
     */
    private $smallestMinimum;

    /**
     * @var int
     */
    private $largestMinimum;

    /**
     * @var int
     */
    private $smallestMaximum;

    /**
     * @var int
     */
    private $largestMaximum;

    /**
     * Obtains a fixed value range.
     *
     * This factory obtains a range where the minimum and maximum values are fixed. For example, the ISO month-of-year always runs from 1 to 12.
     *
     * @param int $min The minimum value
     * @param int $max The maximum value
     *
     * @return ValueRange
     * @throws InvalidArgumentException If the minimum is greater than the maximum.
     */
    public static function ofFixed(int $min, int $max): self
    {
        return new self($min, $min, $max, $max);
    }

    /**
     * Obtains a fully variable value range.
     *
     * This factory obtains a range where both the minimum and maximum value may vary.
     *
     * @param int $smallestMin The smallest minimum value
     * @param int $largestMin  The largest minimum value
     * @param int $smallestMax The smallest maximum value
     * @param int $largestMax  The largest maximum value
     *
     * @return ValueRange
     * @throws InvalidArgumentException If the smallest minimum is greater than the smallest maximum, or the smallest
     * maximum is greater than the largest maximum or the largest minimum is greater than the largest maximum.
     */
    public static function ofVariable(int $smallestMin, int $largestMin, int $smallestMax, int $largestMax): self
    {
        return new self($smallestMin, $largestMin, $smallestMax, $largestMax);
    }

    /**
     * Obtains a variable value range.
     *
     * This factory obtains a range where the minimum value is fixed and the maximum value may vary. For example, the
     * ISO day-of-month always starts at 1, but ends between 28 and 31.
     *
     * @param int $min         The minimum value
     * @param int $smallestMax The smallest maximum value
     * @param int $largestMax  The largest maximum value
     *
     * @return ValueRange
     * @throws InvalidArgumentException If minimum is greater than the smallest maximum, or the smallest
     * maximum is greater than the largest maximum or the minimum is greater than the largest maximum.
     */
    public static function ofVariableMax(int $min, int $smallestMax, int $largestMax): self
    {
        return new self($min, $min, $smallestMax, $largestMax);
    }

    /**
     * @param int $smallestMinimum
     * @param int $largestMinimum
     * @param int $smallestMaximum
     * @param int $largestMaximum
     */
    private function __construct(int $smallestMinimum, int $largestMinimum, int $smallestMaximum, int $largestMaximum)
    {
        Assertion::lessOrEqualThan($smallestMinimum, $largestMinimum);
        Assertion::lessThan($largestMinimum, $smallestMaximum);
        Assertion::lessOrEqualThan($largestMinimum, $largestMaximum);

        $this->smallestMinimum = min($smallestMinimum, $largestMinimum);
        $this->largestMinimum = max($largestMinimum, $smallestMinimum);
        $this->smallestMaximum = min($smallestMaximum, $largestMaximum);
        $this->largestMaximum = max($largestMaximum, $smallestMaximum);
    }

    /**
     * @inheritDoc
     */
    public function equals($other): bool
    {
        if ($other instanceof self && get_class($other) === static::class) {
            return $this->smallestMinimum === $other->smallestMinimum
                && $this->largestMinimum === $other->largestMinimum
                && $this->smallestMaximum === $other->smallestMaximum
                && $this->largestMaximum === $other->largestMaximum;
        }

        return false;
    }

    /**
     * The format will be '{min}/{largestMin} - {smallestMax}/{max}', where the largestMin or smallestMax sections may
     * be omitted, together with associated slash, if they are the same as the min or max.
     *
     * @return string
     */
    public function toString(): string
    {
        $text = $this->getMinimum();
        if ($this->getMinimum() !== $this->getLargestMinimum()) {
            $text .= '/' . $this->getLargestMinimum();
        }

        $text .= ' - ' . $this->getSmallestMaximum();
        if ($this->getSmallestMaximum() !== $this->getMaximum()) {
            $text .= '/' . $this->getMaximum();
        }

        return $text;
    }

    /**
     * Gets the minimum value that the field can take.
     *
     * For example, the ISO day-of-month always starts at 1. The minimum is therefore 1.
     *
     * @return int
     */
    public function getMinimum(): int
    {
        return $this->smallestMinimum;
    }

    /**
     * Gets the largest possible minimum value that the field can take.
     *
     * For example, the ISO day-of-month always starts at 1. The largest minimum is therefore 1.
     *
     * @return int
     */
    public function getLargestMinimum(): int
    {
        return $this->largestMinimum;
    }

    /**
     * Gets the maximum value that the field can take.
     *
     * For example, the ISO day-of-month runs to between 28 and 31 days. The maximum is therefore 31.
     *
     * @return int
     */
    public function getMaximum(): int
    {
        return $this->largestMaximum;
    }

    /**
     * Gets the smallest possible maximum value that the field can take.
     *
     * For example, the ISO day-of-month runs to between 28 and 31 days. The smallest maximum is therefore 28.
     *
     * @return int
     */
    public function getSmallestMaximum(): int
    {
        return $this->smallestMaximum;
    }

    /**
     * Checks if the value is within the valid range.
     *
     * This checks that the value is within the stored range of values.
     *
     * @param int $value The value to check.
     *
     * @return bool
     */
    public function isValidValue(int $value): bool
    {
        return $value >= $this->getMinimum() && $value <= $this->getMaximum();
    }
}
