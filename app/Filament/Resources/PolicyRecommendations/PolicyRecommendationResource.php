<?php

namespace App\Filament\Resources\PolicyRecommendations;

use App\Filament\Resources\PolicyRecommendations\Pages\CreatePolicyRecommendation;
use App\Filament\Resources\PolicyRecommendations\Pages\EditPolicyRecommendation;
use App\Filament\Resources\PolicyRecommendations\Pages\ListPolicyRecommendations;
use App\Filament\Resources\PolicyRecommendations\Schemas\PolicyRecommendationForm;
use App\Filament\Resources\PolicyRecommendations\Tables\PolicyRecommendationsTable;
use App\Models\PolicyRecommendation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PolicyRecommendationResource extends Resource
{
    protected static ?string $model = PolicyRecommendation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Políticas Públicas de Mitigación';

    protected static ?string $modelLabel = 'Recomendación de Política Pública';

    protected static ?string $pluralModelLabel = 'Marco Técnico de Políticas Públicas';

    protected static string|UnitEnum|null $navigationGroup = 'Política Pública y Catálogos';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return auth()->user()?->isResearcher() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return PolicyRecommendationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PolicyRecommendationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPolicyRecommendations::route('/'),
            'create' => CreatePolicyRecommendation::route('/create'),
            'edit' => EditPolicyRecommendation::route('/{record}/edit'),
        ];
    }
}
