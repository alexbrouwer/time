<?php

namespace PARTest\Time;

use PAR\Core\PHPUnit\CoreAssertions;
use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Duration;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\LocalDate;
use PAR\Time\Period;
use PHPUnit\Framework\TestCase;

class PeriodTest extends TestCase
{
    use CoreAssertions;

    public function provideForParse(): array
    {
        return [
            'positive'        => ['P1Y2M3D', Period::of(1, 2, 3)],
            'only-days'       => ['P3D', Period::ofDays(3)],
            'zero'            => ['P0D', Period::zero()],
            'weeks'           => ['P2W', Period::ofWeeks(2)],
            'negative-all'    => ['-P1Y2M3D', Period::of(1, 2, 3)->negated()],
            'negative-years'  => ['P-1Y2M3D', Period::of(-1, 2, 3)],
            'negative-months' => ['P1Y-2M3D', Period::of(1, -2, 3)],
            'negative-days'   => ['P1Y2M-3D', Period::of(1, 2, -3)],
        ];
    }

    public function provideForToString(): array
    {
        return [
            'positive'        => ['P1Y2M3D', Period::of(1, 2, 3)],
            'only-days'       => ['P3D', Period::ofDays(3)],
            'zero'            => ['P0D', Period::zero()],
            'negative-all'    => ['-P1Y2M3D', Period::of(1, 2, 3)->negated()],
            'negative-years'  => ['P-1Y2M3D', Period::of(-1, 2, 3)],
            'negative-months' => ['P1Y-2M3D', Period::of(1, -2, 3)],
            'negative-days'   => ['P1Y2M-3D', Period::of(1, 2, -3)],
        ];
    }

    public function testCanAddAmount(): void
    {
        self::assertSameObject(Period::ofDays(3), Period::ofDays(2)->plusAmount(Period::ofDays(1)));
    }

    public function testCanAddDays(): void
    {
        self::assertSameObject(Period::ofDays(3), Period::ofDays(2)->plusDays(1));
    }

    public function testCanAddMonths(): void
    {
        self::assertSameObject(Period::ofMonths(3), Period::ofMonths(2)->plusMonths(1));
    }

    public function testCanAddToTemporal(): void
    {
        $period = Period::ofDays(3);

        $date = LocalDate::of(2000, 1, 1);

        self::assertSameObject(LocalDate::of(2000, 1, 4), $period->addTo($date));
    }

    public function testCanAddYears(): void
    {
        self::assertSameObject(Period::ofYears(3), Period::ofYears(2)->plusYears(1));
    }

    /**
     * @dataProvider provideForToString
     *
     * @param string $expected
     * @param Period $period
     */
    public function testCanBeCastToString(string $expected, Period $period): void
    {
        self::assertSame($expected, $period->toString());
    }

    public function testCanBeMultipliedByNegativeValue(): void
    {
        $period = Period::ofDays(2);

        self::assertSameObject(Period::ofDays(-6), $period->multipliedBy(-3));
    }

    public function testCanBeMultipliedByPositiveValue(): void
    {
        $period = Period::ofDays(2);

        self::assertSameObject(Period::ofDays(6), $period->multipliedBy(3));
    }

    public function testCanDetermineNegativity(): void
    {
        self::assertTrue(Period::ofDays(-3)->isNegative());
        self::assertFalse(Period::ofDays(3)->isNegative());

        self::assertTrue(Period::ofWeeks(-3)->isNegative());
        self::assertFalse(Period::ofWeeks(3)->isNegative());

        self::assertTrue(Period::ofMonths(-3)->isNegative());
        self::assertFalse(Period::ofMonths(3)->isNegative());

        self::assertTrue(Period::ofYears(-3)->isNegative());
        self::assertFalse(Period::ofYears(3)->isNegative());
    }

    public function testCanNegateNegative(): void
    {
        $period = Period::ofDays(-1);
        $negated = $period->negated();

        self::assertTrue($period->isNegative());
        self::assertFalse($negated->isNegative());
        self::assertNotSame($period, $negated);
    }

    public function testCanNegatePositive(): void
    {
        $period = Period::ofDays(1);
        $negated = $period->negated();

        self::assertFalse($period->isNegative());
        self::assertTrue($negated->isNegative());
        self::assertNotSame($period, $negated);
    }

    public function testCanNormalize(): void
    {
        $period = Period::of(1, 15, 35);

        self::assertSameObject(Period::of(2, 3, 35), $period->normalized());
    }

    /**
     * @dataProvider provideForParse
     *
     * @param string   $text
     * @param Duration $expected
     */
    public function testCanParseString(string $text, Period $expected): void
    {
        $duration = Period::parse($text);

        self::assertSameObject($expected, $duration);
    }

