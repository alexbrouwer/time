<?php declare(strict_types=1);

namespace PARTest\Time;

use DateInterval;
use DateTimeImmutable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PAR\Core\PHPUnit\CoreAssertions;
use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Duration;
use PAR\Time\Exception\DateTimeException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\TemporalAmount;

class DurationTest extends MockeryTestCase
{
    use CoreAssertions;

    public function provideForParse(): array
    {
        return [
            'positive'         => ['P1DT2H3M4S', Duration::ofDays(1)->plusHours(2)->plusMinutes(3)->plusSeconds(4)],
            'only-days'        => ['P3D', Duration::ofDays(3)],
            'one-day-not-24h'  => ['P1D', Duration::ofHours(24)],
            'zero'             => ['PT0S', Duration::zero()],
            'seconds'          => ['PT2S', Duration::ofSeconds(2)],
            'millis'           => ['PT2.002S', Duration::ofMillis(2002)],
            'negative-all'     => ['-P1DT2H3M4.523S', Duration::ofDays(1)->plusHours(2)->plusMinutes(3)->plusSeconds(4)->plusMillis(523)->negated()],
            'negative-days'    => ['P-1DT2H3M4.523S', Duration::ofDays(-1)->plusHours(2)->plusMinutes(3)->plusSeconds(4)->plusMillis(523)],
            'negative-hours'   => ['P1DT-2H3M4.523S', Duration::ofDays(1)->plusHours(-2)->plusMinutes(3)->plusSeconds(4)->plusMillis(523)],
            'negative-minutes' => ['P1DT2H-3M4.523S', Duration::ofDays(1)->plusHours(2)->plusMinutes(-3)->plusSeconds(4)->plusMillis(523)],
            'negative-seconds' => ['P1DT2H3M-4.523S', Duration::ofDays(1)->plusHours(2)->plusMinutes(3)->plusMillis(-4523)],
        ];
    }

    public function provideForToString(): array
    {
        return [
            'positive'        => ['P1DT2H3M4S', Duration::ofDays(1)->plusHours(2)->plusMinutes(3)->plusSeconds(4)],
            'only-days'       => ['P3D', Duration::ofDays(3)],
            'one-day-not-24h' => ['P1D', Duration::ofHours(24)],
            'zero'            => ['PT0S', Duration::zero()],
            'seconds'         => ['PT2S', Duration::ofSeconds(2)],
            'millis'          => ['PT2.002S', Duration::ofMillis(2002)],
            'negative-all'    => ['-P1DT2H3M4.523S', Duration::ofDays(1)->plusHours(2)->plusMinutes(3)->plusSeconds(4)->plusMillis(523)->negated()],
        ];
    }

    public function provideValidDurationAndDateInterval(): array
    {
        $start = DateTimeImmutable::createFromFormat('H:i:s', '00:00:02');
        $inverted = $start->diff($start->modify('-2 seconds'));

        return [
            'negative' => [Duration::parse('-P1D'), DateInterval::createFromDateString('86400 seconds ago')],
            'positive' => [Duration::parse('P2D'), DateInterval::createFromDateString('172800 seconds')],
            'minutes'  => [Duration::parse('PT2M'), DateInterval::createFromDateString('120 seconds')],
            'millis'   => [Duration::parse('PT2.005S'), DateInterval::createFromDateString('2 seconds + 5 milliseconds')],
            'inverted' => [Duration::parse('-PT2S'), $inverted],
        ];
    }

    public function provideValidDurationAndExpectedFormat(): array
    {
        return [
            'negative' => [Duration::parse('-P1D'), '+|0|0|0|0|0|-86400|0'],
            'positive' => [Duration::parse('P2D'), '+|0|0|0|0|0|172800|0'],
            'minutes'  => [Duration::parse('PT2M'), '+|0|0|0|0|0|120|0'],
            'millis'   => [Duration::parse('PT2.005S'), '+|0|0|0|0|0|2|5000'],
        ];
    }

