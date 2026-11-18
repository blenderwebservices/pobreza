<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyRecommendation extends Model
{
    protected $fillable = [
        'poverty_level_id',
        'target_segment',
        'dimension',
        'title',
        'technical_action',
        'ideal_framework_rationale',
        'priority',
        'expected_impact',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function povertyLevel(): BelongsTo
    {
        return $this->belongsTo(PovertyLevel::class);
    }
}
