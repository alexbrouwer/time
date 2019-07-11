<?php declare(strict_types=1);

namespace PARTest\Time;

use PAR\Core\PHPUnit\CoreAssertions;
use PAR\Enum\PHPUnit\EnumTestCase;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\DayOfWeek;
use PAR\Time\Exception\InvalidArgumentException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Factory;

class DayOfWeekTest extends EnumTestCase
{
    use CoreAssertions;

    public function testValues(): void
    {
        self::assertSame(
            [
                DayOfWeek::MONDAY(),
                DayOfWeek::TUESDAY(),
                DayOfWeek::WEDNESDAY(),
                DayOfWeek::THURSDAY(),
                DayOfWeek::FRIDAY(),
                DayOfWeek::SATURDAY(),
                DayOfWeek::SUNDAY(),
            ],
            DayOfWeek::values()
        );
    }

    public function testGetValue(): void
    {
        self::assertSame(1, DayOfWeek::MONDAY()->getValue());
        self::assertSame(7, DayOfWeek::SUNDAY()->getValue());
    }

    public function testOfThrowsInvalidArgumentExceptionWhenValueOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DayOfWeek::of(8);
    }

    public function testOf(): void
    {
        self::assertSameObject(DayOfWeek::TUESDAY(), DayOfWeek::of(2));
    }

    public function testPlus(): void
    {
        self::assertSameObject(DayOfWeek::TUESDAY(), DayOfWeek::MONDAY()->plus(1));
        self::assertSameObject(DayOfWeek::WEDNESDAY(), DayOfWeek::MONDAY()->plus(9));
        self::assertSameObject(DayOfWeek::SATURDAY(), DayOfWeek::MONDAY()->plus(-2));
        self::assertSameObject(DayOfWeek::MONDAY(), DayOfWeek::MONDAY()->plus(0));
        self::assertSameObject(DayOfWeek::MONDAY(), DayOfWeek::MONDAY()->plus(14));
    }

    public function testMinus(): void
    {
        self::assertSameObject(DayOfWeek::SUNDAY(), DayOfWeek::MONDAY()->minus(1));
        self::assertSameObject(DayOfWeek::SATURDAY(), DayOfWeek::MONDAY()->minus(9));
        self::assertSameObject(DayOfWeek::WEDNESDAY(), DayOfWeek::MONDAY()->minus(-2));
        self::assertSameObject(DayOfWeek::MONDAY(), DayOfWeek::MONDAY()->minus(0));
        self::assertSameObject(DayOfWeek::MONDAY(), DayOfWeek::MONDAY()->minus(14));
    }

    public function testFromNative(): void
    {
        $dt = Factory::createDate(2018, 3, 4);

        self::assertSameObject(DayOfWeek::SUNDAY(), DayOfWeek::fromNative($dt));
    }

    public function testSupportsField(): void
    {
        self::assertTrue(DayOfWeek::MONDAY()->supportsField(ChronoField::DAY_OF_WEEK()));
        self::assertFalse(DayOfWeek::MONDAY()->supportsField(ChronoField::DAY_OF_MONTH()));
    }

    public function testGetForSupportedField(): void
    {
        self::assertSame(3, DayOfWeek::WEDNESDAY()->get(ChronoField::DAY_OF_WEEK()));
    }

    public function testGetForUnsupportedFieldThrowUnsupportedTemporalTypeException(): void
    {
        $this->expectException(UnsupportedTemporalTypeException::class);

        DayOfWeek::WEDNESDAY()->get(ChronoField::DAY_OF_MONTH());
    }
}
