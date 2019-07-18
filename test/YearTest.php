<?php

namespace PARTest\Time;

use Mockery;
use PAR\Core\PHPUnit\CoreAssertions;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Exception\InvalidArgumentException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Factory;
use PAR\Time\LocalDate;
use PAR\Time\Month;
use PAR\Time\Temporal\TemporalAccessor;
use PAR\Time\Temporal\TemporalAmount;
use PAR\Time\Year;
use PAR\Time\YearMonth;

class YearTest extends TimeTestCase
{
    use CoreAssertions;

    public function provideForParse(): array
    {
        $data = [
            ['2000', Year::of(2000)],
            ['-2000', Year::of(-2000)],
            ['+2000', Year::of(2000)],
            ['0000', Year::of(0)],
        ];

        return $data;
    }

    public function provideSupportedFields(): array
    {
        $supported = [
            ChronoField::YEAR(),
        ];

        $map = ChronoFieldHelper::createMapWithAll('bool', false);
        $map = ChronoFieldHelper::updateAllIn($map, $supported, true);

        return ChronoFieldHelper::toProviderArray($map);
    }

    public function testAddingUnsupportedUnitThrowsException(): void
    {
        $this->expectException(UnsupportedTemporalTypeException::class);

        Year::of(2000)->plus(1, ChronoUnit::MONTHS());
    }

    public function testCanAddAmount(): void
    {
        $year = Year::of(2000);

        $amount = Mockery::mock(TemporalAmount::class);
        $amount->shouldReceive('addTo')
            ->with($year)
            ->andReturn(Year::of(2010));

        $result = $year->plusAmount($amount);
        $this->assertNotSame($year, $result);
    }

    public function testCanAddUnit(): void
    {
        self::assertSameObject(Year::of(2001), Year::of(2000)->plus(1, ChronoUnit::YEARS()));
        self::assertSameObject(Year::of(2010), Year::of(2000)->plus(1, ChronoUnit::DECADES()));
        self::assertSameObject(Year::of(2100), Year::of(2000)->plus(1, ChronoUnit::CENTURIES()));
        self::assertSameObject(Year::of(3000), Year::of(2000)->plus(1, ChronoUnit::MILLENNIA()));
    }

    public function testCanAddYears(): void
    {
        self::assertSameObject(Year::of(2003), Year::of(2000)->plusYears(3));
        self::assertSameObject(Year::of(1997), Year::of(2000)->plusYears(-3));
    }

    public function testCanBeComparedWithOther(): void
    {
        $this->assertSame(0, Year::of(2000)->compareTo(Year::of(2000)));
        $this->assertSame(10, Year::of(2000)->compareTo(Year::of(1990)));
        $this->assertSame(-8, Year::of(2000)->compareTo(Year::of(2008)));
    }

    public function testCanCreateFromInteger(): void
    {
        $expected = 2019;
        $year = Year::of($expected);

        self::assertSame($expected, $year->getValue());
        self::assertSame((string)$expected, $year->toString());
    }

