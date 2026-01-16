<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TermAcceptanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperTermAcceptance
 *
 * @property int $id
 * @property int $term_id
 * @property int $guardian_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gatepass> $gatepasses
 * @property-read int|null $gatepasses_count
 * @property-read \App\Models\Guardian $guardian
 * @property-read \App\Models\Term $term
 *
 * @method static \Database\Factories\TermAcceptanceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereGuardianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereUserAgent($value)
 *
 * @mixin \Eloquent
 */
final class TermAcceptance extends Model
{
    /** @use HasFactory<TermAcceptanceFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Term, $this>
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * @return BelongsTo<Guardian, $this>
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * @return HasMany<Gatepass, $this>
     */
    public function gatepasses(): HasMany
    {
        return $this->hasMany(Gatepass::class);
    }
}
