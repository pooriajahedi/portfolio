<?php

namespace App\Support;

/**
 * Minimal Jalali (Persian) calendar helper. Resume dates are stored as Jalali
 * parts; the English site shows the matching Gregorian year.
 */
final class JalaliCalendar
{
    private const GREGORIAN_MONTH_DAYS = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    /**
     * @return array{0: int, 1: int, 2: int} Gregorian [year, month, day]
     */
    public static function toGregorian(int $year, int $month, int $day): array
    {
        $year += 1595;

        $days = -355668
            + (365 * $year)
            + intdiv($year, 33) * 8
            + intdiv(($year % 33) + 3, 4)
            + $day
            + ($month < 7 ? ($month - 1) * 31 : (($month - 7) * 30) + 186);

        $gregorianYear = 400 * intdiv($days, 146097);
        $days %= 146097;

        if ($days > 36524) {
            $gregorianYear += 100 * intdiv(--$days, 36524);
            $days %= 36524;

            if ($days >= 365) {
                $days++;
            }
        }

        $gregorianYear += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $gregorianYear += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        $gregorianDay = $days + 1;
        $monthDays = self::GREGORIAN_MONTH_DAYS;
        $monthDays[2] = self::isGregorianLeap($gregorianYear) ? 29 : 28;

        for ($gregorianMonth = 1; $gregorianMonth <= 12 && $gregorianDay > $monthDays[$gregorianMonth]; $gregorianMonth++) {
            $gregorianDay -= $monthDays[$gregorianMonth];
        }

        return [$gregorianYear, $gregorianMonth, $gregorianDay];
    }

    public static function toGregorianYear(int $year, int $month = 1, int $day = 1): int
    {
        return self::toGregorian($year, $month, $day)[0];
    }

    private static function isGregorianLeap(int $year): bool
    {
        return ($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0;
    }
}