    public function testCanCreateFromNow(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $now = Factory::now();

                $currentYear = Year::now();

                self::assertSameYear($now, $currentYear->getValue());
            }
        );
    }

    /**
     * @dataProvider provideForParse
     *
     * @param string $text
     * @param Year   $expectedYear
     */
    public function testCanCreateFromString(string $text, Year $expectedYear): void
    {
        self::assertSameObject($expectedYear, Year::parse($text));
    }

    public function testCanCreateFromTemporal(): void
    {
        $temporal = \Mockery::mock(TemporalAccessor::class);
        $temporal->shouldReceive('get')->with(ChronoField::YEAR())->andReturn(2000);

        self::assertSameObject(Year::of(2000), Year::from($temporal));
    }

    public function testCanCreateOfNative(): void
    {
        $expected = 2018;
        $dt = Factory::createDate($expected, 3, 4);

        $year = Year::ofNative($dt);

        self::assertSameYear($dt, $year->getValue());
    }

    public function testCanDetermineEquality(): void
    {
        self::assertTrue(Year::of(2000)->equals(Year::of(2000)));
        self::assertFalse(Year::of(2001)->equals(Year::of(2000)));
        self::assertFalse(Year::of(2000)->equals(Year::of(2001)));
        self::assertFalse(Year::of(2000)->equals(null));
    }

    public function testCanDetermineSelfIsAfter(): void
    {
        $this->assertTrue(Year::of(2000)->isAfter(Year::of(1995)));
        $this->assertFalse(Year::of(2000)->isAfter(Year::of(2000)));
        $this->assertFalse(Year::of(2000)->isAfter(Year::of(2010)));
    }

    public function testCanDetermineSelfIsBefore(): void
    {
        $this->assertTrue(Year::of(2000)->isBefore(Year::of(2009)));
        $this->assertFalse(Year::of(2000)->isBefore(Year::of(2000)));
        $this->assertFalse(Year::of(2000)->isBefore(Year::of(1995)));
    }

    public function testCanDetermineYearIsLeapYear(): void
    {
        self::assertTrue(Year::of(1904)->isLeap()); // divisible by 4
        self::assertTrue(Year::of(2000)->isLeap()); // divisible by 400

        self::assertFalse(Year::of(1900)->isLeap()); // divisible by 100
        self::assertFalse(Year::of(1901)->isLeap()); // not divisible by 4
    }

    public function testCanRetrieveLengthOfYearInDays(): void
    {
        $this->assertSame(365, Year::of(1995)->length());
        $this->assertSame(366, Year::of(2000)->length());
    }

    /**
     * @dataProvider provideSupportedFields
     *
     * @param ChronoField $field
     * @param bool        $expected
     */
    public function testCanRetrieveListOfSupportedUnits(ChronoField $field, bool $expected): void
    {
        $year = Year::of(2000);

        $this->assertSame($expected, $year->supportsField($field));
    }

    public function testCanRetrieveValueOfUnit(): void
    {
        $expected = 2015;
        $year = Year::of($expected);

        $this->assertSame($expected, $year->get(ChronoField::YEAR()));
    }

    public function testCanSubtractAmount(): void
    {
        $year = Year::of(2010);

        $amount = Mockery::mock(TemporalAmount::class);
        $amount->shouldReceive('subtractFrom')
            ->with($year)
            ->andReturn(Year::of(2000));

        $result = $year->minusAmount($amount);
        $this->assertNotSame($year, $result);
    }

    public function testCanSubtractUnit(): void
    {
        self::assertSameObject(Year::of(1999), Year::of(2000)->minus(1, ChronoUnit::YEARS()));
        self::assertSameObject(Year::of(1990), Year::of(2000)->minus(1, ChronoUnit::DECADES()));
        self::assertSameObject(Year::of(1900), Year::of(2000)->minus(1, ChronoUnit::CENTURIES()));
        self::assertSameObject(Year::of(1000), Year::of(2000)->minus(1, ChronoUnit::MILLENNIA()));
    }

    public function testCanSubtractYears(): void
    {
        self::assertSameObject(Year::of(1997), Year::of(2000)->minusYears(3));
        self::assertSameObject(Year::of(2003), Year::of(2000)->minusYears(-3));
    }

    public function testCanTransformToString(): void
    {
        self::assertSameObject(LocalDate::of(2000, 4, 10), Year::of(2000)->atDay(100));
    }

    public function testCanTransformToYearMonth(): void
    {
        self::assertSameObject(YearMonth::of(2000, 4), Year::of(2000)->atMonth(Month::of(4)));
    }

    public function testCreatingFromInvalidStringFormatThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Year::parse('The year 2000');
    }

    public function testCreatingWithIntegerLargerThanMaxThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Year::of(Year::MAX_VALUE + 1);
    }

    public function testCreatingWithIntegerSmallerThaMinThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Year::of(Year::MIN_VALUE - 1);
    }

    public function testDetermineIntegerIsLeapYear(): void
    {
        self::assertTrue(Year::isLeapYear(1904)); // divisible by 4
        self::assertTrue(Year::isLeapYear(2000)); // divisible by 400

        self::assertFalse(Year::isLeapYear(1900)); // divisible by 100
        self::assertFalse(Year::isLeapYear(1901)); // not divisible by 4

        self::assertFalse(Year::isLeapYear(0)); // not divisible at all
    }

    public function testRetrievingUnsupportedUnitThrowsException(): void
    {
        $this->expectException(UnsupportedTemporalTypeException::class);

        Year::of(2000)->get(ChronoField::MONTH_OF_YEAR());
    }

    public function testSubtractingUnsupportedUnitThrowsException(): void
    {
        $this->expectException(UnsupportedTemporalTypeException::class);

        Year::of(2000)->minus(1, ChronoUnit::MONTHS());
    }
}
