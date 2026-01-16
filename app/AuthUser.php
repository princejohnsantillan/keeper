<?php

declare(strict_types=1);

namespace App;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class AuthUser
{
    public static function user(): User
    {
        $user = Auth::user();

        if ($user === null) {
            abort(403);
        }

        return $user;
    }

    public static function guardian(): Guardian
    {
        $guardian = self::user()->guardian;

        if ($guardian === null) {
            abort(403);
        }

        return $guardian;
    }

    public static function guardianId(): int
    {
        return self::guardian()->id;
    }

    public static function userId(): int
    {
        return self::user()->id;
    }
}
