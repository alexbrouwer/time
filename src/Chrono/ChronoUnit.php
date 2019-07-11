<?php

namespace PAR\Time\Chrono;

use PAR\Enum\Enum;

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
final class ChronoUnit extends Enum
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
     * Checks if this unit is a date unit.
     *
     * All units from days to millenia inclusive are date-based. Time-based units and FOREVER return false.
     *
     * @return bool
     */
    public function isDateBased(): bool
    {
        return $this->dateBased;
    }

    /**
     * Checks if this unit is a time unit.
     *
     * All units from micros to half-days inclusive are time-based. Date-based units and FOREVER return false.
     *
     * @return bool
     */
    public function isTimeBased(): bool
    {
        return $this->timeBased;
    }

    /**
     * Checks if the duration of the unit is an estimate.
     *
     * All time units in this class are considered to be accurate, while all date units in this class are considered to
     * be estimated.
     *
     * This definition ignores leap seconds, but considers that Days vary due to daylight saving time and months have
     * different lengths.
     *
     * @return bool
     */
    public function isDurationEstimated(): bool
    {
        return $this->isDateBased();
    }
}
