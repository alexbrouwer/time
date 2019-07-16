<?php declare(strict_types=1);

namespace PAR\Time;

use PAR\Core\ComparableInterface;
use PAR\Core\Exception\ClassMismatchException;
use PAR\Core\ObjectInterface;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Temporal\TemporalField;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A year-month in the ISO-8601 calendar system, such as 2007-12.
 */
final class YearMonth implements Temporal, ObjectInterface, ComparableInterface
{
    /**
     * @var int
     */
    private $year;

    /**
     * @var int
     */
    private $month;

    public static function of(int $year, int $month): self
    {
        return new self($year, $month);
    }

    /**
     * @param int $year
     * @param int $month
     */
    public function __construct(int $year, int $month)
    {
        $this->year = Year::of($year)->getValue();
        $this->month = Month::of($month)->getValue();
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
    public function toString(): string
    {
        return sprintf('%s-%s', $this->year, $this->month);
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): Temporal
    {
        // TODO: Implement plus() method.
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function plusAmount(TemporalAmount $amount): Temporal
    {
        // TODO: Implement plusAmount() method.
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit): Temporal
    {
        // TODO: Implement minus() method.
    }

    /**
     * @inheritDoc
     *
     * @return self
     */
    public function minusAmount(TemporalAmount $amount): Temporal
    {
        // TODO: Implement minusAmount() method.
    }

    /**
     * @inheritDoc
     */
    public function supportsUnit(TemporalUnit $unit): bool
    {
        // TODO: Implement supportsUnit() method.
    }

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        // TODO: Implement supportsField() method.
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        // TODO: Implement get() method.
    }
}
