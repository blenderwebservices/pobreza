<?php

namespace App\Filament\Resources\PolicyRecommendations\Pages;

use App\Filament\Resources\PolicyRecommendations\PolicyRecommendationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPolicyRecommendation extends EditRecord
{
    protected static string $resource = PolicyRecommendationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
