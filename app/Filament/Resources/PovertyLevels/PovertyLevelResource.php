<?php

namespace App\Filament\Resources\PovertyLevels;

use App\Filament\Resources\PovertyLevels\Pages\CreatePovertyLevel;
use App\Filament\Resources\PovertyLevels\Pages\EditPovertyLevel;
use App\Filament\Resources\PovertyLevels\Pages\ListPovertyLevels;
use App\Filament\Resources\PovertyLevels\Schemas\PovertyLevelForm;
use App\Filament\Resources\PovertyLevels\Tables\PovertyLevelsTable;
use App\Models\PovertyLevel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PovertyLevelResource extends Resource
{
    protected static ?string $model = PovertyLevel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $navigationLabel = 'Niveles de Pobreza (Escala IMC)';

    protected static ?string $modelLabel = 'Nivel Progresivo de Pobreza';

    protected static ?string $pluralModelLabel = 'Niveles Progresivos de Pobreza';

    protected static string|UnitEnum|null $navigationGroup = 'Política Pública y Catálogos';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return auth()->user()?->isResearcher() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return PovertyLevelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PovertyLevelsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPovertyLevels::route('/'),
            'create' => CreatePovertyLevel::route('/create'),
            'edit' => EditPovertyLevel::route('/{record}/edit'),
        ];
    }
}
