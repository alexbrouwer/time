<?php

namespace PARTest\Time\Temporal;

use Mockery;
use PAR\Time\Exception\InvalidArgumentException;
use PAR\Time\Temporal\TemporalField;
use PAR\Time\Temporal\ValueRange;
use PHPUnit\Framework\TestCase;

class ValueRangeTest extends TestCase
{
    public function testCreateFixed(): void
    {
        $min = 0;
        $max = 10;
        $range = ValueRange::ofFixed($min, $max);

        self::assertSame($min, $range->getMinimum());
        self::assertSame($max, $range->getMaximum());
    }

    public function testCreateFixedWithMaxLessThanMinThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ValueRange::ofFixed(1, 0);
    }

    public function testCreateVariableMax(): void
    {
        $min = 0;
        $smallestMax = 28;
        $largestMax = 31;
        $range = ValueRange::ofVariableMax($min, $smallestMax, $largestMax);

        self::assertSame($min, $range->getMinimum());
        self::assertSame($smallestMax, $range->getSmallestMaximum());
        self::assertSame($largestMax, $range->getMaximum());
    }

    public function testCreateVariableMaxWithMaxLessThanMinThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ValueRange::ofVariableMax(1, 0, 1);
    }

    public function testCreateVariable(): void
    {
        $smallestMin = 0;
        $largestMin = 1;
        $smallestMax = 28;
        $largestMax = 31;
        $range = ValueRange::ofVariable($smallestMin, $largestMin, $smallestMax, $largestMax);

        self::assertSame($smallestMin, $range->getMinimum());
        self::assertSame($largestMin, $range->getLargestMinimum());
        self::assertSame($smallestMax, $range->getSmallestMaximum());
        self::assertSame($largestMax, $range->getMaximum());
    }

    public function testCreateVariableWithSmallestMinGreaterThanLargestMinThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ValueRange::ofVariable(1, 0, 3, 4);
    }

    /**
     * @dataProvider provideForTransformToString
     *
     * @param int    $smallestMin
     * @param int    $largestMin
     * @param int    $smallestMax
     * @param int    $largestMax
     * @param string $expected
     */
    public function testTransformToString(int $smallestMin, int $largestMin, int $smallestMax, int $largestMax, string $expected): void
    {
        $range = ValueRange::ofVariable($smallestMin, $largestMin, $smallestMax, $largestMax);
        self::assertSame($expected, $range->toString());
    }

    public function provideForTransformToString(): array
    {
        return [
            [0, 1, 2, 3, '0/1 - 2/3'],
            [0, 1, 3, 3, '0/1 - 3'],
            [0, 0, 3, 3, '0 - 3'],
            [0, 0, 2, 3, '0 - 2/3'],
        ];
    }

    public function testCanDetermineEquality(): void
    {
        self::assertTrue(ValueRange::ofVariable(0, 1, 2, 3)->equals(ValueRange::ofVariable(0, 1, 2, 3)));
        self::assertFalse(ValueRange::ofVariable(0, 1, 2, 3)->equals(ValueRange::ofFixed(0, 1)));
    }

    public function testCanDetermineValueIsValid(): void
    {
        $range = ValueRange::ofVariable(0, 1, 2, 3);

        self::assertFalse($range->isValidValue(-1));
        self::assertTrue($range->isValidValue(0));
        self::assertTrue($range->isValidValue(1));
        self::assertTrue($range->isValidValue(2));
        self::assertTrue($range->isValidValue(3));
        self::assertFalse($range->isValidValue(4));
    }

    public function testCheckValidValue(): void
    {
        $range = ValueRange::ofFixed(0, 5);

        $expected = 2;
        $field = Mockery::mock(TemporalField::class);

        self::assertSame($expected, $range->checkValidValue($expected, $field));
    }

    public function testCheckValidValueThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value within range 0 - 5 for MockedTemporalField, got 6');

        $range = ValueRange::ofFixed(0, 5);

        $expected = 6;
        $field = Mockery::mock(TemporalField::class);
        $field->shouldReceive('toString')->andReturn('MockedTemporalField');

        $range->checkValidValue($expected, $field);
    }
}