    public function testCanAddDays(): void
    {
        self::assertSameObject(Duration::ofDays(3), Duration::ofDays(2)->plusDays(1));
    }

    public function testCanAddDuration(): void
    {
        self::assertSameObject(
            Duration::ofSeconds(3),
            Duration::ofSeconds(2)->plusDuration(Duration::ofSeconds(1))
        );
    }

    public function testCanAddHours(): void
    {
        self::assertSameObject(Duration::ofHours(3), Duration::ofHours(2)->plusHours(1));
    }

    public function testCanAddMicros(): void
    {
        self::assertSameObject(Duration::ofMicros(3), Duration::ofMicros(2)->plusMicros(1));
    }

    public function testCanAddMillis(): void
    {
        self::assertSameObject(Duration::ofMillis(3), Duration::ofMillis(2)->plusMillis(1));
    }

    public function testCanAddMinutes(): void
    {
        self::assertSameObject(Duration::ofMinutes(3), Duration::ofMinutes(2)->plusMinutes(1));
    }

    public function testCanAddSeconds(): void
    {
        self::assertSameObject(Duration::ofSeconds(3), Duration::ofSeconds(2)->plusSeconds(1));
    }

    public function testCanAddToTemporal(): void
    {
        $this->markTestSkipped('Awaiting LocalTime::of + LocalTime::plus');
//        $duration = Duration::ofSeconds(1, 2);
//
//        $time = LocalTime::of(1, 2, 3);
//
//        $result = $duration->addTo($time);
//        self::assertNotSame($time, $result);
//        self::assertSameObject(LocalTime::of(1, 2, 4), $result);
    }

    public function testCanBeDividedByNegativeValue(): void
    {
        $duration = Duration::ofSeconds(10, 3)->dividedBy(-3);

        self::assertSame(-4, $duration->getSeconds());
        self::assertSame(666665, $duration->getMicroSeconds());
    }

    public function testCanBeDividedByPositiveValue(): void
    {
        $duration = Duration::ofSeconds(10, 3)->dividedBy(3);

        self::assertSame(3, $duration->getSeconds());
        self::assertSame(333334, $duration->getMicroSeconds());
    }

    public function testCanBeDividedByZeroValue(): void
    {
        $duration = Duration::ofSeconds(1);

        self::assertSameObject($duration, $duration->dividedBy(0));
        self::assertSameObject(Duration::zero(), Duration::zero()->dividedBy(1));
    }

    public function testCanBeMultipliedByNegativeValue(): void
    {
        $duration = Duration::ofSeconds(2);

        self::assertSameObject(Duration::ofSeconds(-6), $duration->multipliedBy(-3));
    }

    public function testCanBeMultipliedByPositiveValue(): void
    {
        $duration = Duration::ofSeconds(2);

        self::assertSameObject(Duration::ofSeconds(6), $duration->multipliedBy(3));
    }

    /**
     * @dataProvider provideValidDurationAndExpectedFormat
     *
     * @param Duration $duration
     * @param string   $expectedFormat
     */
    public function testCanBeTransformedIntoNativeDateInterval(Duration $duration, string $expectedFormat): void
    {
        $interval = $duration->toDateInterval();

        $format = '%R|%y|%m|%d|%h|%i|%s|%f';
        self::assertSame($expectedFormat, $interval->format($format));
    }

    public function testCanDetermineEquality(): void
    {
        $duration = Duration::ofDays(1);

        self::assertTrue($duration->equals(Duration::ofDays(1)));
        self::assertFalse($duration->equals(Duration::ofHours(1)));
        self::assertFalse($duration->equals(false));
    }

    public function testCanDetermineNegativity(): void
    {
        self::assertTrue(Duration::ofSeconds(-3)->isNegative());
        self::assertFalse(Duration::ofSeconds(4, -999999)->isNegative());
    }

