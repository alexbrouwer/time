<?php declare(strict_types=1);

namespace PARTest\Time;

use DateInterval;
use DateTimeImmutable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PAR\Time\Chrono\ChronoUnit;
use PAR\Time\Duration;
use PAR\Time\Exception\DateTimeException;
use PAR\Time\Exception\UnsupportedTemporalTypeException;
use PAR\Time\Temporal\Temporal;
use PAR\Time\Temporal\TemporalAmount;

class DurationTest extends MockeryTestCase
{
    public function testOfDays(): void
    {
        $amount = 4;
        $duration = Duration::ofDays($amount);
        $this->assertSame(86400 * $amount, $duration->getSeconds());
        $this->assertSame(0, $duration->getMicroSeconds());
    }

    public function testOfHours(): void
    {
        $amount = 3;
        $duration = Duration::ofHours($amount);
        $this->assertSame(3600 * $amount, $duration->getSeconds());
        $this->assertSame(0, $duration->getMicroSeconds());
    }

    public function testOfMinutes(): void
    {
        $amount = 5;
        $duration = Duration::ofMinutes($amount);
        $this->assertSame(60 * $amount, $duration->getSeconds());
        $this->assertSame(0, $duration->getMicroSeconds());
    }

    public function testOfSeconds(): void
    {
        $amount = 10;
        $duration = Duration::ofSeconds($amount);
        $this->assertSame($amount, $duration->getSeconds());
        $this->assertSame(0, $duration->getMicroSeconds());
    }

    public function testOfMillis(): void
    {
        $amount = 25;
        $duration = Duration::ofMillis($amount);
        $this->assertSame(0, $duration->getSeconds());
        $this->assertSame($amount * 1000, $duration->getMicroSeconds());
    }

    public function testOfMicros(): void
    {
        $amount = 25;
        $duration = Duration::ofMicros($amount);
        $this->assertSame(0, $duration->getSeconds());
        $this->assertSame($amount, $duration->getMicroSeconds());
    }

    public function testMicroSecondOverflowing(): void
    {
        $expected = Duration::ofSeconds(3, 1);
        $this->assertTrue($expected->equals(Duration::ofSeconds(4, -999999)));
        $this->assertTrue($expected->equals(Duration::ofSeconds(2, 1000001)));
    }

    public function testZeroIsOfZeroLength(): void
    {
        $duration = Duration::zero();
        $this->assertSame(0, $duration->getSeconds());
        $this->assertSame(0, $duration->getMicroSeconds());
        $this->assertTrue($duration->isZero());
    }

    public function testOfWithEstimatedTemporalUnitThrowsUnsupportedTemporalTypeException(): void
    {
        $this->expectException(UnsupportedTemporalTypeException::class);

        Duration::of(1, ChronoUnit::MONTHS());
    }

    public function testOfWithDaysIsInterpretedAsMultiplesOf24Hours(): void
    {
        $duration = Duration::of(2, ChronoUnit::DAYS());

        $this->assertSame(48, $duration->toHours());
        $this->assertSame(2, $duration->toDays());
    }

    public function testOfWithTimeBasedChronoUnit(): void
    {
        $duration = Duration::of(2, ChronoUnit::MINUTES());

        $this->assertSame(2, $duration->toMinutes());
    }

    public function testOfWithZeroAmount(): void
    {
        $duration = Duration::of(0, ChronoUnit::SECONDS());

        $this->assertTrue($duration->isZero());
    }

    public function testCanBeMultipliedByPositiveValue(): void
    {
        $duration = Duration::ofSeconds(2);

        $this->assertTrue($duration->multipliedBy(3)->equals(Duration::ofSeconds(6)));
    }

    public function testCanBeMultipliedByNegativeValue(): void
    {
        $duration = Duration::ofSeconds(2);

        $this->assertTrue($duration->multipliedBy(-3)->equals(Duration::ofSeconds(-6)));
    }

    public function testCanDetermineNegativity(): void
    {
        $this->assertTrue(Duration::ofSeconds(-3)->isNegative());
        $this->assertFalse(Duration::ofSeconds(4, -999999)->isNegative());
    }

