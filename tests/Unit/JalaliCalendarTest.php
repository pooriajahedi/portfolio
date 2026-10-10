<?php

namespace Tests\Unit;

use App\Support\JalaliCalendar;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class JalaliCalendarTest extends TestCase
{
    /**
     * @return array<string, array{0: int, 1: int, 2: int, 3: array{0: int, 1: int, 2: int}}>
     */
    public static function conversions(): array
    {
        return [
            'start of 1394' => [1394, 1, 1, [2015, 3, 21]],
            'start of 1398 tir' => [1398, 4, 1, [2019, 6, 22]],
            'start of 1401' => [1401, 1, 1, [2022, 3, 21]],
            'start of 1403 (leap-year boundary)' => [1403, 1, 1, [2024, 3, 20]],
            'mid mehr 1405' => [1405, 7, 18, [2026, 10, 10]],
        ];
    }

    /**
     * @param  array{0: int, 1: int, 2: int}  $expected
     */
    #[DataProvider('conversions')]
    public function test_it_converts_jalali_dates_to_gregorian(int $year, int $month, int $day, array $expected): void
    {
        $this->assertSame($expected, JalaliCalendar::toGregorian($year, $month, $day));
    }

    public function test_it_returns_only_the_gregorian_year(): void
    {
        $this->assertSame(2015, JalaliCalendar::toGregorianYear(1394));
    }
}
