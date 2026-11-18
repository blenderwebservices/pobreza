<?php

namespace App\Services;

use Illuminate\Support\Collection;

class PovertyForecastService
{
    public function __construct(
        protected PovertyCalculatorService $calculator
    ) {}

    /**
     * Analiza una colección de evaluaciones (o puntos anuales) y proyecta los próximos $horizonYears años.
     */
    public function analyzeAndForecast(Collection|array $evaluations, string $segment = 'general', int $horizonYears = 3): array
    {
        $column = match ($segment) {
            'femenina' => 'poverty_index_female',
            'masculina' => 'poverty_index_male',
            default => 'poverty_index_general',
        };

        $grouped = collect($evaluations)
            ->groupBy('year')
            ->map(function ($items, $year) use ($column) {
                return [
                    'year' => (int) $year,
                    'score' => round((float) collect($items)->avg($column), 2),
                    'general' => round((float) collect($items)->avg('poverty_index_general'), 2),
                    'female' => round((float) collect($items)->avg('poverty_index_female'), 2),
                    'male' => round((float) collect($items)->avg('poverty_index_male'), 2),
                    'income_avg' => round((float) collect($items)->avg('monthly_income_total'), 2),
                    'expense_avg' => round((float) collect($items)->avg('monthly_expense_total'), 2),
                ];
            })
            ->sortBy('year')
            ->values();

        if ($grouped->isEmpty()) {
            return [
                'historical' => [],
                'forecasts' => [],
                'slope' => 0.0,
                'delta_total' => 0.0,
                'trend_status' => 'Sin datos suficientes',
                'trend_badge_color' => 'gray',
                'narrative' => 'Aún no existen registros históricos para calcular la tendencia ni proyectar escenarios futuros.',
            ];
        }

        $n = $grouped->count();
        $firstPoint = $grouped->first();
        $lastPoint = $grouped->last();
        $deltaTotal = round($lastPoint['score'] - $firstPoint['score'], 2);

        $slope = 0.0;
        $intercept = $lastPoint['score'];

        if ($n >= 2) {
            $meanX = $grouped->avg('year');
            $meanY = $grouped->avg('score');

            $num = 0.0;
            $den = 0.0;
            foreach ($grouped as $pt) {
                $dx = $pt['year'] - $meanX;
                $num += $dx * ($pt['score'] - $meanY);
                $den += $dx * $dx;
            }

            $slope = $den > 0 ? round($num / $den, 3) : 0.0;
            $intercept = $meanY - ($slope * $meanX);
        }

        if ($slope <= -0.6) {
            $trendStatus = 'Mejorando (Reducción de Pobreza)';
            $trendBadgeColor = 'success';
        } elseif ($slope >= 0.6) {
            $trendStatus = 'Empeorando (Aumento de Pobreza)';
            $trendBadgeColor = 'danger';
        } else {
            $trendStatus = 'Estable / Estancamiento Estructural';
            $trendBadgeColor = 'warning';
        }

        $forecasts = [];
        $lastYear = (int) $lastPoint['year'];

        for ($step = 1; $step <= $horizonYears; $step++) {
            $targetYear = $lastYear + $step;
            $rawInertial = $n >= 2
                ? ($intercept + ($slope * $targetYear))
                : $lastPoint['score'];

            $inertialScore = round(max(0.0, min(100.0, $rawInertial)), 2);
            $mitigatedScore = round(max(0.0, min(100.0, $inertialScore - ($step * 4.75))), 2);

            $inertialLevel = $this->calculator->classifyScore($inertialScore);
            $mitigatedLevel = $this->calculator->classifyScore($mitigatedScore);

            $forecasts[] = [
                'year' => $targetYear,
                'inertial_score' => $inertialScore,
                'inertial_level_name' => $inertialLevel['name'],
                'inertial_level_slug' => $inertialLevel['slug'],
                'inertial_badge_hex' => $inertialLevel['badge_hex'],
                'mitigated_score' => $mitigatedScore,
                'mitigated_level_name' => $mitigatedLevel['name'],
                'mitigated_level_slug' => $mitigatedLevel['slug'],
                'mitigated_badge_hex' => $mitigatedLevel['badge_hex'],
            ];
        }

        $finalForecast = end($forecasts);
        $segmentLabel = match ($segment) {
            'femenina' => 'pobreza femenina',
            'masculina' => 'pobreza masculina',
            default => 'pobreza general',
        };

        $directionText = $slope < -0.6
            ? sprintf('una trayectoria favorable con reducción promedio de %.2f puntos anuales', abs($slope))
            : ($slope > 0.6
                ? sprintf('un deterioro progresivo con incremento promedio de +%.2f puntos anuales', $slope)
                : 'una trayectoria prácticamente estancada con variaciones menores a 0.6 puntos por año');

        $narrative = sprintf(
            'Entre %d y %d, el índice de %s pasó de %.1f a %.1f puntos (%s). De mantenerse la tendencia inercial actual sin intervención adicional, para el año %d se proyecta un Índice de Pobreza de %.1f puntos (%s). Con la aplicación del marco técnico de política pública recomendado, el índice podría reducirse a %.1f puntos (%s).',
            $firstPoint['year'],
            $lastPoint['year'],
            $segmentLabel,
            $firstPoint['score'],
            $lastPoint['score'],
            $directionText,
            $finalForecast['year'],
            $finalForecast['inertial_score'],
            $finalForecast['inertial_level_name'],
            $finalForecast['mitigated_score'],
            $finalForecast['mitigated_level_name']
        );

        return [
            'segment' => $segment,
            'historical' => $grouped->all(),
            'forecasts' => $forecasts,
            'slope' => $slope,
            'delta_total' => $deltaTotal,
            'trend_status' => $trendStatus,
            'trend_badge_color' => $trendBadgeColor,
            'narrative' => $narrative,
        ];
    }
}