    public function testCanRetrieveTotalAmountOfMonths(): void
    {
        $period = Period::of(2, 2, 2);

        self::assertSame(26, $period->toTotalMonths());
    }

    public function testCanSubtractAmount(): void
    {
        self::assertSameObject(Period::ofDays(1), Period::ofDays(2)->minusAmount(Period::ofDays(1)));
    }

    public function testCanSubtractDays(): void
    {
        self::assertSameObject(Period::ofDays(1), Period::ofDays(2)->minusDays(1));
    }

    public function testCanSubtractFromTemporal(): void
    {
        $period = Period::ofDays(3);

        $date = LocalDate::of(2000, 1, 5);

        self::assertSameObject(LocalDate::of(2000, 1, 2), $period->subtractFrom($date));
    }

    public function testCanSubtractMonths(): void
    {
        self::assertSameObject(Period::ofMonths(1), Period::ofMonths(2)->minusMonths(1));
    }

    public function testCanSubtractYears(): void
    {
        self::assertSameObject(Period::ofYears(1), Period::ofYears(2)->minusYears(1));
    }

    public function testChangeDays(): void
    {
        $period = Period::ofDays(1);

        $changed = $period->withDays(2);
        self::assertNotSame($period, $changed);
        self::assertSame(2, $changed->getDays());
        self::assertSame($period->getYears(), $changed->getYears());
        self::assertSame($period->getMonths(), $changed->getMonths());
    }

    public function testChangeMonths(): void
    {
        $period = Period::ofMonths(1);

        $changed = $period->withMonths(2);
        self::assertNotSame($period, $changed);
        self::assertSame(2, $changed->getMonths());
        self::assertSame($period->getYears(), $changed->getYears());
        self::assertSame($period->getDays(), $changed->getDays());
    }

    public function testChangeYears(): void
    {
        $period = Period::ofYears(1);

        $changed = $period->withYears(2);
        self::assertNotSame($period, $changed);
        self::assertSame(2, $changed->getYears());
        self::assertSame($period->getMonths(), $changed->getMonths());
        self::assertSame($period->getDays(), $changed->getDays());
    }

    public function testCreateFromOtherAmount(): void
    {
        self::assertSameObject(Period::of(1, 2, 3), Period::from(Period::of(1, 2, 3)));
        self::assertSameObject(Period::zero(), Period::from(Duration::ofDays(1)));
    }

    public function testCreateWithOnlyDays(): void
    {
        $period = Period::ofDays(2);

        self::assertSame(0, $period->getYears());
        self::assertSame(0, $period->getMonths());
        self::assertSame(2, $period->getDays());
    }

    public function testCreateWithOnlyMonths(): void
    {
        $period = Period::ofMonths(2);

        self::assertSame(0, $period->getYears());
        self::assertSame(2, $period->getMonths());
        self::assertSame(0, $period->getDays());
    }

    public function testCreateWithOnlyWeeks(): void
    {
        $period = Period::ofWeeks(2);

        self::assertSame(0, $period->getYears());
        self::assertSame(0, $period->getMonths());
        self::assertSame(14, $period->getDays());
    }

    public function testCreateWithOnlyYears(): void
    {
        $period = Period::ofYears(2);

        self::assertSame(2, $period->getYears());
        self::assertSame(0, $period->getMonths());
        self::assertSame(0, $period->getDays());
    }

    public function testRetrieveListOfUnits(): void
    {
        $period = Period::ofDays(1);
        $units = $period->getUnits();

        self::assertCount(3, $units);
        self::assertSameObject(ChronoUnit::YEARS(), $units[0]);
        self::assertSameObject(ChronoUnit::MONTHS(), $units[1]);
        self::assertSameObject(ChronoUnit::DAYS(), $units[2]);
    }

    public function testRetrieveValueForUnit(): void
    {
        $period = Period::of(1, 2, 3);

        self::assertSame(1, $period->get(ChronoUnit::YEARS()));
        self::assertSame(2, $period->get(ChronoUnit::MONTHS()));
        self::assertSame(3, $period->get(ChronoUnit::DAYS()));
    }

    public function testRetrieveValueForUnsupportedUnitThrowsException(): void
    {
        $this->expectException(UnsupportedTemporalTypeException::class);

        $period = Period::of(1, 2, 3);
        $period->get(ChronoUnit::WEEKS());
    }

    public function testZero(): void
    {
        $period = Period::zero();

        self::assertSame(0, $period->getYears());
        self::assertSame(0, $period->getMonths());
        self::assertSame(0, $period->getDays());
        self::assertTrue($period->isZero());
    }
}
