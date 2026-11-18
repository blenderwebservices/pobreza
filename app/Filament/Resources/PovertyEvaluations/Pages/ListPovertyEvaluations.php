<?php

namespace App\Filament\Resources\PovertyEvaluations\Pages;

use App\Filament\Resources\PovertyEvaluations\PovertyEvaluationResource;
use App\Services\MassiveImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPovertyEvaluations extends ListRecords
{
    protected static string $resource = PovertyEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importar_enigh_csv')
                ->label('Importación Masiva Oficial (ENIGH CSV)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->schema([
                    TextInput::make('source_name')
                        ->label('Nombre del Estudio Oficial / Encuesta')
                        ->default('Encuesta Nacional de Ingresos y Gastos de los Hogares (ENIGH)')
                        ->required(),
                    Textarea::make('csv_content')
                        ->label('Contenido CSV (Encabezados + Filas de Hogares)')
                        ->rows(10)
                        ->default(fn () => MassiveImportService::sampleCsvContent())
                        ->helperText('Puede pegar directamente un bloque CSV con formato ENIGH o usar el lote oficial precargado.')
                        ->required(),
                ])
                ->action(function (array $data, MassiveImportService $importer): void {
                    $batch = $importer->importCsvString(
                        $data['csv_content'],
                        $data['source_name'],
                        'filament_import_' . date('Ymd_His') . '.csv',
                        auth()->id()
                    );

                    Notification::make()
                        ->title("Importación completada ({$batch->batch_code})")
                        ->body("Se procesaron {$batch->processed_rows} registros multidimensionales con éxito.")
                        ->success()
                        ->send();
                }),
            CreateAction::make()
                ->label('Nueva Evaluación (Cuestionario)'),
        ];
    }
}