    public function testCanDetermineOrder(): void
    {
        $zero = Duration::zero();

        self::assertLessThan(0, $zero->compareTo(Duration::ofSeconds(1)));
        self::assertLessThan(0, $zero->compareTo(Duration::ofSeconds(0, 1)));
        self::assertSame(0, $zero->compareTo(Duration::ofSeconds(0)));
        self::assertGreaterThan(0, $zero->compareTo(Duration::ofSeconds(-1)));
        self::assertGreaterThan(0, $zero->compareTo(Duration::ofSeconds(0, -1)));
    }

    public function testCanExtractParts(): void
    {
        self::assertSame(2, Duration::ofHours(26)->toHoursPart());
        self::assertSame(2, Duration::ofMinutes(62)->toMinutesPart());
        self::assertSame(2, Duration::ofSeconds(62)->toSecondsPart());
        self::assertSame(2, Duration::ofMillis(1002)->toMillisPart());
        self::assertSame(2, Duration::ofMicros(1002)->toMicrosPart());
    }

    public function testCanGetTheNumberOfDays(): void
    {
        self::assertSame(2, Duration::ofDays(2)->toDays());
        self::assertSame(-2, Duration::ofDays(-2)->toDays());
    }

    public function testCanGetTheNumberOfHours(): void
    {
        self::assertSame(2, Duration::ofHours(2)->toHours());
        self::assertSame(-2, Duration::ofHours(-2)->toHours());
    }

    public function testCanGetTheNumberOfMicros(): void
    {
        self::assertSame(2, Duration::ofMicros(2)->toMicros());
        self::assertSame(-2, Duration::ofMicros(-2)->toMicros());
    }

    public function testCanGetTheNumberOfMillis(): void
    {
        self::assertSame(2, Duration::ofMillis(2)->toMillis());
        self::assertSame(-2, Duration::ofMillis(-2)->toMillis());
    }

    public function testCanGetTheNumberOfMinutes(): void
    {
        self::assertSame(2, Duration::ofMinutes(2)->toMinutes());
        self::assertSame(-2, Duration::ofMinutes(-2)->toMinutes());
    }

    public function testCanGetTheNumberOfSeconds(): void
    {
        self::assertSame(2, Duration::ofSeconds(2)->toSeconds());
        self::assertSame(-2, Duration::ofSeconds(-2)->toSeconds());
    }

    public function testCanNegateNegative(): void
    {
        $duration = Duration::ofSeconds(-1);
        $negated = $duration->negated();

        self::assertTrue($duration->isNegative());
        self::assertFalse($negated->isNegative());
        self::assertNotSame($duration, $negated);
    }

    public function testCanNegatePositive(): void
    {
        $duration = Duration::ofSeconds(1);
        $negated = $duration->negated();

        self::assertFalse($duration->isNegative());
        self::assertTrue($negated->isNegative());
        self::assertNotSame($duration, $negated);
    }

    /**
     * @dataProvider provideForParse
     *
     * @param string   $text
     * @param Duration $expected
     */
    public function testCanParseString(string $text, Duration $expected): void
    {
        $duration = Duration::parse($text);

        self::assertSameObject($expected, $duration);
    }

    public function testCanSubtractDays(): void
    {
        self::assertSameObject(Duration::ofDays(1), Duration::ofDays(2)->minusDays(1));
    }

    public function testCanSubtractDuration(): void
    {
        self::assertSameObject(
            Duration::ofSeconds(1),
            Duration::ofSeconds(2)->minusDuration(Duration::ofSeconds(1))
        );
    }

    public function testCanSubtractFromTemporal(): void
    {
        $this->markTestSkipped('Awaiting LocalTime::of + LocalTime::minus');
//        $duration = Duration::ofSeconds(1, 2);
//
//        $time = LocalTime::of(1, 2, 3);
//
//        $result = $duration->subtractFrom($time);
//        self::assertNotSame($time, $result);
//        self::assertSameObject(LocalTime::of(1, 2, 2), $result);
    }

