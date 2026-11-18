<?php

namespace App\Filament\Resources\PovertyEvaluations\Pages;

use App\Filament\Resources\PovertyEvaluations\PovertyEvaluationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPovertyEvaluation extends EditRecord
{
    protected static string $resource = PovertyEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
