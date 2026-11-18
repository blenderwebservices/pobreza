<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\ImportBatch;
use App\Models\PolicyRecommendation;
use App\Models\PovertyEvaluation;
use App\Models\PovertyLevel;
use App\Models\User;
use App\Services\PolicyRecommendationService;
use App\Services\PovertyCalculatorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuarios con control de roles (admin, investigador, individuo)
        $admin = User::updateOrCreate(
            ['email' => 'admin@pobreza.gob.mx'],
            [
                'name' => 'Dra. Elena Vázquez (Administradora)',
                'role' => User::ROLE_ADMIN,
                'institution' => 'Consejo Nacional de Evaluación Socioeconómica',
                'phone' => '55-5000-1001',
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );

        $researcher = User::updateOrCreate(
            ['email' => 'investigador@pobreza.gob.mx'],
            [
                'name' => 'Dr. Marco Tulio Herrera (Investigador)',
                'role' => User::ROLE_RESEARCHER,
                'institution' => 'Instituto de Investigaciones Económicas y Demográficas',
                'phone' => '55-5000-2002',
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );

        $citizen = User::updateOrCreate(
            ['email' => 'ciudadano@pobreza.gob.mx'],
            [
                'name' => 'María Fernanda López (Hogar Individual)',
                'role' => User::ROLE_INDIVIDUAL,
                'institution' => 'Usuaria Ciudadana',
                'phone' => '55-5000-3003',
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Catálogo de 5 Niveles Progresivos de Pobreza (No Binarios, tipo IMC)
        $levelMap = [];
        foreach (PovertyCalculatorService::defaultLevelsCatalog() as $levelData) {
            $level = PovertyLevel::updateOrCreate(
                ['slug' => $levelData['slug']],
                $levelData
            );
            $levelMap[$level->slug] = $level;
        }

        // 3. Catálogo de Recomendaciones Técnicas de Política Pública
        foreach (PolicyRecommendationService::defaultRecommendationsCatalog() as $slug => $items) {
            $level = $levelMap[$slug] ?? null;
            if (! $level) {
                continue;
            }
            foreach ($items as $item) {
                PolicyRecommendation::updateOrCreate(
                    [
                        'poverty_level_id' => $level->id,
                        'title' => $item['title'],
                    ],
                    [
                        'target_segment' => $item['target_segment'],
                        'dimension' => $item['dimension'],
                        'technical_action' => $item['technical_action'],
                        'ideal_framework_rationale' => $item['ideal_framework_rationale'],
                        'priority' => $item['priority'],
                        'expected_impact' => $item['expected_impact'],
                        'is_active' => true,
                    ]
                );
            }
        }

        // 4. Lote inicial de estudio oficial (ENIGH Histórico)
        $batch = ImportBatch::updateOrCreate(
            ['batch_code' => 'ENIGH-HIST-2026'],
            [
                'user_id' => $researcher->id,
                'source_name' => 'Panel Longitudinal ENIGH 2021-2026 (INEGI / CONEVAL)',
                'filename' => 'enigh_panel_2021_2026.csv',
                'total_rows' => 24,
                'processed_rows' => 24,
                'status' => 'completado',
                'summary' => [
                    'avg_general' => 47.8,
                    'avg_female' => 53.4,
                    'avg_male' => 44.1,
                ],
            ]
        );

        // 5. Hogares e Individuos con series históricas multi-anuales (2021-2026)
        $householdsData = [
            [
                'meta' => [
                    'user_id' => $citizen->id,
                    'folio_code' => 'HOG-CDMX-001',
                    'name' => 'Familia López Hernández (Iztapalapa)',
                    'subject_type' => 'hogar',
                    'state' => 'Ciudad de México',
                    'municipality' => 'Iztapalapa',
                    'zone_type' => 'urbana',
                    'total_members' => 4,
                    'female_members' => 2,
                    'male_members' => 2,
                    'dependent_children' => 2,
                    'female_headed' => true,
                    'notes' => 'Hogar urbano con jefatura femenina que ha reducido su índice de pobreza gracias a mejoras laborales y de vivienda.',
                ],
                'years' => [
                    2021 => ['inc_f' => 3800, 'inc_m' => 5400, 'food' => 4900, 'hous' => 3100, 'hedu' => 1600, 'flr' => 45, 'ovr' => 55, 'srv' => 40, 'edu' => 45, 'hlt' => 65, 'fdi' => 55, 'ch_f' => 36, 'ch_m' => 10],
                    2022 => ['inc_f' => 4400, 'inc_m' => 5900, 'food' => 5000, 'hous' => 3200, 'hedu' => 1650, 'flr' => 40, 'ovr' => 50, 'srv' => 35, 'edu' => 40, 'hlt' => 55, 'fdi' => 48, 'ch_f' => 34, 'ch_m' => 11],
                    2023 => ['inc_f' => 5200, 'inc_m' => 6500, 'food' => 5100, 'hous' => 3300, 'hedu' => 1800, 'flr' => 30, 'ovr' => 40, 'srv' => 25, 'edu' => 35, 'hlt' => 45, 'fdi' => 40, 'ch_f' => 31, 'ch_m' => 12],
                    2024 => ['inc_f' => 6100, 'inc_m' => 7200, 'food' => 5250, 'hous' => 3450, 'hedu' => 1950, 'flr' => 20, 'ovr' => 30, 'srv' => 20, 'edu' => 25, 'hlt' => 35, 'fdi' => 30, 'ch_f' => 28, 'ch_m' => 13],
                    2025 => ['inc_f' => 7100, 'inc_m' => 7900, 'food' => 5400, 'hous' => 3600, 'hedu' => 2100, 'flr' => 15, 'ovr' => 25, 'srv' => 15, 'edu' => 20, 'hlt' => 25, 'fdi' => 22, 'ch_f' => 25, 'ch_m' => 14],
                    2026 => ['inc_f' => 8200, 'inc_m' => 8600, 'food' => 5500, 'hous' => 3750, 'hedu' => 2300, 'flr' => 10, 'ovr' => 20, 'srv' => 10, 'edu' => 15, 'hlt' => 20, 'fdi' => 15, 'ch_f' => 22, 'ch_m' => 15],
                ],
            ],
            [
                'meta' => [
                    'user_id' => $researcher->id,
                    'folio_code' => 'HOG-CHIS-002',
                    'name' => 'Comunidad Altos de Chamula (Grupo Rural)',
                    'subject_type' => 'grupo_comunitario',
                    'state' => 'Chiapas',
                    'municipality' => 'San Juan Chamula',
                    'zone_type' => 'rural',
                    'total_members' => 6,
                    'female_members' => 4,
                    'male_members' => 2,
                    'dependent_children' => 3,
                    'female_headed' => true,
                    'notes' => 'Hogar rural indígena en condición de pobreza muy alta con empeoramiento por encarecimiento alimentario y sequías.',
                ],
                'years' => [
                    2021 => ['inc_f' => 1400, 'inc_m' => 3100, 'food' => 3900, 'hous' => 1200, 'hedu' => 900, 'flr' => 75, 'ovr' => 70, 'srv' => 80, 'edu' => 70, 'hlt' => 80, 'fdi' => 70, 'ch_f' => 44, 'ch_m' => 9],
                    2022 => ['inc_f' => 1350, 'inc_m' => 3000, 'food' => 4100, 'hous' => 1250, 'hedu' => 950, 'flr' => 75, 'ovr' => 75, 'srv' => 80, 'edu' => 72, 'hlt' => 82, 'fdi' => 75, 'ch_f' => 45, 'ch_m' => 9],
                    2023 => ['inc_f' => 1250, 'inc_m' => 2950, 'food' => 4300, 'hous' => 1300, 'hedu' => 1000, 'flr' => 80, 'ovr' => 78, 'srv' => 82, 'edu' => 75, 'hlt' => 85, 'fdi' => 80, 'ch_f' => 46, 'ch_m' => 8],
                    2024 => ['inc_f' => 1200, 'inc_m' => 2850, 'food' => 4500, 'hous' => 1400, 'hedu' => 1050, 'flr' => 82, 'ovr' => 80, 'srv' => 85, 'edu' => 78, 'hlt' => 88, 'fdi' => 84, 'ch_f' => 47, 'ch_m' => 8],
                    2025 => ['inc_f' => 1150, 'inc_m' => 2700, 'food' => 4700, 'hous' => 1450, 'hedu' => 1100, 'flr' => 85, 'ovr' => 84, 'srv' => 88, 'edu' => 80, 'hlt' => 90, 'fdi' => 88, 'ch_f' => 48, 'ch_m' => 8],
                    2026 => ['inc_f' => 1100, 'inc_m' => 2600, 'food' => 4900, 'hous' => 1500, 'hedu' => 1150, 'flr' => 88, 'ovr' => 86, 'srv' => 90, 'edu' => 82, 'hlt' => 92, 'fdi' => 92, 'ch_f' => 48, 'ch_m' => 8],
                ],
            ],
            [
                'meta' => [
                    'user_id' => $researcher->id,
                    'folio_code' => 'HOG-PUE-003',
                    'name' => 'Hogar Martínez Flores (Tehuacán)',
                    'subject_type' => 'hogar',
                    'state' => 'Puebla',
                    'municipality' => 'Tehuacán',
                    'zone_type' => 'urbana',
                    'total_members' => 5,
                    'female_members' => 3,
                    'male_members' => 2,
                    'dependent_children' => 2,
                    'female_headed' => false,
                    'notes' => 'Hogar en pobreza alta/moderada con marcada asimetría entre el índice femenino y el masculino.',
                ],
                'years' => [
                    2021 => ['inc_f' => 2100, 'inc_m' => 7800, 'food' => 5900, 'hous' => 2900, 'hedu' => 1800, 'flr' => 50, 'ovr' => 55, 'srv' => 50, 'edu' => 45, 'hlt' => 60, 'fdi' => 55, 'ch_f' => 40, 'ch_m' => 8],
                    2022 => ['inc_f' => 2250, 'inc_m' => 8000, 'food' => 6050, 'hous' => 3000, 'hedu' => 1850, 'flr' => 48, 'ovr' => 52, 'srv' => 48, 'edu' => 45, 'hlt' => 58, 'fdi' => 52, 'ch_f' => 39, 'ch_m' => 8],
                    2023 => ['inc_f' => 2350, 'inc_m' => 8100, 'food' => 6150, 'hous' => 3100, 'hedu' => 1900, 'flr' => 45, 'ovr' => 50, 'srv' => 45, 'edu' => 42, 'hlt' => 55, 'fdi' => 50, 'ch_f' => 39, 'ch_m' => 9],
                    2024 => ['inc_f' => 2400, 'inc_m' => 8300, 'food' => 6250, 'hous' => 3150, 'hedu' => 1950, 'flr' => 45, 'ovr' => 50, 'srv' => 44, 'edu' => 40, 'hlt' => 54, 'fdi' => 48, 'ch_f' => 38, 'ch_m' => 9],
                    2025 => ['inc_f' => 2500, 'inc_m' => 8400, 'food' => 6350, 'hous' => 3200, 'hedu' => 2000, 'flr' => 42, 'ovr' => 48, 'srv' => 42, 'edu' => 40, 'hlt' => 52, 'fdi' => 46, 'ch_f' => 38, 'ch_m' => 9],
                    2026 => ['inc_f' => 2600, 'inc_m' => 8550, 'food' => 6450, 'hous' => 3250, 'hedu' => 2050, 'flr' => 40, 'ovr' => 45, 'srv' => 40, 'edu' => 38, 'hlt' => 50, 'fdi' => 45, 'ch_f' => 37, 'ch_m' => 10],
                ],
            ],
            [
                'meta' => [
                    'user_id' => $admin->id,
                    'folio_code' => 'HOG-NL-004',
                    'name' => 'Hogar Garza Elizondo (Monterrey)',
                    'subject_type' => 'individuo',
                    'state' => 'Nuevo León',
                    'municipality' => 'Monterrey',
                    'zone_type' => 'urbana',
                    'total_members' => 2,
                    'female_members' => 1,
                    'male_members' => 1,
                    'dependent_children' => 0,
                    'female_headed' => false,
                    'notes' => 'Hogar biparental sin dependientes en nivel de pobreza muy baja y paridad de ingresos.',
                ],
                'years' => [
                    2021 => ['inc_f' => 15500, 'inc_m' => 16200, 'food' => 5200, 'hous' => 7800, 'hedu' => 4200, 'flr' => 0, 'ovr' => 0, 'srv' => 5, 'edu' => 0, 'hlt' => 10, 'fdi' => 0, 'ch_f' => 14, 'ch_m' => 12],
                    2022 => ['inc_f' => 16400, 'inc_m' => 17000, 'food' => 5400, 'hous' => 8000, 'hedu' => 4400, 'flr' => 0, 'ovr' => 0, 'srv' => 0, 'edu' => 0, 'hlt' => 8, 'fdi' => 0, 'ch_f' => 14, 'ch_m' => 12],
                    2023 => ['inc_f' => 17500, 'inc_m' => 18100, 'food' => 5600, 'hous' => 8300, 'hedu' => 4600, 'flr' => 0, 'ovr' => 0, 'srv' => 0, 'edu' => 0, 'hlt' => 5, 'fdi' => 0, 'ch_f' => 13, 'ch_m' => 13],
                    2024 => ['inc_f' => 18900, 'inc_m' => 19200, 'food' => 5800, 'hous' => 8500, 'hedu' => 4800, 'flr' => 0, 'ovr' => 0, 'srv' => 0, 'edu' => 0, 'hlt' => 0, 'fdi' => 0, 'ch_f' => 13, 'ch_m' => 13],
                    2025 => ['inc_f' => 20100, 'inc_m' => 20400, 'food' => 6000, 'hous' => 8800, 'hedu' => 5100, 'flr' => 0, 'ovr' => 0, 'srv' => 0, 'edu' => 0, 'hlt' => 0, 'fdi' => 0, 'ch_f' => 12, 'ch_m' => 12],
                    2026 => ['inc_f' => 21500, 'inc_m' => 21800, 'food' => 6200, 'hous' => 9000, 'hedu' => 5300, 'flr' => 0, 'ovr' => 0, 'srv' => 0, 'edu' => 0, 'hlt' => 0, 'fdi' => 0, 'ch_f' => 12, 'ch_m' => 12],
                ],
            ],
        ];

        foreach ($householdsData as $entry) {
            $household = Household::updateOrCreate(
                ['folio_code' => $entry['meta']['folio_code']],
                $entry['meta']
            );

            foreach ($entry['years'] as $year => $metrics) {
                $incTotal = $metrics['inc_f'] + $metrics['inc_m'];
                $expTotal = $metrics['food'] + $metrics['hous'] + $metrics['hedu'];

                PovertyEvaluation::updateOrCreate(
                    [
                        'household_id' => $household->id,
                        'year' => $year,
                        'quarter' => 'T4',
                    ],
                    [
                        'user_id' => $household->user_id,
                        'import_batch_id' => $batch->id,
                        'evaluation_source' => $year <= 2024 ? 'importacion_enigh' : 'cuestionario',
                        'monthly_income_total' => $incTotal,
                        'monthly_income_female' => $metrics['inc_f'],
                        'monthly_income_male' => $metrics['inc_m'],
                        'monthly_expense_total' => $expTotal,
                        'monthly_expense_food' => $metrics['food'],
                        'monthly_expense_housing_services' => $metrics['hous'],
                        'monthly_expense_health_education' => $metrics['hedu'],
                        'female_unpaid_care_hours_weekly' => $metrics['ch_f'],
                        'male_unpaid_care_hours_weekly' => $metrics['ch_m'],
                        'housing_floor_wall_deprivation' => $metrics['flr'],
                        'housing_overcrowding_deprivation' => $metrics['ovr'],
                        'basic_services_deprivation' => $metrics['srv'],
                        'education_lag_deprivation' => $metrics['edu'],
                        'health_security_deprivation' => $metrics['hlt'],
                        'food_insecurity_deprivation' => $metrics['fdi'],
                    ]
                );
            }
        }
    }
}
