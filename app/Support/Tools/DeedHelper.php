<?php

namespace App\Support\Tools;

use Carbon\Carbon;
use Throwable;

class DeedHelper
{
    /** @param array<string,mixed> $fields English label => value */
    public static function missing(array $fields): array
    {
        $issues = [];
        foreach ($fields as $label => $value) {
            if (trim((string) $value) === '') {
                $issues[] = __(':field is required', ['field' => __($label)]);
            }
        }

        return $issues;
    }

    public static function validCnic($cnic): bool
    {
        return strlen(preg_replace('/\D/', '', (string) $cnic)) === 13;
    }

    /** Valid date ho to Carbon, warna null: khali date par page crash na ho. */
    public static function date($value): ?Carbon
    {
        if (trim((string) $value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}
