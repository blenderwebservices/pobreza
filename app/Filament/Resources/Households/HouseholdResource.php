<?php

namespace App\Filament\Resources\Households;

use App\Filament\Resources\Households\Pages\CreateHousehold;
use App\Filament\Resources\Households\Pages\EditHousehold;
use App\Filament\Resources\Households\Pages\ListHouseholds;
use App\Filament\Resources\Households\Schemas\HouseholdForm;
use App\Filament\Resources\Households\Tables\HouseholdsTable;
use App\Models\Household;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class HouseholdResource extends Resource
{
    protected static ?string $model = Household::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static ?string $navigationLabel = 'Hogares y Grupos';

    protected static ?string $modelLabel = 'Hogar / Sujeto de Estudio';

    protected static ?string $pluralModelLabel = 'Hogares y Sujetos de Estudio';

    protected static string|UnitEnum|null $navigationGroup = 'Evaluación Socioeconómica';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && ! $user->isResearcher()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return HouseholdForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HouseholdsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHouseholds::route('/'),
            'create' => CreateHousehold::route('/create'),
            'edit' => EditHousehold::route('/{record}/edit'),
        ];
    }
}
