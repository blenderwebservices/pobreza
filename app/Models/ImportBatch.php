<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportBatch extends Model
{
    protected $fillable = [
        'user_id',
        'batch_code',
        'source_name',
        'filename',
        'total_rows',
        'processed_rows',
        'status',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'total_rows' => 'integer',
            'processed_rows' => 'integer',
            'summary' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(PovertyEvaluation::class);
    }
}