    public function testCanSubtractHours(): void
    {
        self::assertSameObject(Duration::ofHours(1), Duration::ofHours(2)->minusHours(1));
    }

    public function testCanSubtractMicros(): void
    {
        self::assertSameObject(Duration::ofMicros(1), Duration::ofMicros(2)->minusMicros(1));
    }

    public function testCanSubtractMillis(): void
    {
        self::assertSameObject(Duration::ofMillis(1), Duration::ofMillis(2)->minusMillis(1));
    }

    public function testCanSubtractMinutes(): void
    {
        self::assertSameObject(Duration::ofMinutes(1), Duration::ofMinutes(2)->minusMinutes(1));
    }

    public function testCanSubtractSeconds(): void
    {
        self::assertSameObject(Duration::ofSeconds(1), Duration::ofSeconds(2)->minusSeconds(1));
    }

    /**
     * @dataProvider provideForToString
     *
     * @param string   $expected
     * @param Duration $duration
     */
    public function testCanTransformToISO8601(string $expected, Duration $duration): void
    {
        self::assertSame($expected, $duration->toString());
    }

    public function testCanTransformToPositive(): void
    {
        self::assertFalse(Duration::ofSeconds(-1)->abs()->isNegative());
        self::assertFalse(Duration::ofSeconds(1)->abs()->isNegative());
    }

    public function testChangeMicroSeconds(): void
    {
        $duration = Duration::ofSeconds(1, 1);

        $changed = $duration->withMicroSeconds(2);
        self::assertNotSame($duration, $changed);
        self::assertSame(2, $changed->getMicroSeconds());
        self::assertSame($duration->getSeconds(), $changed->getSeconds());
    }

    public function testChangeSeconds(): void
    {
        $duration = Duration::ofSeconds(1, 1);

        $changed = $duration->withSeconds(2);
        self::assertNotSame($duration, $changed);
        self::assertSame(2, $changed->getSeconds());
        self::assertSame($duration->getMicroSeconds(), $changed->getMicroSeconds());
    }

    public function testCreateWithOnlyDays(): void
    {
        $amount = 4;
        $duration = Duration::ofDays($amount);
        self::assertSame(86400 * $amount, $duration->getSeconds());
        self::assertSame(0, $duration->getMicroSeconds());
    }

    public function testCreateWithOnlyHours(): void
    {
        $amount = 3;
        $duration = Duration::ofHours($amount);
        self::assertSame(3600 * $amount, $duration->getSeconds());
        self::assertSame(0, $duration->getMicroSeconds());
    }

    public function testCreateWithOnlyMicros(): void
    {
        $amount = 25;
        $duration = Duration::ofMicros($amount);
        self::assertSame(0, $duration->getSeconds());
        self::assertSame($amount, $duration->getMicroSeconds());
    }

    public function testCreateWithOnlyMillis(): void
    {
        $amount = 25;
        $duration = Duration::ofMillis($amount);
        self::assertSame(0, $duration->getSeconds());
        self::assertSame($amount * 1000, $duration->getMicroSeconds());
    }

    public function testCreateWithOnlyMinutes(): void
    {
        $amount = 5;
        $duration = Duration::ofMinutes($amount);
        self::assertSame(60 * $amount, $duration->getSeconds());
        self::assertSame(0, $duration->getMicroSeconds());
    }

    public function testCreateWithOnlySeconds(): void
    {
        $amount = 10;
        $duration = Duration::ofSeconds($amount);
        self::assertSame($amount, $duration->getSeconds());
        self::assertSame(0, $duration->getMicroSeconds());
    }

    public function testCreateWithTimeBasedUnit(): void
    {
        $duration = Duration::of(2, ChronoUnit::MINUTES());

        self::assertSame(2, $duration->toMinutes());
    }

    public function testCreateWithUnsupportedUnitThrowsException(): void
    {
        $this->expectException(UnsupportedTemporalTypeException::class);

        Duration::of(1, ChronoUnit::MONTHS());
    }