    public function testCanDetermineOrder(): void
    {
        $zero = Duration::zero();

        $this->assertLessThan(0, $zero->compareTo(Duration::ofSeconds(1)));
        $this->assertLessThan(0, $zero->compareTo(Duration::ofSeconds(0, 1)));
        $this->assertSame(0, $zero->compareTo(Duration::ofSeconds(0)));
        $this->assertGreaterThan(0, $zero->compareTo(Duration::ofSeconds(-1)));
        $this->assertGreaterThan(0, $zero->compareTo(Duration::ofSeconds(0, -1)));
    }

    public function testCanGetTheNumberOfDays(): void
    {
        $this->assertSame(2, Duration::ofDays(2)->toDays());
        $this->assertSame(-2, Duration::ofDays(-2)->toDays());
    }

    public function testCanGetTheNumberOfHours(): void
    {
        $this->assertSame(2, Duration::ofHours(2)->toHours());
        $this->assertSame(-2, Duration::ofHours(-2)->toHours());
    }

    public function testCanGetTheNumberOfMinutes(): void
    {
        $this->assertSame(2, Duration::ofMinutes(2)->toMinutes());
        $this->assertSame(-2, Duration::ofMinutes(-2)->toMinutes());
    }

    public function testCanGetTheNumberOfSeconds(): void
    {
        $this->assertSame(2, Duration::ofSeconds(2)->toSeconds());
        $this->assertSame(-2, Duration::ofSeconds(-2)->toSeconds());
    }

    public function testCanGetTheNumberOfMillis(): void
    {
        $this->assertSame(2, Duration::ofMillis(2)->toMillis());
        $this->assertSame(-2, Duration::ofMillis(-2)->toMillis());
    }

    public function testCanGetTheNumberOfMicros(): void
    {
        $this->assertSame(2, Duration::ofMicros(2)->toMicros());
        $this->assertSame(-2, Duration::ofMicros(-2)->toMicros());
    }

    public function testWithSeconds(): void
    {
        $duration = Duration::ofSeconds(1, 1);

        $changed = $duration->withSeconds(2);
        $this->assertNotSame($duration, $changed);
        $this->assertSame(2, $changed->getSeconds());
        $this->assertSame($duration->getMicroSeconds(), $changed->getMicroSeconds());
    }

    public function testWithMicroSeconds(): void
    {
        $duration = Duration::ofSeconds(1, 1);

        $changed = $duration->withMicroSeconds(2);
        $this->assertNotSame($duration, $changed);
        $this->assertSame(2, $changed->getMicroSeconds());
        $this->assertSame($duration->getSeconds(), $changed->getSeconds());
    }

    public function testGetForSupportedUnits(): void
    {
        $duration = Duration::ofSeconds(1, 2);

        $this->assertSame(1, $duration->get(ChronoUnit::SECONDS()));
        $this->assertSame(2, $duration->get(ChronoUnit::MICROS()));
    }

    public function testGetForUnsupportedUnitThrowsUnsupportedTemporalTypeException(): void
    {
        $duration = Duration::ofSeconds(1, 2);

        $this->expectException(UnsupportedTemporalTypeException::class);

        $duration->get(ChronoUnit::MILLIS());
    }

    public function testGetUnitsReturnsBothSupportedUnits(): void
    {
        $duration = Duration::ofSeconds(1);
        $units = $duration->getUnits();

        $this->assertCount(2, $units);
        $this->assertTrue($units[0]->equals(ChronoUnit::SECONDS()));
        $this->assertTrue($units[1]->equals(ChronoUnit::MICROS()));
    }

    public function testGetMicroSeconds(): void
    {
        $this->assertSame(1, Duration::ofMicros(1)->getMicroSeconds());
        $this->assertSame(999999, Duration::ofMicros(-1)->getMicroSeconds());
    }

    public function testCanTransformToPositive(): void
    {
        $this->assertFalse(Duration::ofSeconds(-1)->abs()->isNegative());
        $this->assertFalse(Duration::ofSeconds(1)->abs()->isNegative());
    }

