<?php

namespace App\Enums;

enum Relationship: string
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
}
