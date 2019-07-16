<?php /** @noinspection ReturnTypeCanBeDeclaredInspection */

namespace PAR\Time\Temporal;

interface Temporal extends TemporalAccessor
{
    /**
     * Returns an object of the same type as this object with the specified period added.
     *
     * This method returns a new object based on this one with the specified period subtracted. For example, on a LocalDate,
     * this could be used to subtract a number of years, months or days.
     *
     * @param int          $amountToSubtract The amount of the specified unit to subtract
     * @param TemporalUnit $unit             The unit of the amount to add
     *
     * @return self
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit);

    /**
     * Returns an object of the same type as this object with an amount subtracted.
     *
     * This adjusts this temporal, subtracting according to the rules of the specified amount. The amount is typically a
     * Period but may be any other type implementing the TemporalAmount interface, such as Duration.
     *
     * @param TemporalAmount $amount The amount to subtract
     *
     * @return self
     */
    public function minusAmount(TemporalAmount $amount);

    /**
     * Returns an object of the same type as this object with the specified period added.
     *
     * This method returns a new object based on this one with the specified period added. For example, on a LocalDate,
     * this could be used to add a number of years, months or days.
     *
     * @param int          $amountToAdd The amount of the specified unit to add
     * @param TemporalUnit $unit        The unit of the amount to add
     *
     * @return self
     */
    public function plus(int $amountToAdd, TemporalUnit $unit);

    /**
     * Returns an object of the same type as this object with an amount added.
     *
     * This adjusts this temporal, adding according to the rules of the specified amount. The amount is typically a
     * Period but may be any other type implementing the TemporalAmount interface, such as Duration.
     *
     * @param TemporalAmount $amount The amount to add
     *
     * @return self
     */
    public function plusAmount(TemporalAmount $amount);

    /**
     * Checks if the specified unit is supported.
     *
     * @param TemporalUnit $unit
     *
     * @return bool
     */
    public function supportsUnit(TemporalUnit $unit): bool;
}
