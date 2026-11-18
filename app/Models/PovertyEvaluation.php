<?php

namespace App\Models;

use App\Services\PovertyCalculatorService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PovertyEvaluation extends Model
{
    protected $fillable = [
        'household_id',
        'user_id',
        'import_batch_id',
        'poverty_level_id',
        'year',
        'quarter',
        'evaluation_source',
        'monthly_income_total',
        'monthly_income_female',
        'monthly_income_male',
        'monthly_expense_total',
        'monthly_expense_food',
        'monthly_expense_housing_services',
        'monthly_expense_health_education',
        'female_unpaid_care_hours_weekly',
        'male_unpaid_care_hours_weekly',
        'housing_floor_wall_deprivation',
        'housing_overcrowding_deprivation',
        'basic_services_deprivation',
        'education_lag_deprivation',
        'health_security_deprivation',
        'food_insecurity_deprivation',
        'per_capita_income',
        'income_to_expense_ratio',
        'economic_vulnerability_score',
        'multidimensional_deprivation_score',
        'poverty_index_general',
        'poverty_index_female',
        'poverty_index_male',
        'level_slug_general',
        'level_slug_female',
        'level_slug_male',
        'dimension_breakdown',
        'recommended_policies_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'monthly_income_total' => 'float',
            'monthly_income_female' => 'float',
            'monthly_income_male' => 'float',
            'monthly_expense_total' => 'float',
            'monthly_expense_food' => 'float',
            'monthly_expense_housing_services' => 'float',
            'monthly_expense_health_education' => 'float',
            'female_unpaid_care_hours_weekly' => 'float',
            'male_unpaid_care_hours_weekly' => 'float',
            'housing_floor_wall_deprivation' => 'integer',
            'housing_overcrowding_deprivation' => 'integer',
            'basic_services_deprivation' => 'integer',
            'education_lag_deprivation' => 'integer',
            'health_security_deprivation' => 'integer',
            'food_insecurity_deprivation' => 'integer',
            'per_capita_income' => 'float',
            'income_to_expense_ratio' => 'float',
            'economic_vulnerability_score' => 'float',
            'multidimensional_deprivation_score' => 'float',
            'poverty_index_general' => 'float',
            'poverty_index_female' => 'float',
            'poverty_index_male' => 'float',
            'dimension_breakdown' => 'array',
            'recommended_policies_snapshot' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (PovertyEvaluation $evaluation) {
            app(PovertyCalculatorService::class)->hydrateEvaluation($evaluation);
        });
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function povertyLevel(): BelongsTo
    {
        return $this->belongsTo(PovertyLevel::class);
    }
}
