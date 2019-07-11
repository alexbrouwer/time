<?php

namespace PARTest\Time\Chrono;

use PAR\Core\PHPUnit\CoreAssertions;
use PAR\Enum\PHPUnit\EnumTestCase;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Chrono\ChronoUnit;

class ChronoFieldTest extends EnumTestCase
{
    use CoreAssertions;

    public function testValues(): void
    {
        self::assertSame(
            [
                ChronoField::DAY_OF_WEEK(),
            ],
            ChronoField::values()
        );
    }

    public function testDayOfWeek(): void
    {
        $dayOfWeek = ChronoField::DAY_OF_WEEK();

        self::assertSameObject(ChronoUnit::DAYS(), $dayOfWeek->getBaseUnit());
        self::assertSameObject(ChronoUnit::WEEKS(), $dayOfWeek->getRangeUnit());
    }
}