    public function testCanBeDividedByNegativeValue(): void
    {
        $duration = Duration::ofSeconds(10, 3)->dividedBy(-3);

        $this->assertSame(-4, $duration->getSeconds());
        $this->assertSame(666665, $duration->getMicroSeconds());
    }

    public function testCanBeDividedByPositiveValue(): void
    {
        $duration = Duration::ofSeconds(10, 3)->dividedBy(3);

        $this->assertSame(3, $duration->getSeconds());
        $this->assertSame(333334, $duration->getMicroSeconds());
    }

    public function testCanBeDividedByZeroValue(): void
    {
        $duration = Duration::ofSeconds(1);

        $this->assertTrue($duration->dividedBy(0)->equals($duration));
        $this->assertTrue(Duration::zero()->dividedBy(1)->equals(Duration::zero()));
    }

    public function testCanDetermineEquality(): void
    {
        $duration = Duration::ofDays(1);

        $this->assertTrue($duration->equals(Duration::ofDays(1)));
        $this->assertFalse($duration->equals(Duration::ofHours(1)));
        $this->assertFalse($duration->equals(false));
    }

    public function testPositiveCanBeNegated(): void
    {
        $duration = Duration::ofSeconds(1);
        $negated = $duration->negated();

        $this->assertFalse($duration->isNegative());
        $this->assertTrue($negated->isNegative());
        $this->assertNotSame($duration, $negated);
    }

    public function testNegativeCanBeNegated(): void
    {
        $duration = Duration::ofSeconds(-1);
        $negated = $duration->negated();

        $this->assertTrue($duration->isNegative());
        $this->assertFalse($negated->isNegative());
        $this->assertNotSame($duration, $negated);
    }

    public function testCanSubtractFromTemporal(): void
    {
        $duration = Duration::ofSeconds(1);

        $temporal = $this->createMock(Temporal::class);
        $temporal->method('minus')->with($duration)->willReturn($temporal);

        $this->assertSame($temporal, $duration->subtractFrom($temporal));
    }

    public function testCanAddToTemporal(): void
    {
        $duration = Duration::ofSeconds(1);

        $temporal = $this->createMock(Temporal::class);
        $temporal->method('plus')->with($duration)->willReturnSelf();

        $this->assertSame($temporal, $duration->addTo($temporal));
    }

    public function testCanSubtractDuration(): void
    {
        $this->assertTrue(
            Duration::ofSeconds(2)
                ->minus(Duration::ofSeconds(1))
                ->equals(Duration::ofSeconds(1))
        );
    }

    public function testCanSubtractDays(): void
    {
        $this->assertTrue(
            Duration::ofDays(2)
                ->minusDays(1)
                ->equals(Duration::ofDays(1))
        );
    }

    public function testCanSubtractHours(): void
    {
        $this->assertTrue(
            Duration::ofHours(2)
                ->minusHours(1)
                ->equals(Duration::ofHours(1))
        );
    }

    public function testCanSubtractMinutes(): void
    {
        $this->assertTrue(
            Duration::ofMinutes(2)
                ->minusMinutes(1)
                ->equals(Duration::ofMinutes(1))
        );
    }

    public function testCanSubtractSeconds(): void
    {
        $this->assertTrue(
            Duration::ofSeconds(2)
                ->minusSeconds(1)
                ->equals(Duration::ofSeconds(1))
        );
    }

    public function testCanSubtractMillis(): void
    {
        $this->assertTrue(
            Duration::ofMillis(2)
                ->minusMillis(1)
                ->equals(Duration::ofMillis(1))
        );
    }

    public function testCanSubtractMicros(): void
    {
        $this->assertTrue(
            Duration::ofMicros(2)
                ->minusMicros(1)
                ->equals(Duration::ofMicros(1))
        );
    }

    public function testCanAddDuration(): void
    {
        $this->assertTrue(
            Duration::ofSeconds(2)
                ->plus(Duration::ofSeconds(1))
                ->equals(Duration::ofSeconds(3))
        );
    }

