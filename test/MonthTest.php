<?php declare(strict_types=1);

namespace PARTest\Time;

use PAR\Core\PHPUnit\CoreAssertions;
use PAR\Time\Exception\InvalidArgumentException;
use PAR\Time\Month;
use PHPUnit\Framework\TestCase;

class MonthTest extends TestCase
{
    use CoreAssertions;

    public function testGetValue(): void
    {
        $this->assertSame(1, Month::JANUARY()->getValue());
        $this->assertSame(12, Month::DECEMBER()->getValue());
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
        $this->assertSameObject(Month::of(3), Month::JANUARY()->plus(14));
        $this->assertSameObject(Month::of(11), Month::JANUARY()->plus(-2));
        $this->assertSameObject(Month::of(1), Month::JANUARY()->plus(0));
        $this->assertSameObject(Month::of(3), Month::JANUARY()->plus(14));
    }

    public function testMinus(): void
    {
        $this->assertSameObject(Month::of(12), Month::JANUARY()->minus(1));
        $this->assertSameObject(Month::of(4), Month::JANUARY()->minus(9));
        $this->assertSameObject(Month::of(3), Month::JANUARY()->minus(-2));
        $this->assertSameObject(Month::of(1), Month::JANUARY()->minus(0));
        $this->assertSameObject(Month::of(11), Month::JANUARY()->minus(14));
    }

    public function testFirstMonthOfQuarter(): void
    {
        $this->assertSameObject(Month::JANUARY(), Month::JANUARY()->firstMonthOfQuarter());
        $this->assertSameObject(Month::JANUARY(), Month::FEBRUARY()->firstMonthOfQuarter());
        $this->assertSameObject(Month::JANUARY(), Month::MARCH()->firstMonthOfQuarter());
        $this->assertSameObject(Month::APRIL(), Month::APRIL()->firstMonthOfQuarter());
        $this->assertSameObject(Month::APRIL(), Month::MAY()->firstMonthOfQuarter());
        $this->assertSameObject(Month::APRIL(), Month::JUNE()->firstMonthOfQuarter());
        $this->assertSameObject(Month::JULY(), Month::JULY()->firstMonthOfQuarter());
        $this->assertSameObject(Month::JULY(), Month::AUGUST()->firstMonthOfQuarter());
        $this->assertSameObject(Month::JULY(), Month::SEPTEMBER()->firstMonthOfQuarter());
        $this->assertSameObject(Month::OCTOBER(), Month::OCTOBER()->firstMonthOfQuarter());
        $this->assertSameObject(Month::OCTOBER(), Month::NOVEMBER()->firstMonthOfQuarter());
        $this->assertSameObject(Month::OCTOBER(), Month::DECEMBER()->firstMonthOfQuarter());
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
        $this->assertSame($expected, $month->firstDayOfYear($leapYear));
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
}
