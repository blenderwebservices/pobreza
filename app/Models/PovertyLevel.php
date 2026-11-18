<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PovertyLevel extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'bmi_analogy_label',
        'min_score',
        'max_score',
        'color',
        'badge_hex',
        'description',
        'implications',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'min_score' => 'float',
            'max_score' => 'float',
            'display_order' => 'integer',
        ];
    }

    public function policyRecommendations(): HasMany
    {
        return $this->hasMany(PolicyRecommendation::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(PovertyEvaluation::class);
    }
}
