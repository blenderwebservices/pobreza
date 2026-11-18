<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Household extends Model
{
    protected $fillable = [
        'user_id',
        'folio_code',
        'name',
        'subject_type',
        'state',
        'municipality',
        'zone_type',
        'total_members',
        'female_members',
        'male_members',
        'dependent_children',
        'female_headed',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_members' => 'integer',
            'female_members' => 'integer',
            'male_members' => 'integer',
            'dependent_children' => 'integer',
            'female_headed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(PovertyEvaluation::class)->orderBy('year');
    }
}
