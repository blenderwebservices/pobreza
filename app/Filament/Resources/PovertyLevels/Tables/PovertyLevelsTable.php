<?php

namespace App\Filament\Resources\PovertyLevels\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PovertyLevelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('display_order')
            ->columns([
                TextColumn::make('display_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nivel Progresivo')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('bmi_analogy_label')
                    ->label('Rango Índice (Tipo IMC)')
                    ->badge()
                    ->color(fn ($record) => $record->color),
                TextColumn::make('description')
                    ->label('Significado')
                    ->limit(70),
                TextColumn::make('implications')
                    ->label('Implicaciones')
                    ->limit(70),
                TextColumn::make('policy_recommendations_count')
                    ->counts('policyRecommendations')
                    ->label('Políticas Asociadas')
                    ->badge(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
