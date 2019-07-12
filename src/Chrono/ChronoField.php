<?php

namespace PAR\Time\Chrono;

use DateTimeInterface;
use PAR\Enum\Enum;
use PAR\Time\Temporal\TemporalAccessor;
use PAR\Time\Temporal\TemporalField;
use PAR\Time\Temporal\ValueRange;
use PAR\Time\Year;

/**
 * A standard set of fields.
 *
 * This set of fields provide field-based access to manipulate a date, time or date-time.
 *
 * @method static self DAY_OF_WEEK()
 * @method static self DAY_OF_MONTH()
 * @method static self MONTH_OF_YEAR()
 * @method static self YEAR()
 */
final class ChronoField extends Enum implements TemporalField
{
    protected const DAY_OF_WEEK = ['DAYS', 'WEEKS', [1, 7], 'N'];
    protected const DAY_OF_MONTH = ['DAYS', 'MONTHS', [1, 28, 31], 'j'];
    protected const MONTH_OF_YEAR = ['MONTHS', 'YEARS', [1, 12], 'n'];
    protected const YEAR = ['YEARS', 'FOREVER', [Year::MIN_VALUE, Year::MAX_VALUE], 'Y'];

    /**
     * @var string
     */
    private $baseUnit;

    /**
     * @var string
     */
    private $rangeUnit;

    /**
     * @var string
     */
    private $format;

    /**
     * @var array
     */
    private $rangeValues;

    /**
     * @param string     $baseUnit
     * @param string     $rangeUnit
     * @param array<int> $rangeValues
     * @param string     $format
     */
    protected function __construct(string $baseUnit, string $rangeUnit, array $rangeValues, string $format)
    {
        $this->baseUnit = $baseUnit;
        $this->rangeUnit = $rangeUnit;
        $this->rangeValues = $rangeValues;
        $this->format = $format;
    }

    /**
     * @inheritDoc
     *
     * @return ChronoUnit
     */
    public function getBaseUnit(): ChronoUnit
    {
        return ChronoUnit::valueOf($this->baseUnit);
    }

    /**
     * @inheritDoc
     *
     * @return ChronoUnit
     */
    public function getRangeUnit(): ChronoUnit
    {
        return ChronoUnit::valueOf($this->rangeUnit);
    }

    /**
     * @inheritDoc
     */
    public function getFromNative(DateTimeInterface $dateTime): int
    {
        return (int)$dateTime->format($this->format);
    }

    /**
     * @inheritDoc
     */
    public function range(): ValueRange
    {
        if (empty($this->rangeValues)) {
            $this->rangeValues = [PHP_INT_MIN, PHP_INT_MAX];
        }

        if (count($this->rangeValues) >= 4) {
            return forward_static_call_array([ValueRange::class, 'ofVariable'], $this->rangeValues);
        }
        if (count($this->rangeValues) >= 3) {
            return forward_static_call_array([ValueRange::class, 'ofVariableMax'], $this->rangeValues);
        }

        return forward_static_call_array([ValueRange::class, 'ofFixed'], $this->rangeValues);
    }

    /**
     * @inheritDoc
     */
    public function isSupportedBy(TemporalAccessor $temporalAccessor): bool
    {
        return $temporalAccessor->supportsField($this);
    }
}
