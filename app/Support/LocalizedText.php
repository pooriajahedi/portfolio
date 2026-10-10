<?php

namespace App\Support;

/**
 * Builds the `{ fa, en }` pair the public API uses for translatable content.
 * A missing English value falls back to the Persian one, so the site is never blank.
 */
final class LocalizedText
{
    /**
     * @return array{fa: string, en: string}
     */
    public static function pair(?string $fa, ?string $en): array
    {
        $fa = trim((string) $fa);
        $en = trim((string) $en);

        return [
            'fa' => $fa,
            'en' => $en !== '' ? $en : $fa,
        ];
    }
}
