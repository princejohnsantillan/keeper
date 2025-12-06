<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperKeeper
 */
final class Keeper extends Model
{
    /** @use HasFactory<\Database\Factories\KeeperFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
        ];
    }

    /**
     * @return BelongsToMany<Child, $this, Relationship>
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'relationship')
            ->using(Relationship::class)
            ->withPivot('relationship')
            ->withTimestamps();
    }
}
