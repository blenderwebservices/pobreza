<?php

namespace App\Filament\Resources\Households\Tables;

use App\Models\Household;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HouseholdsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio_code')
                    ->label('Folio')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('name')
                    ->label('Hogar / Sujeto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->label('Estado / Municipio')
                    ->description(fn (Household $record): string => $record->municipality . ' (' . ucfirst($record->zone_type) . ')')
                    ->searchable(),
                TextColumn::make('total_members')
                    ->label('Integrantes (M / H)')
                    ->formatStateUsing(fn ($state, Household $record): string => "{$record->total_members} ({$record->female_members}M / {$record->male_members}H)"),
                IconColumn::make('female_headed')
                    ->label('Jefatura Femenina')
                    ->boolean(),
                TextColumn::make('evaluations_count')
                    ->counts('evaluations')
                    ->label('Años Evaluados')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('zone_type')
                    ->label('Zona')
                    ->options([
                        'urbana' => 'Urbana',
                        'rural' => 'Rural',
                    ]),
            ])
            ->recordActions([
                Action::make('ver_pronostico')
                    ->label('Tendencia y Pronóstico')
                    ->icon('heroicon-o-chart-bar-square')
                    ->color('info')
                    ->url(fn (Household $record): string => route('temporal.index', ['household_id' => $record->id])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
