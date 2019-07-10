<?php declare(strict_types=1);

namespace PARTest\Time;

use PAR\Time\DayOfWeek;
use PAR\Time\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DayOfWeekTest extends TestCase
{
    public function testGetValue(): void
    {
        $this->assertSame(1, DayOfWeek::MONDAY()->getValue());
        $this->assertSame(7, DayOfWeek::SUNDAY()->getValue());
    }

    public function testOfThrowsInvalidArgumentExceptionWhenValueOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DayOfWeek::of(8);
    }

    public function testOf(): void
    {
        $this->assertSame(DayOfWeek::TUESDAY(), DayOfWeek::of(2));
    }

    public function testPlus(): void
    {
        $this->assertSame(DayOfWeek::TUESDAY(), DayOfWeek::MONDAY()->plus(1));
        $this->assertSame(DayOfWeek::WEDNESDAY(), DayOfWeek::MONDAY()->plus(9));
        $this->assertSame(DayOfWeek::SATURDAY(), DayOfWeek::MONDAY()->plus(-2));
        $this->assertSame(DayOfWeek::MONDAY(), DayOfWeek::MONDAY()->plus(0));
        $this->assertSame(DayOfWeek::MONDAY(), DayOfWeek::MONDAY()->plus(14));
    }

    public function testMinus(): void
    {
        $this->assertSame(DayOfWeek::SUNDAY(), DayOfWeek::MONDAY()->minus(1));
        $this->assertSame(DayOfWeek::SATURDAY(), DayOfWeek::MONDAY()->minus(9));
        $this->assertSame(DayOfWeek::WEDNESDAY(), DayOfWeek::MONDAY()->minus(-2));
        $this->assertSame(DayOfWeek::MONDAY(), DayOfWeek::MONDAY()->minus(0));
        $this->assertSame(DayOfWeek::MONDAY(), DayOfWeek::MONDAY()->minus(14));
    }
}
