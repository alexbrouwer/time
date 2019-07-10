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
        $this->assertSame(Month::of(3), Month::JANUARY()->plus(14));
        $this->assertSame(Month::of(11), Month::JANUARY()->plus(-2));
        $this->assertSame(Month::of(1), Month::JANUARY()->plus(0));
        $this->assertSame(Month::of(3), Month::JANUARY()->plus(14));
    }

    public function testMinus(): void
    {
        $this->assertSame(Month::of(12), Month::JANUARY()->minus(1));
        $this->assertSame(Month::of(4), Month::JANUARY()->minus(9));
        $this->assertSame(Month::of(3), Month::JANUARY()->minus(-2));
        $this->assertSame(Month::of(1), Month::JANUARY()->minus(0));
        $this->assertSame(Month::of(11), Month::JANUARY()->minus(14));
    }
}
