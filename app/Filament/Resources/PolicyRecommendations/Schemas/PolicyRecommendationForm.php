<?php

namespace App\Filament\Resources\PolicyRecommendations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PolicyRecommendationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Marco Técnico Ideal de Política Pública')
                    ->description('Recomendaciones técnicas orientadas a mitigar la pobreza independientemente de la coyuntura política.')
                    ->columns(2)
                    ->schema([
                        Select::make('poverty_level_id')
                            ->label('Nivel de Pobreza Objetivo')
                            ->relationship('povertyLevel', 'name')
                            ->required()
                            ->preload(),
                        Select::make('target_segment')
                            ->label('Segmento Poblacional')
                            ->options([
                                'todos' => 'General / Todo el Hogar',
                                'femenina' => 'Pobreza Femenina',
                                'masculina' => 'Pobreza Masculina',
                            ])
                            ->default('todos')
                            ->required(),
                        Select::make('dimension')
                            ->label('Dimensión de Intervención')
                            ->options([
                                'ingreso' => 'Ingreso y Empleo Digno',
                                'alimentacion' => 'Seguridad Alimentaria y Nutrición',
                                'vivienda' => 'Condiciones Físicas de la Vivienda',
                                'servicios' => 'Servicios Básicos Domiciliarios',
                                'salud' => 'Salud y Seguridad Social',
                                'educacion' => 'Rezago Educativo',
                                'genero' => 'Equidad de Género y Sistema de Cuidados',
                            ])
                            ->required(),
                        Select::make('priority')
                            ->label('Nivel de Prioridad Técnica')
                            ->options([
                                'critica' => 'Crítica / Emergencia Vital',
                                'alta' => 'Alta / Estructural',
                                'media' => 'Media / Fortalecimiento',
                                'preventiva' => 'Preventiva / Consolidación',
                            ])
                            ->default('alta')
                            ->required(),
                        TextInput::make('title')
                            ->label('Título de la Política / Programa')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('technical_action')
                            ->label('Acción Técnica Necesaria')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('ideal_framework_rationale')
                            ->label('Justificación del Marco Técnico Ideal')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('expected_impact')
                            ->label('Impacto Esperado en el Índice de Pobreza')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label('Recomendación Activa')
                            ->default(true),
                    ]),
            ]);
    }
}