    /**
     * @dataProvider provideValidDurationAndDateInterval
     *
     * @param Duration     $expectedDuration
     * @param DateInterval $interval
     */
    public function testFromDateInterval(Duration $expectedDuration, DateInterval $interval): void
    {
        $duration = Duration::ofDateInterval($interval);

        self::assertSameObject($expectedDuration, $duration);
    }

    public function testFromDateIntervalThrowsDateTimeExceptionWhenIntervalHasYears(): void
    {
        $interval = new DateInterval('P1Y');

        $this->expectException(DateTimeException::class);

        Duration::ofDateInterval($interval);
    }

    public function testFromTemporalAmount(): void
    {
        $years = ChronoUnit::YEARS();
        $minutes = ChronoUnit::MINUTES();

        $amount = Mockery::mock(TemporalAmount::class);
        $amount->shouldReceive('getUnits')->withNoArgs()->andReturn([$years, $minutes]);
        $amount->shouldReceive('get')->with($years)->andReturn(2);
        $amount->shouldReceive('get')->with($minutes)->andReturn(3);

        $duration = Duration::from($amount);

        self::assertSameObject(Duration::parse('P730DT3M'), $duration);
    }

    public function testGetMicroSeconds(): void
    {
        self::assertSame(1, Duration::ofMicros(1)->getMicroSeconds());
        self::assertSame(999999, Duration::ofMicros(-1)->getMicroSeconds());
    }

    public function testGetSeconds(): void
    {
        self::assertSame(1, Duration::ofSeconds(1)->getSeconds());
        self::assertSame(-1, Duration::ofSeconds(-1)->getSeconds());
    }

    public function testLengthOfDayInHours(): void
    {
        $duration = Duration::of(2, ChronoUnit::DAYS());

        self::assertSame(48, $duration->toHours());
        self::assertSame(2, $duration->toDays());
    }

    public function testMicroSecondOverflowing(): void
    {
        $expected = Duration::ofSeconds(3, 1);
        self::assertTrue($expected->equals(Duration::ofSeconds(4, -999999)));
        self::assertTrue($expected->equals(Duration::ofSeconds(2, 1000001)));
    }

    public function testParseWillThrowDateTimeExceptionWhenInvalidText(): void
    {
        $this->expectException(DateTimeException::class);

        Duration::parse('P1YT1H');
    }

    public function testRetrieveListOfUnits(): void
    {
        $duration = Duration::ofSeconds(1);
        $units = $duration->getUnits();

        self::assertCount(2, $units);
        self::assertSameObject(ChronoUnit::SECONDS(), $units[0]);
        self::assertSameObject(ChronoUnit::MICROS(), $units[1]);
    }

    public function testRetrieveValueForUnit(): void
    {
        $duration = Duration::ofSeconds(1, 2);

        self::assertSame(1, $duration->get(ChronoUnit::SECONDS()));
        self::assertSame(2, $duration->get(ChronoUnit::MICROS()));
    }

    public function testRetrieveValueForUnsupportedUnitThrowsException(): void
    {
        $this->expectException(UnsupportedTemporalTypeException::class);

        $duration = Duration::ofSeconds(1, 2);
        $duration->get(ChronoUnit::MILLIS());
    }

    public function testZero(): void
    {
        $duration = Duration::zero();
        self::assertSame(0, $duration->getSeconds());
        self::assertSame(0, $duration->getMicroSeconds());
        self::assertTrue($duration->isZero());
        self::assertTrue(Duration::ofDays(0)->isZero());
        self::assertTrue(Duration::ofHours(0)->isZero());
        self::assertTrue(Duration::ofMinutes(0)->isZero());
        self::assertTrue(Duration::ofSeconds(0)->isZero());
        self::assertTrue(Duration::ofMillis(0)->isZero());
        self::assertTrue(Duration::ofMicros(0)->isZero());
    }
}
