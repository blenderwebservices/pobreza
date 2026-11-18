<?php

namespace App\Services;

use App\Models\Household;
use App\Models\PovertyEvaluation;
use App\Models\PovertyLevel;

class PovertyCalculatorService
{
    /**
     * Líneas de bienestar referenciales mensuales per cápita (MXN).
     */
    public const URBAN_WELLBEING_LINE = 4560.00;
    public const RURAL_WELLBEING_LINE = 3290.00;
    public const URBAN_EXTREME_LINE = 2310.00;
    public const RURAL_EXTREME_LINE = 1760.00;

    /**
     * Definición base de los 5 niveles progresivos (no binarios) tipo IMC.
     */
    public static function defaultLevelsCatalog(): array
    {
        return [
            [
                'slug' => 'muy_baja',
                'name' => 'Pobreza Muy Baja (Bienestar Consolidado)',
                'bmi_analogy_label' => 'IP 0.0 – 19.9 · Óptimo Socioeconómico',
                'min_score' => 0.00,
                'max_score' => 19.99,
                'color' => 'success',
                'badge_hex' => '#15803d',
                'display_order' => 1,
                'description' => 'El hogar o individuo presenta suficiencia holgada de ingresos respecto a su estructura de gasto y ausencia de carencias físicas en vivienda, servicios, salud y educación.',
                'implications' => 'Implica capacidad de ahorro, resiliencia ante choques inflacionarios o médicos, movilidad social ascendente y cobertura plena de derechos sociales básicos.',
            ],
            [
                'slug' => 'baja',
                'name' => 'Pobreza Baja (Vulnerabilidad Leve)',
                'bmi_analogy_label' => 'IP 20.0 – 39.9 · Riesgo Preventivo',
                'min_score' => 20.00,
                'max_score' => 39.99,
                'color' => 'info',
                'badge_hex' => '#0369a1',
                'display_order' => 2,
                'description' => 'Los ingresos cubren la canasta básica y los gastos ordinarios con margen estrecho, o bien se registra una carencia aislada de baja intensidad (ej. acceso parcial a seguridad social).',
                'implications' => 'El hogar no es pobre extremo, pero un evento imprevisto (desempleo temporal, enfermedad o inflación alimentaria) puede desplazarlo rápidamente hacia pobreza moderada.',
            ],
            [
                'slug' => 'moderada',
                'name' => 'Pobreza Moderada',
                'bmi_analogy_label' => 'IP 40.0 – 59.9 · Déficit Estructural Moderado',
                'min_score' => 40.00,
                'max_score' => 59.99,
                'color' => 'warning',
                'badge_hex' => '#b45309',
                'display_order' => 3,
                'description' => 'Existe tensión presupuestaria constante (alta proporción del gasto absorbida por alimentos y servicios básicos) combinada con dos o más rezagos en vivienda, salud o educación.',
                'implications' => 'Limita la inversión familiar en capital humano, obliga al endeudamiento recurrente o trabajo informal precario, y profundiza las brechas de género en el hogar.',
            ],
            [
                'slug' => 'alta',
                'name' => 'Pobreza Alta (Severa)',
                'bmi_analogy_label' => 'IP 60.0 – 79.9 · Privación Multidimensional Severa',
                'min_score' => 60.00,
                'max_score' => 79.99,
                'color' => 'danger',
                'badge_hex' => '#c2410c',
                'display_order' => 4,
                'description' => 'El ingreso per cápita es inferior a la línea de bienestar digno, el gasto supera o agota el ingreso disponible y convergen carencias físicas severas en vivienda, hacinamiento o alimentación.',
                'implications' => 'Restringe severamente el desarrollo físico y educativo de los integrantes, incrementa la deserción escolar y sobrecarga de trabajo no remunerado a las mujeres del hogar.',
            ],
            [
                'slug' => 'muy_alta',
                'name' => 'Pobreza Muy Alta (Extrema Crítica)',
                'bmi_analogy_label' => 'IP 80.0 – 100.0 · Urgencia Prioritaria',
                'min_score' => 80.00,
                'max_score' => 100.00,
                'color' => 'danger',
                'badge_hex' => '#991b1b',
                'display_order' => 5,
                'description' => 'Condición crítica donde ni destinando la totalidad del ingreso se logra cubrir la canasta alimentaria básica, acompañada de precariedad habitacional grave y exclusión de servicios de salud y protección social.',
                'implications' => 'Riesgo inmediato de desnutrición, reproducción intergeneracional de la pobreza extrema y urgencia de intervención estatal directa en transferencias, alimentación e infraestructura.',
            ],
        ];
    }

