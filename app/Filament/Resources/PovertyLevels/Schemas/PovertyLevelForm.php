<?php

namespace App\Filament\Resources\PovertyLevels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PovertyLevelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Definición del Nivel Progresivo (Escala Tipo IMC 0–100)')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->label('Identificador (Slug)')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->label('Nombre Oficial del Nivel')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('bmi_analogy_label')
                            ->label('Etiqueta Analógica (Tipo IMC)')
                            ->required(),
                        TextInput::make('min_score')
                            ->label('Puntaje Mínimo (IP)')
                            ->numeric()
                            ->required(),
                        TextInput::make('max_score')
                            ->label('Puntaje Máximo (IP)')
                            ->numeric()
                            ->required(),
                        Select::make('color')
                            ->label('Color Semántico')
                            ->options([
                                'success' => 'Verde (Muy Baja)',
                                'info' => 'Azul (Baja)',
                                'warning' => 'Ámbar (Moderada)',
                                'danger' => 'Rojo (Alta / Muy Alta)',
                            ])
                            ->required(),
                        TextInput::make('badge_hex')
                            ->label('Color Hexadecimal')
                            ->default('#0369a1')
                            ->required(),
                        TextInput::make('display_order')
                            ->label('Orden Progresivo')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Textarea::make('description')
                            ->label('Explicación del Nivel')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('implications')
                            ->label('Implicaciones Socioeconómicas para el Individuo o Grupo')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
