<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPass
 */
class Pass extends Model
{
    /** @use HasFactory<\Database\Factories\PassFactory> */
    use HasFactory;
}