    /**
     * Calcula el Índice de Pobreza (0-100) tipo IMC con segmentación general, femenina y masculina.
     */
    public function calculate(array $input): array
    {
        $zoneType = ($input['zone_type'] ?? 'urbana') === 'rural' ? 'rural' : 'urbana';
        $wellbeingLine = $zoneType === 'rural' ? self::RURAL_WELLBEING_LINE : self::URBAN_WELLBEING_LINE;

        $totalMembers = max(1, (int) ($input['total_members'] ?? 4));
        $femaleMembers = max(0, (int) ($input['female_members'] ?? intdiv($totalMembers, 2)));
        $maleMembers = max(0, (int) ($input['male_members'] ?? ($totalMembers - $femaleMembers)));
        $dependentChildren = max(0, (int) ($input['dependent_children'] ?? 1));
        $femaleHeaded = (bool) ($input['female_headed'] ?? false);

        $incomeFemale = max(0.0, (float) ($input['monthly_income_female'] ?? 0));
        $incomeMale = max(0.0, (float) ($input['monthly_income_male'] ?? 0));
        $incomeTotal = max(0.0, (float) ($input['monthly_income_total'] ?? ($incomeFemale + $incomeMale)));

        if ($incomeTotal <= 0 && ($incomeFemale + $incomeMale) > 0) {
            $incomeTotal = $incomeFemale + $incomeMale;
        }
        if (($incomeFemale + $incomeMale) <= 0 && $incomeTotal > 0) {
            $incomeFemale = round($incomeTotal * 0.38, 2);
            $incomeMale = round($incomeTotal * 0.62, 2);
        }

        $expenseFood = max(0.0, (float) ($input['monthly_expense_food'] ?? 0));
        $expenseHousing = max(0.0, (float) ($input['monthly_expense_housing_services'] ?? 0));
        $expenseHealthEdu = max(0.0, (float) ($input['monthly_expense_health_education'] ?? 0));
        $expenseTotal = max(
            $expenseFood + $expenseHousing + $expenseHealthEdu,
            (float) ($input['monthly_expense_total'] ?? ($expenseFood + $expenseHousing + $expenseHealthEdu))
        );

        $femaleCareHours = max(0.0, (float) ($input['female_unpaid_care_hours_weekly'] ?? 28));
        $maleCareHours = max(0.0, (float) ($input['male_unpaid_care_hours_weekly'] ?? 10));

        // 1. Indicadores de Proporción Ingreso / Gasto (55% del Índice)
        $perCapitaIncome = $incomeTotal / $totalMembers;
        $incomeToExpenseRatio = $expenseTotal > 0 ? ($incomeTotal / $expenseTotal) : ($incomeTotal > 0 ? 2.0 : 0.0);

        // Suficiencia frente a línea de bienestar (0 = 2x línea bienestar, 100 = 0 ingreso)
        $incomeGapScore = $this->clamp((1.0 - ($perCapitaIncome / ($wellbeingLine * 1.45))) * 100.0, 0.0, 100.0);

        // Tensión Ingreso/Gasto (Si el gasto iguala o supera el ingreso, sube la vulnerabilidad)
        // Ratio >= 1.5 -> 0 pts, Ratio = 1.0 -> 50 pts, Ratio <= 0.6 -> 100 pts
        $expensePressureScore = $this->clamp(((1.5 - $incomeToExpenseRatio) / 0.9) * 100.0, 0.0, 100.0);

        // Coeficiente de Engel (proporción del gasto o ingreso destinado a alimentación)
        $foodShare = $expenseTotal > 0 ? ($expenseFood / $expenseTotal) : 0.5;
        $engelScore = $this->clamp((($foodShare - 0.22) / 0.50) * 100.0, 0.0, 100.0);

        $economicVulnerabilityScore = round(
            ($incomeGapScore * 0.48) + ($expensePressureScore * 0.34) + ($engelScore * 0.18),
            2
        );

        // 2. Indicadores Multidimensionales de Vivienda y Carencias Sociales (45% del Índice)
        $floorWall = $this->clamp((float) ($input['housing_floor_wall_deprivation'] ?? 0), 0, 100);
        $overcrowding = $this->clamp((float) ($input['housing_overcrowding_deprivation'] ?? 0), 0, 100);
        $basicServices = $this->clamp((float) ($input['basic_services_deprivation'] ?? 0), 0, 100);
        $educationLag = $this->clamp((float) ($input['education_lag_deprivation'] ?? 0), 0, 100);
        $healthSecurity = $this->clamp((float) ($input['health_security_deprivation'] ?? 0), 0, 100);
        $foodInsecurity = $this->clamp((float) ($input['food_insecurity_deprivation'] ?? 0), 0, 100);

        $multidimensionalScore = round(
            ($floorWall * 0.16) +
            ($overcrowding * 0.14) +
            ($basicServices * 0.18) +
            ($educationLag * 0.15) +
            ($healthSecurity * 0.17) +
            ($foodInsecurity * 0.20),
            2
        );

        // 3. Índice de Pobreza General (0.0 a 100.0, estilo IMC)
        $povertyIndexGeneral = round(
            $this->clamp(($economicVulnerabilityScore * 0.55) + ($multidimensionalScore * 0.45), 0.0, 100.0),
            2
        );

        // 4. Segmentación Femenina y Masculina dentro del hogar
        $adultFemales = max(1, $femaleMembers);
        $adultMales = max(1, $maleMembers);

        $femaleAutonomousPerCapita = $incomeFemale / $adultFemales;
        $maleAutonomousPerCapita = $incomeMale / $adultMales;

        $femaleIncomeGap = $this->clamp((1.0 - ($femaleAutonomousPerCapita / ($wellbeingLine * 1.25))) * 100.0, 0.0, 100.0);
        $maleIncomeGap = $this->clamp((1.0 - ($maleAutonomousPerCapita / ($wellbeingLine * 1.25))) * 100.0, 0.0, 100.0);

        // Pobreza de tiempo y carga de cuidados no remunerados
        $carePenaltyFemale = $this->clamp(($femaleCareHours / 48.0) * 18.0, 0.0, 18.0)
            + ($femaleHeaded && $dependentChildren > 0 ? 5.5 : 0.0);
        $carePenaltyMale = $this->clamp(($maleCareHours / 48.0) * 12.0, 0.0, 12.0);

        $povertyIndexFemale = $femaleMembers > 0
            ? round($this->clamp(
                ($povertyIndexGeneral * 0.52) + ($femaleIncomeGap * 0.30) + ($multidimensionalScore * 0.18) + $carePenaltyFemale - 4.0,
                0.0,
                100.0
            ), 2)
            : $povertyIndexGeneral;

        $povertyIndexMale = $maleMembers > 0
            ? round($this->clamp(
                ($povertyIndexGeneral * 0.56) + ($maleIncomeGap * 0.28) + ($multidimensionalScore * 0.16) + $carePenaltyMale - 4.0,
                0.0,
                100.0
            ), 2)
            : $povertyIndexGeneral;

        $levelGeneral = $this->classifyScore($povertyIndexGeneral);
        $levelFemale = $this->classifyScore($povertyIndexFemale);
        $levelMale = $this->classifyScore($povertyIndexMale);

        $dimensionBreakdown = [
            'economic_sufficiency' => round($incomeGapScore, 1),
            'expense_pressure' => round($expensePressureScore, 1),
            'food_share_engel' => round($engelScore, 1),
            'housing_physical' => round(($floorWall + $overcrowding) / 2, 1),
            'basic_services' => round($basicServices, 1),
            'education_lag' => round($educationLag, 1),
            'health_social_security' => round($healthSecurity, 1),
            'food_insecurity' => round($foodInsecurity, 1),
            'gender_gap_delta' => round($povertyIndexFemale - $povertyIndexMale, 2),
        ];

        $policies = app(PolicyRecommendationService::class)->recommendForCalculation(
            $levelGeneral['slug'],
            $dimensionBreakdown,
            $povertyIndexFemale,
            $povertyIndexMale
        );

        return [
            'per_capita_income' => round($perCapitaIncome, 2),
            'income_to_expense_ratio' => round($incomeToExpenseRatio, 4),
            'economic_vulnerability_score' => $economicVulnerabilityScore,
            'multidimensional_deprivation_score' => $multidimensionalScore,
            'poverty_index_general' => $povertyIndexGeneral,
            'poverty_index_female' => $povertyIndexFemale,
            'poverty_index_male' => $povertyIndexMale,
            'level_general' => $levelGeneral,
            'level_female' => $levelFemale,
            'level_male' => $levelMale,
            'dimension_breakdown' => $dimensionBreakdown,
            'recommended_policies' => $policies,
        ];
    }

