<?php

namespace PARTest\Time;

use Closure;
use DateTimeInterface;
use PAR\Time\Factory;
use PHPUnit\Framework\TestCase;

class TimeTestCase extends TestCase
{
    /**
     * @var string
     */
    private $saveTz;

    protected function setUp(): void
    {
        //save current timezone
        $this->saveTz = date_default_timezone_get();
        date_default_timezone_set('Europe/Amsterdam');

        parent::setUp();
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->saveTz);
        Factory::setTestNow();

        parent::tearDown();
    }

    /**
     * Lock Factory::parse() within the Closure to make sure every call uses the exact datetime regardless of current time
     *
     * @param Closure                $func The function to execute with locked now
     * @param DateTimeInterface|null $dt   The now to use
     */
    protected function wrapWithTestNow(Closure $func, DateTimeInterface $dt = null): void
    {
        Factory::setTestNow($dt ?? Factory::now());
        $func();
        Factory::setTestNow();
    }

    protected static function assertSameYear(DateTimeInterface $dt, $year): void
    {
        self::assertSame((int)$year, (int)$dt->format('Y'));
    }

    protected static function assertSameMonth(DateTimeInterface $dt, $month): void
    {
        self::assertSame((int)$month, (int)$dt->format('n'));
    }

    protected static function assertSameDay(DateTimeInterface $dt, $day): void
    {
        self::assertSame((int)$day, (int)$dt->format('d'));
    }

    protected static function assertSameHour(DateTimeInterface $dt, $hour): void
    {
        self::assertSame((int)$hour, (int)$dt->format('H'));
    }

    protected static function assertSameMinute(DateTimeInterface $dt, $minute): void
    {
        self::assertSame((int)$minute, (int)$dt->format('i'));
    }

    protected static function assertSameSecond(DateTimeInterface $dt, $second): void
    {
        self::assertSame((int)$second, (int)$dt->format('s'));
    }

    protected static function assertSameTime(DateTimeInterface $dt, $hour, $minute, $second): void
    {
        self::assertSameHour($dt, $hour);
        self::assertSameMinute($dt, $minute);
        self::assertSameSecond($dt, $second);
    }

    protected static function assertSameDate(DateTimeInterface $dt, $year, $month, $day): void
    {
        self::assertSameYear($dt, $year);
        self::assertSameMonth($dt, $month);
        self::assertSameDay($dt, $day);
    }

    protected static function assertDateTime(DateTimeInterface $dt, $year, $month, $day, $hour, $minute, $second): void
    {
        self::assertSameYear($dt, $year);
        self::assertSameMonth($dt, $month);
        self::assertSameDay($dt, $day);
        self::assertSameHour($dt, $hour);
        self::assertSameMinute($dt, $minute);
        self::assertSameSecond($dt, $second);
    }
}
