<?php

namespace App\Filament\Resources\PovertyEvaluations\Tables;

use App\Models\PovertyEvaluation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PovertyEvaluationsTable
{
    public static function configure(Table $table): Table
    {
        $levelLabels = [
            'muy_baja' => 'Muy Baja (0-19.9)',
            'baja' => 'Baja (20-39.9)',
            'moderada' => 'Moderada (40-59.9)',
            'alta' => 'Alta (60-79.9)',
            'muy_alta' => 'Muy Alta (80-100)',
        ];

        $badgeColor = fn (string $state): string => match ($state) {
            'muy_baja' => 'success',
            'baja' => 'info',
            'moderada' => 'warning',
            'alta', 'muy_alta' => 'danger',
            default => 'gray',
        };

        return $table
            ->defaultSort('year', 'desc')
            ->columns([
                TextColumn::make('household.folio_code')
                    ->label('Folio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('household.name')
                    ->label('Hogar / Sujeto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->label('Año')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('poverty_index_general')
                    ->label('IP General (IMC)')
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 1) . ' pts'),
                TextColumn::make('level_slug_general')
                    ->label('Nivel Progresivo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $levelLabels[$state] ?? $state)
                    ->color($badgeColor),
                TextColumn::make('poverty_index_female')
                    ->label('IP Femenina')
                    ->sortable()
                    ->formatStateUsing(fn ($state, PovertyEvaluation $r) => number_format((float) $state, 1) . ' (' . ($levelLabels[$r->level_slug_female] ?? $r->level_slug_female) . ')'),
                TextColumn::make('poverty_index_male')
                    ->label('IP Masculina')
                    ->sortable()
                    ->formatStateUsing(fn ($state, PovertyEvaluation $r) => number_format((float) $state, 1) . ' (' . ($levelLabels[$r->level_slug_male] ?? $r->level_slug_male) . ')'),
                TextColumn::make('income_to_expense_ratio')
                    ->label('Ingreso / Gasto')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2) . 'x'),
                TextColumn::make('evaluation_source')
                    ->label('Fuente')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'importacion_enigh' ? 'ENIGH Oficial' : 'Cuestionario'),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Año')
                    ->options([
                        2021 => '2021',
                        2022 => '2022',
                        2023 => '2023',
                        2024 => '2024',
                        2025 => '2025',
                        2026 => '2026',
                    ]),
                SelectFilter::make('level_slug_general')
                    ->label('Nivel de Pobreza General')
                    ->options($levelLabels),
                SelectFilter::make('evaluation_source')
                    ->label('Fuente de Datos')
                    ->options([
                        'cuestionario' => 'Cuestionario Integrado',
                        'importacion_enigh' => 'Estudio Oficial (ENIGH)',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
