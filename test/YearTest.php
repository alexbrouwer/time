<?php

namespace PARTest\Time;

use PAR\Core\PHPUnit\CoreAssertions;
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
        self::assertTrue(Year::isLeap(1904)); // divisible by 4
        self::assertTrue(Year::isLeap(2000)); // divisible by 400

        self::assertFalse(Year::isLeap(1900)); // divisible by 100
        self::assertFalse(Year::isLeap(1901)); // not divisible by 4
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
        return [
            ['2000', Year::of(2000)],
            ['-2000', Year::of(-2000)],
            ['+2000', Year::of(2000)],
            ['0000', Year::of(0)],
            ['0000', Year::of(0)],
        ];
    }
}
