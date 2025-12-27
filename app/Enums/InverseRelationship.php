<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * An enumeration of Child to Guardian relationships.
 */
enum InverseRelationship: string implements HasLabel
{
    case Son = 'son';
    case Daughter = 'daughter';
    case Brother = 'brother';
    case Sister = 'sister';
    case Grandson = 'grandson';
    case Granddaughter = 'granddaughter';
    case Nephew = 'nephew';
    case Niece = 'niece';
    case Relative = 'relative';
    case Godson = 'godson';
    case Goddaughter = 'goddaughter';
    case FamilyFriend = 'family_friend';
    case Child = 'child';

    public function getLabel(): string
    {
        return match ($this) {
            self::Son => 'Son',
            self::Daughter => 'Daughter',
            self::Brother => 'Brother',
            self::Sister => 'Sister',
            self::Grandson => 'Grandson',
            self::Granddaughter => 'Granddaughter',
            self::Nephew => 'Nephew',
            self::Niece => 'Niece',
            self::Relative => 'Relative',
            self::Godson => 'Godson',
            self::Goddaughter => 'Goddaughter',
            self::FamilyFriend => 'Family Friend',
            self::Child => 'Child',
        };
    }

    /**
     * The inverse relationship, Guardian to Child.
     */
    public function inverse(Gender $gender): Relationship
    {
        return match ($this) {
            self::Son, self::Daughter => $gender->value ? Relationship::Father : Relationship::Mother,
            self::Brother, self::Sister => $gender->value ? Relationship::Brother : Relationship::Sister,
            self::Grandson, self::Granddaughter => $gender->value ? Relationship::Grandfather : Relationship::Grandmother,
            self::Nephew, self::Niece => $gender->value ? Relationship::Uncle : Relationship::Aunt,
            self::Relative => Relationship::Relative,
            self::Godson, self::Goddaughter => $gender->value ? Relationship::Godfather : Relationship::Godmother,
            self::FamilyFriend => Relationship::FamilyFriend,
            self::Child => Relationship::Guardian
        };
    }
}
