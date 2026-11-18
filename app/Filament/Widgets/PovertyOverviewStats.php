<?php

namespace App\Filament\Widgets;

use App\Models\Household;
use App\Models\PovertyEvaluation;
use App\Services\PovertyCalculatorService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PovertyOverviewStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $latestYear = (int) (PovertyEvaluation::max('year') ?? date('Y'));
        $query = PovertyEvaluation::where('year', $latestYear);

        $avgGeneral = round((float) (clone $query)->avg('poverty_index_general'), 1);
        $avgFemale = round((float) (clone $query)->avg('poverty_index_female'), 1);
        $avgMale = round((float) (clone $query)->avg('poverty_index_male'), 1);
        $genderGap = round($avgFemale - $avgMale, 1);

        $calculator = app(PovertyCalculatorService::class);
        $levelGeneral = $calculator->classifyScore($avgGeneral);

        return [
            Stat::make("Índice de Pobreza General ({$latestYear})", "{$avgGeneral} / 100 pts")
                ->description($levelGeneral['name'])
                ->color($levelGeneral['color']),
            Stat::make("Pobreza Femenina ({$latestYear})", "{$avgFemale} / 100 pts")
                ->description(($genderGap >= 0 ? "+{$genderGap}" : "{$genderGap}") . ' pts vs. Pobreza Masculina')
                ->color($avgFemale >= 40 ? 'danger' : 'info'),
            Stat::make("Pobreza Masculina ({$latestYear})", "{$avgMale} / 100 pts")
                ->description('Segmentación intrahogar masculina')
                ->color($avgMale >= 40 ? 'warning' : 'success'),
            Stat::make('Hogares y Series Evaluadas', Household::count() . ' hogares · ' . PovertyEvaluation::count() . ' registros')
                ->description('Cuestionarios + Importaciones ENIGH')
                ->color('primary'),
        ];
    }
}