    public function classifyScore(float $score): array
    {
        $clamped = $this->clamp($score, 0.0, 100.0);

        try {
            $dbLevel = PovertyLevel::query()
                ->where('min_score', '<=', $clamped)
                ->where('max_score', '>=', $clamped)
                ->orderBy('display_order')
                ->first();

            if ($dbLevel) {
                return [
                    'id' => $dbLevel->id,
                    'slug' => $dbLevel->slug,
                    'name' => $dbLevel->name,
                    'bmi_analogy_label' => $dbLevel->bmi_analogy_label,
                    'min_score' => $dbLevel->min_score,
                    'max_score' => $dbLevel->max_score,
                    'color' => $dbLevel->color,
                    'badge_hex' => $dbLevel->badge_hex,
                    'description' => $dbLevel->description,
                    'implications' => $dbLevel->implications,
                ];
            }
        } catch (\Throwable) {
            // Fallback to static catalog when tables are not yet migrated
        }

        foreach (self::defaultLevelsCatalog() as $level) {
            if ($clamped >= $level['min_score'] && $clamped <= $level['max_score']) {
                return ['id' => null, ...$level];
            }
        }

        $last = self::defaultLevelsCatalog()[4];

        return ['id' => null, ...$last];
    }

    public function hydrateEvaluation(PovertyEvaluation $evaluation): void
    {
        $household = $evaluation->household ?: Household::find($evaluation->household_id);

        $result = $this->calculate([
            'zone_type' => $household?->zone_type ?? 'urbana',
            'total_members' => $household?->total_members ?? 4,
            'female_members' => $household?->female_members ?? 2,
            'male_members' => $household?->male_members ?? 2,
            'dependent_children' => $household?->dependent_children ?? 1,
            'female_headed' => $household?->female_headed ?? false,
            'monthly_income_total' => $evaluation->monthly_income_total,
            'monthly_income_female' => $evaluation->monthly_income_female,
            'monthly_income_male' => $evaluation->monthly_income_male,
            'monthly_expense_total' => $evaluation->monthly_expense_total,
            'monthly_expense_food' => $evaluation->monthly_expense_food,
            'monthly_expense_housing_services' => $evaluation->monthly_expense_housing_services,
            'monthly_expense_health_education' => $evaluation->monthly_expense_health_education,
            'female_unpaid_care_hours_weekly' => $evaluation->female_unpaid_care_hours_weekly,
            'male_unpaid_care_hours_weekly' => $evaluation->male_unpaid_care_hours_weekly,
            'housing_floor_wall_deprivation' => $evaluation->housing_floor_wall_deprivation,
            'housing_overcrowding_deprivation' => $evaluation->housing_overcrowding_deprivation,
            'basic_services_deprivation' => $evaluation->basic_services_deprivation,
            'education_lag_deprivation' => $evaluation->education_lag_deprivation,
            'health_security_deprivation' => $evaluation->health_security_deprivation,
            'food_insecurity_deprivation' => $evaluation->food_insecurity_deprivation,
        ]);

        $evaluation->per_capita_income = $result['per_capita_income'];
        $evaluation->income_to_expense_ratio = $result['income_to_expense_ratio'];
        $evaluation->economic_vulnerability_score = $result['economic_vulnerability_score'];
        $evaluation->multidimensional_deprivation_score = $result['multidimensional_deprivation_score'];
        $evaluation->poverty_index_general = $result['poverty_index_general'];
        $evaluation->poverty_index_female = $result['poverty_index_female'];
        $evaluation->poverty_index_male = $result['poverty_index_male'];
        $evaluation->level_slug_general = $result['level_general']['slug'];
        $evaluation->level_slug_female = $result['level_female']['slug'];
        $evaluation->level_slug_male = $result['level_male']['slug'];
        $evaluation->poverty_level_id = $result['level_general']['id'];
        $evaluation->dimension_breakdown = $result['dimension_breakdown'];
        $evaluation->recommended_policies_snapshot = $result['recommended_policies'];
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
