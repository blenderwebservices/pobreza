<?php

namespace App\Filament\Resources\PolicyRecommendations\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PolicyRecommendationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('povertyLevel.name')
                    ->label('Nivel de Pobreza')
                    ->sortable()
                    ->badge(),
                TextColumn::make('title')
                    ->label('Intervención de Política Pública')
                    ->weight('bold')
                    ->searchable()
                    ->limit(55),
                TextColumn::make('dimension')
                    ->label('Dimensión')
                    ->badge()
                    ->color('info'),
                TextColumn::make('target_segment')
                    ->label('Segmento')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critica' => 'danger',
                        'alta' => 'warning',
                        'media' => 'info',
                        default => 'success',
                    }),
                TextColumn::make('expected_impact')
                    ->label('Impacto Técnico Estimado')
                    ->limit(45),
                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('target_segment')
                    ->label('Segmento')
                    ->options([
                        'todos' => 'General / Todos',
                        'femenina' => 'Pobreza Femenina',
                        'masculina' => 'Pobreza Masculina',
                    ]),
                SelectFilter::make('dimension')
                    ->label('Dimensión')
                    ->options([
                        'ingreso' => 'Ingreso',
                        'alimentacion' => 'Alimentación',
                        'vivienda' => 'Vivienda',
                        'servicios' => 'Servicios Básicos',
                        'salud' => 'Salud y Seguridad Social',
                        'genero' => 'Género y Cuidados',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
