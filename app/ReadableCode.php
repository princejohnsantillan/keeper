<?php

declare(strict_types=1);

namespace App;

use OutOfBoundsException;

final class ReadableCode
{
    public const CHARACTERS = 'ACDEFHJKMNPRTWXY347';

    public static function generate(int $length = 5): string
    {
        $max = strlen(self::CHARACTERS);

        if ($length > $max) {
            throw new OutOfBoundsException("Length cannot be greater than {$max}.");
        }

        $arr = str_split(self::CHARACTERS);

        shuffle($arr);
        shuffle($arr);

        $str = implode('', $arr);

        return mb_substr($str, 0, $length);
    }
}
