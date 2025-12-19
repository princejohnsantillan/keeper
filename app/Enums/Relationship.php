<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Relationship: string implements HasLabel
{
    case Father = 'father';
    case Mother = 'mother';
    case Brother = 'brother';
    case Sister = 'sister';
    case Grandfather = 'grandfather';
    case Grandmother = 'grandmother';
    case Uncle = 'uncle';
    case Aunt = 'aunt';
    case Relative = 'relative';
    case Godfather = 'godfather';
    case Godmother = 'godmother';
    case FamilyFriend = 'family_friend';
    case Guardian = 'guardian';

    public function getLabel(): string
    {
        return match ($this) {
            self::Father => 'Father',
            self::Mother => 'Mother',
            self::Brother => 'Brother',
            self::Sister => 'Sister',
            self::Grandfather => 'Grandfather',
            self::Grandmother => 'Grandmother',
            self::Uncle => 'Uncle',
            self::Aunt => 'Aunt',
            self::Relative => 'Relative',
            self::Godfather => 'Godfather',
            self::Godmother => 'Godmother',
            self::FamilyFriend => 'Family Friend',
            self::Guardian => 'Guardian',
        };
    }
}
