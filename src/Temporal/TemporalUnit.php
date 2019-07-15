<?php

namespace PAR\Time\Temporal;

use PAR\Core\ComparableInterface;
use PAR\Time\Duration;

interface TemporalUnit extends ComparableInterface
{
    /**
     * Checks if this unit is a date unit.
     *
     * All units from days to millenia inclusive are date-based. Time-based units and FOREVER return false.
     *
     * @return bool
     */
    public function isDateBased(): bool;

    /**
     * Checks if this unit is a time unit.
     *
     * All units from micros to half-days inclusive are time-based. Date-based units and FOREVER return false.
     *
     * @return bool
     */
    public function isTimeBased(): bool;

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
    public function isDurationEstimated(): bool;

    /**
     * Gets the amount of this unit, which may be an estimate.
     *
     * @return Duration
     */
    public function getDuration(): Duration;
}
