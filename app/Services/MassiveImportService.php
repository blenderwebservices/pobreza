<?php

namespace App\Services;

use App\Models\Household;
use App\Models\ImportBatch;
use App\Models\PovertyEvaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MassiveImportService
{
    public static function sampleCsvContent(): string
    {
        $headers = [
            'folio_hogar',
            'nombre_hogar',
            'estado',
            'municipio',
            'zona',
            'anio',
            'integrantes_total',
            'integrantes_mujeres',
            'integrantes_hombres',
            'dependientes_menores',
            'jefatura_femenina',
            'ingreso_mensual_total',
            'ingreso_femenino',
            'ingreso_masculino',
            'gasto_mensual_total',
            'gasto_alimentos',
            'gasto_vivienda_servicios',
            'gasto_salud_educacion',
            'horas_cuidado_femenino',
            'horas_cuidado_masculino',
            'carencia_piso_muros',
            'carencia_hacinamiento',
            'carencia_servicios_basicos',
            'carencia_rezago_educativo',
            'carencia_salud_seguridad_social',
            'carencia_alimentaria',
        ];

        $rows = [
            ['ENIGH-OAX-101', 'Hogar Sierra Juárez 101', 'Oaxaca', 'Ixtlán de Juárez', 'rural', 2023, 5, 3, 2, 2, 1, 6200, 1800, 4400, 6950, 4100, 1650, 1200, 38, 8, 65, 55, 70, 50, 75, 65],
            ['ENIGH-OAX-101', 'Hogar Sierra Juárez 101', 'Oaxaca', 'Ixtlán de Juárez', 'rural', 2024, 5, 3, 2, 2, 1, 6800, 2200, 4600, 7100, 4050, 1700, 1350, 36, 9, 55, 50, 60, 45, 70, 55],
            ['ENIGH-OAX-101', 'Hogar Sierra Juárez 101', 'Oaxaca', 'Ixtlán de Juárez', 'rural', 2025, 5, 3, 2, 2, 1, 7450, 2650, 4800, 7250, 3950, 1800, 1500, 34, 10, 45, 45, 50, 40, 60, 45],
            ['ENIGH-CDMX-204', 'Hogar Iztapalapa Oriente 204', 'Ciudad de México', 'Iztapalapa', 'urbana', 2023, 4, 2, 2, 1, 1, 13400, 5200, 8200, 12900, 5600, 4500, 2800, 30, 11, 20, 35, 25, 20, 45, 30],
            ['ENIGH-CDMX-204', 'Hogar Iztapalapa Oriente 204', 'Ciudad de México', 'Iztapalapa', 'urbana', 2024, 4, 2, 2, 1, 1, 12800, 4600, 8200, 13600, 6100, 4700, 2800, 32, 10, 25, 40, 30, 25, 50, 40],
            ['ENIGH-CDMX-204', 'Hogar Iztapalapa Oriente 204', 'Ciudad de México', 'Iztapalapa', 'urbana', 2025, 4, 2, 2, 1, 1, 12100, 4100, 8000, 14100, 6500, 4800, 2800, 35, 10, 30, 45, 35, 30, 55, 50],
            ['ENIGH-NL-309', 'Hogar San Nicolás 309', 'Nuevo León', 'San Nicolás de los Garza', 'urbana', 2025, 3, 2, 1, 0, 0, 34500, 16500, 18000, 21900, 6200, 9500, 6200, 16, 14, 0, 0, 0, 0, 5, 0],
        ];

        $lines = [implode(',', $headers)];
        foreach ($rows as $row) {
            $lines[] = implode(',', $row);
        }

        return implode("\n", $lines) . "\n";
    }

    public function importCsvString(string $csvContent, string $sourceName, string $filename, ?int $userId = null): ImportBatch
    {
        $lines = array_values(array_filter(
            preg_split('/\r\n|\r|\n/', trim($csvContent)),
            fn ($line) => trim($line) !== ''
        ));

        if (count($lines) < 2) {
            throw new \InvalidArgumentException('El archivo CSV debe contener encabezados y al menos una fila de datos.');
        }

        $headers = array_map(fn ($h) => Str::slug(trim($h), '_'), str_getcsv(array_shift($lines)));

        return DB::transaction(function () use ($lines, $headers, $sourceName, $filename, $userId) {
            $batch = ImportBatch::create([
                'user_id' => $userId,
                'batch_code' => 'LOTE-' . strtoupper(Str::random(6)),
                'source_name' => $sourceName,
                'filename' => $filename,
                'total_rows' => count($lines),
                'processed_rows' => 0,
                'status' => 'procesando',
            ]);

            $processed = 0;
            $scoresGeneral = [];
            $scoresFemale = [];
            $scoresMale = [];

            foreach ($lines as $rawLine) {
                $values = str_getcsv($rawLine);
                if (count($values) < 5) {
                    continue;
                }

                $row = [];
                foreach ($headers as $idx => $key) {
                    $row[$key] = $values[$idx] ?? null;
                }

                $folio = trim((string) ($row['folio_hogar'] ?? ('ENIGH-' . strtoupper(Str::random(5)))));
                $household = Household::updateOrCreate(
                    ['folio_code' => $folio],
                    [
                        'user_id' => $userId,
                        'name' => $row['nombre_hogar'] ?? ('Hogar ' . $folio),
                        'subject_type' => 'hogar',
                        'state' => $row['estado'] ?? 'Nacional',
                        'municipality' => $row['municipio'] ?? 'General',
                        'zone_type' => in_array(strtolower((string) ($row['zona'] ?? 'urbana')), ['rural', 'urbana'], true)
                            ? strtolower((string) $row['zona'])
                            : 'urbana',
                        'total_members' => max(1, (int) ($row['integrantes_total'] ?? 4)),
                        'female_members' => max(0, (int) ($row['integrantes_mujeres'] ?? 2)),
                        'male_members' => max(0, (int) ($row['integrantes_hombres'] ?? 2)),
                        'dependent_children' => max(0, (int) ($row['dependientes_menores'] ?? 1)),
                        'female_headed' => (bool) ((int) ($row['jefatura_femenina'] ?? 0)),
                    ]
                );

                $year = max(1990, min(2100, (int) ($row['anio'] ?? date('Y'))));

                $evaluation = PovertyEvaluation::updateOrCreate(
                    [
                        'household_id' => $household->id,
                        'year' => $year,
                        'quarter' => 'T4',
                    ],
                    [
                        'user_id' => $userId,
                        'import_batch_id' => $batch->id,
                        'evaluation_source' => 'importacion_enigh',
                        'monthly_income_total' => (float) ($row['ingreso_mensual_total'] ?? 0),
                        'monthly_income_female' => (float) ($row['ingreso_femenino'] ?? 0),
                        'monthly_income_male' => (float) ($row['ingreso_masculino'] ?? 0),
                        'monthly_expense_total' => (float) ($row['gasto_mensual_total'] ?? 0),
                        'monthly_expense_food' => (float) ($row['gasto_alimentos'] ?? 0),
                        'monthly_expense_housing_services' => (float) ($row['gasto_vivienda_servicios'] ?? 0),
                        'monthly_expense_health_education' => (float) ($row['gasto_salud_educacion'] ?? 0),
                        'female_unpaid_care_hours_weekly' => (float) ($row['horas_cuidado_femenino'] ?? 28),
                        'male_unpaid_care_hours_weekly' => (float) ($row['horas_cuidado_masculino'] ?? 10),
                        'housing_floor_wall_deprivation' => (int) ($row['carencia_piso_muros'] ?? 0),
                        'housing_overcrowding_deprivation' => (int) ($row['carencia_hacinamiento'] ?? 0),
                        'basic_services_deprivation' => (int) ($row['carencia_servicios_basicos'] ?? 0),
                        'education_lag_deprivation' => (int) ($row['carencia_rezago_educativo'] ?? 0),
                        'health_security_deprivation' => (int) ($row['carencia_salud_seguridad_social'] ?? 0),
                        'food_insecurity_deprivation' => (int) ($row['carencia_alimentaria'] ?? 0),
                    ]
                );

                $processed++;
                $scoresGeneral[] = $evaluation->poverty_index_general;
                $scoresFemale[] = $evaluation->poverty_index_female;
                $scoresMale[] = $evaluation->poverty_index_male;
            }

            $batch->update([
                'processed_rows' => $processed,
                'status' => 'completado',
                'summary' => [
                    'avg_general' => count($scoresGeneral) ? round(array_sum($scoresGeneral) / count($scoresGeneral), 2) : 0,
                    'avg_female' => count($scoresFemale) ? round(array_sum($scoresFemale) / count($scoresFemale), 2) : 0,
                    'avg_male' => count($scoresMale) ? round(array_sum($scoresMale) / count($scoresMale), 2) : 0,
                ],
            ]);

            return $batch->fresh();
        });
    }
}
