<?php

namespace App\Filament\Resources\PovertyLevels\Pages;

use App\Filament\Resources\PovertyLevels\PovertyLevelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPovertyLevel extends EditRecord
{
    protected static string $resource = PovertyLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
