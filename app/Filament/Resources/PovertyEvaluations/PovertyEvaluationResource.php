<?php

namespace App\Filament\Resources\PovertyEvaluations;

use App\Filament\Resources\PovertyEvaluations\Pages\CreatePovertyEvaluation;
use App\Filament\Resources\PovertyEvaluations\Pages\EditPovertyEvaluation;
use App\Filament\Resources\PovertyEvaluations\Pages\ListPovertyEvaluations;
use App\Filament\Resources\PovertyEvaluations\Schemas\PovertyEvaluationForm;
use App\Filament\Resources\PovertyEvaluations\Tables\PovertyEvaluationsTable;
use App\Models\PovertyEvaluation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PovertyEvaluationResource extends Resource
{
    protected static ?string $model = PovertyEvaluation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $navigationLabel = 'Evaluaciones e Índice (IP)';

    protected static ?string $modelLabel = 'Evaluación de Pobreza';

    protected static ?string $pluralModelLabel = 'Evaluaciones de Pobreza';

    protected static string|UnitEnum|null $navigationGroup = 'Evaluación Socioeconómica';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['household', 'povertyLevel']);
        $user = auth()->user();

        if ($user && ! $user->isResearcher()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return PovertyEvaluationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PovertyEvaluationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPovertyEvaluations::route('/'),
            'create' => CreatePovertyEvaluation::route('/create'),
            'edit' => EditPovertyEvaluation::route('/{record}/edit'),
        ];
    }
}
