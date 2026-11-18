<?php

namespace App\Filament\Widgets;

use App\Models\PovertyEvaluation;
use App\Services\PovertyForecastService;
use Filament\Widgets\ChartWidget;

class PovertyTrendForecastChart extends ChartWidget
{
    protected ?string $heading = 'Evolución Histórica y Pronóstico a 3 Años (General, Femenina y Masculina)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $evaluations = PovertyEvaluation::orderBy('year')->get();
        $forecaster = app(PovertyForecastService::class);

        $general = $forecaster->analyzeAndForecast($evaluations, 'general', 3);
        $female = $forecaster->analyzeAndForecast($evaluations, 'femenina', 3);
        $male = $forecaster->analyzeAndForecast($evaluations, 'masculina', 3);

        $labels = [];
        $seriesGeneral = [];
        $seriesFemale = [];
        $seriesMale = [];
        $seriesMitigated = [];

        foreach ($general['historical'] as $idx => $pt) {
            $labels[] = (string) $pt['year'];
            $seriesGeneral[] = $pt['score'];
            $seriesFemale[] = $female['historical'][$idx]['score'] ?? $pt['score'];
            $seriesMale[] = $male['historical'][$idx]['score'] ?? $pt['score'];
            $seriesMitigated[] = $pt['score'];
        }

        foreach ($general['forecasts'] as $idx => $fc) {
            $labels[] = $fc['year'] . ' (Pronóstico)';
            $seriesGeneral[] = $fc['inertial_score'];
            $seriesFemale[] = $female['forecasts'][$idx]['inertial_score'] ?? $fc['inertial_score'];
            $seriesMale[] = $male['forecasts'][$idx]['inertial_score'] ?? $fc['inertial_score'];
            $seriesMitigated[] = $fc['mitigated_score'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'IP General (Tendencia Inercial)',
                    'data' => $seriesGeneral,
                    'borderColor' => '#d97706',
                    'backgroundColor' => 'rgba(217, 119, 6, 0.12)',
                ],
                [
                    'label' => 'IP Pobreza Femenina',
                    'data' => $seriesFemale,
                    'borderColor' => '#e11d48',
                    'backgroundColor' => 'rgba(225, 29, 72, 0.08)',
                ],
                [
                    'label' => 'IP Pobreza Masculina',
                    'data' => $seriesMale,
                    'borderColor' => '#0284c7',
                    'backgroundColor' => 'rgba(2, 132, 199, 0.08)',
                ],
                [
                    'label' => 'IP con Política Pública de Mitigación',
                    'data' => $seriesMitigated,
                    'borderColor' => '#16a34a',
                    'borderDash' => [6, 4],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
