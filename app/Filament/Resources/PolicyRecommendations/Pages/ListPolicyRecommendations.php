<?php

namespace App\Filament\Resources\PolicyRecommendations\Pages;

use App\Filament\Resources\PolicyRecommendations\PolicyRecommendationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPolicyRecommendations extends ListRecords
{
    protected static string $resource = PolicyRecommendationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
