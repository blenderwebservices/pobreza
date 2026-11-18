<?php

namespace App\Filament\Resources\PovertyLevels\Pages;

use App\Filament\Resources\PovertyLevels\PovertyLevelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPovertyLevels extends ListRecords
{
    protected static string $resource = PovertyLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
