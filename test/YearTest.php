<?php

namespace PARTest\Time;

use PAR\Core\PHPUnit\CoreAssertions;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Exception\InvalidArgumentException;
use PAR\Time\Factory;
use PAR\Time\Year;

class YearTest extends TimeTestCase
{
    use CoreAssertions;

    public function testOf(): void
    {
        $expected = 2019;
        $year = Year::of($expected);

        self::assertSame($expected, $year->getValue());
        self::assertSame((string)$expected, $year->toString());
    }

    public function testEquals(): void
    {
        self::assertTrue(Year::of(2000)->equals(Year::of(2000)));
        self::assertFalse(Year::of(2001)->equals(Year::of(2000)));
        self::assertFalse(Year::of(2000)->equals(Year::of(2001)));
    }

    public function testOfWithValueLargerThanMaxThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Year::of(Year::MAX_VALUE + 1);
    }

    public function testOfWithValueSmallerThaMinThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Year::of(Year::MIN_VALUE - 1);
    }

    public function testIsLeap(): void
    {
        self::assertTrue(Year::isLeapYear(1904)); // divisible by 4
        self::assertTrue(Year::isLeapYear(2000)); // divisible by 400

        self::assertFalse(Year::isLeapYear(1900)); // divisible by 100
        self::assertFalse(Year::isLeapYear(1901)); // not divisible by 4
    }

    public function testIsLeapYear(): void
    {
        self::assertTrue(Year::of(1904)->isLeap()); // divisible by 4
        self::assertTrue(Year::of(2000)->isLeap()); // divisible by 400

        self::assertFalse(Year::of(1900)->isLeap()); // divisible by 100
        self::assertFalse(Year::of(1901)->isLeap()); // not divisible by 4
    }

    public function testFromNative(): void
    {
        $expected = 2018;
        $dt = Factory::createDate($expected, 3, 4);

        $year = Year::fromNative($dt);

        self::assertSameYear($dt, $year->getValue());
    }

    public function testNow(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $now = Factory::now();

                $currentYear = Year::now();

                self::assertSameYear($now, $currentYear->getValue());
            }
        );
    }

    public function provideSupportedFields(): array
    {
        $supported = [
            ChronoField::YEAR(),
        ];

        return SupportedProvider::fields($supported);
    }

    /**
     * @dataProvider provideSupportedFields
     *
     * @param ChronoField $field
     * @param bool        $expected
     */
    public function testSupportsFields(ChronoField $field, bool $expected): void
    {
        $year = Year::of(2000);

        $this->assertSame($expected, $year->supportsField($field));
    }

    public function testGet()
    {
        $expected = 2015;
        $year = Year::of($expected);

        $this->assertSame($expected, $year->get(ChronoField::YEAR()));
    }

    /**
     * @dataProvider provideForParse
     *
     * @param string $text
     * @param Year   $expectedYear
     */
    public function testParse(string $text, Year $expectedYear): void
    {
        self::assertSameObject($expectedYear, Year::parse($text));
    }

    public function testParseThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Year::parse('The year 2000');
    }

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

    public function testCompareTo(): void
    {
        $this->assertSame(0, Year::of(2000)->compareTo(Year::of(2000)));
        $this->assertSame(10, Year::of(2000)->compareTo(Year::of(1990)));
        $this->assertSame(-8, Year::of(2000)->compareTo(Year::of(2008)));
    }

    public function testIsBefore(): void
    {
        $this->assertTrue(Year::of(2000)->isBefore(Year::of(2009)));
        $this->assertFalse(Year::of(2000)->isBefore(Year::of(2000)));
        $this->assertFalse(Year::of(2000)->isBefore(Year::of(1995)));
    }

    public function testIsAfter(): void
    {
        $this->assertTrue(Year::of(2000)->isAfter(Year::of(1995)));
        $this->assertFalse(Year::of(2000)->isAfter(Year::of(2000)));
        $this->assertFalse(Year::of(2000)->isAfter(Year::of(2010)));
    }

    public function testLength(): void
    {
        $this->assertSame(365, Year::of(1995)->length());
        $this->assertSame(366, Year::of(2000)->length());
    }
}
