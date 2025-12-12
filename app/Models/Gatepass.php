<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPass
 */
class Gatepass extends Model
{
    /** @use HasFactory<\Database\Factories\GatepassFactory> */
    use HasFactory;
}