    public function testCanAddDays(): void
    {
        $this->assertTrue(
            Duration::ofDays(2)
                ->plusDays(1)
                ->equals(Duration::ofDays(3))
        );
    }

    public function testCanAddHours(): void
    {
        $this->assertTrue(
            Duration::ofHours(2)
                ->plusHours(1)
                ->equals(Duration::ofHours(3))
        );
    }

    public function testCanAddMinutes(): void
    {
        $this->assertTrue(
            Duration::ofMinutes(2)
                ->plusMinutes(1)
                ->equals(Duration::ofMinutes(3))
        );
    }

    public function testCanAddSeconds(): void
    {
        $this->assertTrue(
            Duration::ofSeconds(2)
                ->plusSeconds(1)
                ->equals(Duration::ofSeconds(3))
        );
    }

    public function testCanAddMillis(): void
    {
        $this->assertTrue(
            Duration::ofMillis(2)
                ->plusMillis(1)
                ->equals(Duration::ofMillis(3))
        );
    }

    public function testCanAddMicros(): void
    {
        $this->assertTrue(
            Duration::ofMicros(2)
                ->plusMicros(1)
                ->equals(Duration::ofMicros(3))
        );
    }

    /**
     * @dataProvider provideValidISO8601TextAndDuration
     *
     * @param string   $text
     * @param Duration $expected
     */
    public function testCanParseString(string $text, Duration $expected): void
    {
        $duration = Duration::parse($text);

        $this->assertTrue($duration->equals($expected));
    }

    public function testParseWillThrowDateTimeExceptionWhenInvalidText(): void
    {
        $this->expectException(DateTimeException::class);

        Duration::parse('P1YT1H');
    }

    /**
     * @dataProvider provideValidISO8601TextAndDuration
     *
     * @param string   $expected
     * @param Duration $duration
     */
    public function testCanTransformToISO8601(string $expected, Duration $duration): void
    {
        $this->assertSame($expected, $duration->toString());
    }

    public function provideValidISO8601TextAndDuration(): array
    {
        return [
            'full'            => ['-P1DT2H3M4.523S', Duration::ofDays(1)->plusHours(2)->plusMinutes(3)->plusSeconds(4)->plusMillis(523)->negated()],
            'positive'        => ['P1DT2H3M4S', Duration::ofDays(1)->plusHours(2)->plusMinutes(3)->plusSeconds(4)],
            'only-days'       => ['P3D', Duration::ofDays(3)],
            'one-day-not-24h' => ['P1D', Duration::ofHours(24)],
            'zero'            => ['PT0S', Duration::zero()],
            'millis'          => ['PT2.002S', Duration::ofMillis(2002)],
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
            'millis'   => [Duration::parse('PT2.002S'), DateInterval::createFromDateString('2 seconds + 2 milliseconds')],
            'inverted' => [Duration::parse('-PT2S'), $inverted],
        ];
    }

    public function provideValidDurationAndExpectedFormat(): array
    {
        return [
            'negative' => [Duration::parse('-P1D'), '+|0|0|0|0|0|-86400|0'],
            'positive' => [Duration::parse('P2D'), '+|0|0|0|0|0|172800|0'],
            'minutes'  => [Duration::parse('PT2M'), '+|0|0|0|0|0|120|0'],
            'millis'   => [Duration::parse('PT2.002S'), '+|0|0|0|0|0|2|2000'],
        ];
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
        $this->assertSame($expectedFormat, $interval->format($format));
    }

    /**
     * @dataProvider provideValidDurationAndDateInterval
     *
     * @param Duration     $expectedDuration
     * @param DateInterval $interval
     */
    public function testFromDateInterval(Duration $expectedDuration, DateInterval $interval): void
    {
        $duration = Duration::fromDateInterval($interval);

        $this->assertTrue($expectedDuration->equals($duration));
    }

    public function testFromDateIntervalThrowsDateTimeExceptionWhenIntervalHasYears(): void
    {
        $interval = new DateInterval('P1Y');

        $this->expectException(DateTimeException::class);

        Duration::fromDateInterval($interval);
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

        $this->assertTrue(Duration::parse('P730DT3M')->equals($duration));
    }
}
