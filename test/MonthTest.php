<?php declare(strict_types=1);

namespace PARTest\Time;

use PAR\Core\PHPUnit\CoreAssertions;
use PAR\Enum\PHPUnit\EnumTestCase;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Exception\InvalidArgumentException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Factory;
use PAR\Time\Month;

class MonthTest extends EnumTestCase
{
    use CoreAssertions;

    public function testValues(): void
    {
        self::assertSame(
            [
                Month::JANUARY(),
                Month::FEBRUARY(),
                Month::MARCH(),
                Month::APRIL(),
                Month::MAY(),
                Month::JUNE(),
                Month::JULY(),
                Month::AUGUST(),
                Month::SEPTEMBER(),
                Month::OCTOBER(),
                Month::NOVEMBER(),
                Month::DECEMBER(),
            ],
            Month::values()
        );
    }

    public function testGetValue(): void
    {
        self::assertSame(1, Month::JANUARY()->getValue());
        self::assertSame(12, Month::DECEMBER()->getValue());
    }

    public function testOfThrowsInvalidArgumentExceptionWhenValueOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Month::of(13);
    }

    public function testOf(): void
    {
        self::assertSameObject(Month::MARCH(), Month::of(3));
    }

    public function testPlus(): void
    {
        self::assertSameObject(Month::of(2), Month::JANUARY()->plus(1));
        self::assertSameObject(Month::of(3), Month::JANUARY()->plus(14));
        self::assertSameObject(Month::of(11), Month::JANUARY()->plus(-2));
        self::assertSameObject(Month::of(1), Month::JANUARY()->plus(0));
        self::assertSameObject(Month::of(3), Month::JANUARY()->plus(14));
    }

    public function testMinus(): void
    {
        self::assertSameObject(Month::of(12), Month::JANUARY()->minus(1));
        self::assertSameObject(Month::of(4), Month::JANUARY()->minus(9));
        self::assertSameObject(Month::of(3), Month::JANUARY()->minus(-2));
        self::assertSameObject(Month::of(1), Month::JANUARY()->minus(0));
        self::assertSameObject(Month::of(11), Month::JANUARY()->minus(14));
    }

    public function testFirstMonthOfQuarter(): void
    {
        self::assertSameObject(Month::JANUARY(), Month::JANUARY()->firstMonthOfQuarter());
        self::assertSameObject(Month::JANUARY(), Month::FEBRUARY()->firstMonthOfQuarter());
        self::assertSameObject(Month::JANUARY(), Month::MARCH()->firstMonthOfQuarter());
        self::assertSameObject(Month::APRIL(), Month::APRIL()->firstMonthOfQuarter());
        self::assertSameObject(Month::APRIL(), Month::MAY()->firstMonthOfQuarter());
        self::assertSameObject(Month::APRIL(), Month::JUNE()->firstMonthOfQuarter());
        self::assertSameObject(Month::JULY(), Month::JULY()->firstMonthOfQuarter());
        self::assertSameObject(Month::JULY(), Month::AUGUST()->firstMonthOfQuarter());
        self::assertSameObject(Month::JULY(), Month::SEPTEMBER()->firstMonthOfQuarter());
        self::assertSameObject(Month::OCTOBER(), Month::OCTOBER()->firstMonthOfQuarter());
        self::assertSameObject(Month::OCTOBER(), Month::NOVEMBER()->firstMonthOfQuarter());
        self::assertSameObject(Month::OCTOBER(), Month::DECEMBER()->firstMonthOfQuarter());
    }

    /**
     * @dataProvider provideFirstDayOfYearValues
     *
     * @param Month $month
     * @param bool  $leapYear
     * @param int   $expected
     */
    public function testFirstDayOfYear(Month $month, bool $leapYear, int $expected): void
    {
        self::assertSame($expected, $month->firstDayOfYear($leapYear));
    }

    public function provideFirstDayOfYearValues(): array
    {
        return [
            [Month::JANUARY(), false, 1],
            [Month::JANUARY(), true, 1],
            [Month::FEBRUARY(), false, 32],
            [Month::FEBRUARY(), true, 32],
            [Month::MARCH(), false, 60],
            [Month::MARCH(), true, 61],
            [Month::APRIL(), false, 91],
            [Month::APRIL(), true, 92],
            [Month::MAY(), false, 121],
            [Month::MAY(), true, 122],
            [Month::JUNE(), false, 152],
            [Month::JUNE(), true, 153],
            [Month::JULY(), false, 182],
            [Month::JULY(), true, 183],
            [Month::AUGUST(), false, 213],
            [Month::AUGUST(), true, 214],
            [Month::SEPTEMBER(), false, 244],
            [Month::SEPTEMBER(), true, 245],
            [Month::OCTOBER(), false, 274],
            [Month::OCTOBER(), true, 275],
            [Month::NOVEMBER(), false, 305],
            [Month::NOVEMBER(), true, 306],
            [Month::DECEMBER(), false, 335],
            [Month::DECEMBER(), true, 336],
        ];
    }

    public function testFromNative(): void
    {
        $dt = Factory::createDate(2018, 3, 4);

        self::assertSameObject(Month::MARCH(), Month::fromNative($dt));
    }

    public function testSupportsField(): void
    {
        self::assertTrue(Month::JUNE()->supportsField(ChronoField::MONTH_OF_YEAR()));
        self::assertFalse(Month::JUNE()->supportsField(ChronoField::DAY_OF_MONTH()));
    }

    public function testGetForSupportedField(): void
    {
        self::assertSame(4, Month::APRIL()->get(ChronoField::MONTH_OF_YEAR()));
    }

    public function testGetForUnsupportedFieldThrowUnsupportedTemporalTypeException(): void
    {
        $this->expectException(UnsupportedTemporalTypeException::class);

        Month::APRIL()->get(ChronoField::DAY_OF_MONTH());
    }
}
