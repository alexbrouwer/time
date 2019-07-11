<?php

namespace PAR\Time\Chrono;

use PAR\Enum\Enum;
use PAR\Time\Temporal\TemporalUnit;

/**
 * A standard set of date periods units.
 *
 * @method static self MICROS()
 * @method static self MILLIS()
 * @method static self SECONDS()
 * @method static self MINUTES()
 * @method static self HOURS()
 * @method static self HALF_DAYS()
 * @method static self DAYS()
 * @method static self WEEKS()
 * @method static self MONTHS()
 * @method static self YEARS()
 * @method static self DECADES()
 * @method static self CENTURIES()
 * @method static self MILLENIA()
 * @method static self FOREVER()
 */
final class ChronoUnit extends Enum implements TemporalUnit
{
    protected const MICROS = [false, true];
    protected const MILLIS = [false, true];
    protected const SECONDS = [false, true];
    protected const MINUTES = [false, true];
    protected const HOURS = [false, true];
    protected const HALF_DAYS = [false, true];
    protected const DAYS = [true, false];
    protected const WEEKS = [true, false];
    protected const MONTHS = [true, false];
    protected const YEARS = [true, false];
    protected const DECADES = [true, false];
    protected const CENTURIES = [true, false];
    protected const MILLENIA = [true, false];
    protected const FOREVER = [false, false];

    /**
     * @var bool
     */
    private $dateBased;

    /**
     * @var bool
     */
    private $timeBased;

    /**
     * @param bool $dateBased
     * @param bool $timeBased
     */
    protected function __construct(bool $dateBased, bool $timeBased)
    {
        $this->dateBased = $dateBased;
        $this->timeBased = $timeBased;
    }

    /**
     * @inheritDoc
     */
    public function isDateBased(): bool
    {
        return $this->dateBased;
    }

    /**
     * @inheritDoc
     */
    public function isTimeBased(): bool
    {
        return $this->timeBased;
    }

    /**
     * @inheritDoc
     */
    public function isDurationEstimated(): bool
    {
        return $this->isDateBased();
    }

}
