<?php

namespace PARTest\Time\Chrono;

use Mockery;
use PAR\Core\PHPUnit\CoreAssertions;
use PAR\Enum\PHPUnit\EnumTestCase;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Factory;
use PAR\Time\Temporal\TemporalAccessor;
use PAR\Time\Temporal\ValueRange;

class ChronoFieldTest extends EnumTestCase
{
    use CoreAssertions;

    public function testValues(): void
    {
        self::assertSame(
            [
                ChronoField::DAY_OF_WEEK(),
                ChronoField::DAY_OF_MONTH(),
                ChronoField::MONTH_OF_YEAR(),
            ],
            ChronoField::values()
        );
    }

    public function testDayOfWeek(): void
    {
        $field = ChronoField::DAY_OF_WEEK();

        $native = Factory::create(2018, 3, 4, 5, 6, 7);

        self::assertSameObject(ChronoUnit::DAYS(), $field->getBaseUnit());
        self::assertSameObject(ChronoUnit::WEEKS(), $field->getRangeUnit());
        self::assertSame((int)$native->format('N'), $field->getFromNative($native));
        self::assertSameObject(ValueRange::ofFixed(1, 7), $field->range());
    }

    public function testDayOfMonth(): void
    {
        $field = ChronoField::DAY_OF_MONTH();

        $native = Factory::create(2018, 3, 4, 5, 6, 7);

        self::assertSameObject(ChronoUnit::DAYS(), $field->getBaseUnit());
        self::assertSameObject(ChronoUnit::MONTHS(), $field->getRangeUnit());
        self::assertSame((int)$native->format('j'), $field->getFromNative($native));
        self::assertSameObject(ValueRange::ofVariableMax(1, 28, 31), $field->range());
    }

    public function testMonthOfYear(): void
    {
        $field = ChronoField::MONTH_OF_YEAR();

        $native = Factory::create(2018, 3, 4, 5, 6, 7);

        self::assertSameObject(ChronoUnit::MONTHS(), $field->getBaseUnit());
        self::assertSameObject(ChronoUnit::YEARS(), $field->getRangeUnit());
        self::assertSame((int)$native->format('n'), $field->getFromNative($native));
        self::assertSameObject(ValueRange::ofFixed(1, 12), $field->range());
    }

    public function testIsSupportedBy(): void
    {

        $field = ChronoField::MONTH_OF_YEAR();

        $expected = true;
        $temporal = Mockery::mock(TemporalAccessor::class);
        $temporal->shouldReceive('supportsField')->with($field)->andReturn($expected);

        self::assertSame($expected, $field->isSupportedBy($temporal));
    }
}
