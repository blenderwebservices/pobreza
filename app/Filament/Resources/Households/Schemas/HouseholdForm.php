<?php

namespace App\Filament\Resources\Households\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HouseholdForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificación del Hogar, Individuo o Grupo')
                    ->columns(3)
                    ->schema([
                        TextInput::make('folio_code')
                            ->label('Código / Folio Oficial')
                            ->required()
                            ->default(fn () => 'HOG-' . strtoupper(substr(md5((string) microtime(true)), 0, 6)))
                            ->unique(ignoreRecord: true)
                            ->maxLength(64),
                        TextInput::make('name')
                            ->label('Nombre del Hogar, Individuo o Comunidad')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Select::make('subject_type')
                            ->label('Tipo de Sujeto')
                            ->options([
                                'individuo' => 'Individuo Unipersonal',
                                'hogar' => 'Hogar Familiar',
                                'grupo_comunitario' => 'Grupo / Cohorte Comunitaria',
                            ])
                            ->required()
                            ->default('hogar'),
                        TextInput::make('state')
                            ->label('Entidad Federativa / Estado')
                            ->required()
                            ->default('Ciudad de México'),
                        TextInput::make('municipality')
                            ->label('Municipio / Demarcación')
                            ->required()
                            ->default('Iztapalapa'),
                        Select::make('zone_type')
                            ->label('Ámbito Geográfico')
                            ->options([
                                'urbana' => 'Urbana (Línea Bienestar: $4,560/mes)',
                                'rural' => 'Rural (Línea Bienestar: $3,290/mes)',
                            ])
                            ->required()
                            ->default('urbana'),
                        Select::make('user_id')
                            ->label('Usuario Responsable')
                            ->relationship('user', 'name')
                            ->default(fn () => auth()->id())
                            ->searchable()
                            ->preload(),
                    ]),
                Section::make('Composición Demográfica y Segmentación de Género')
                    ->columns(4)
                    ->schema([
                        TextInput::make('total_members')
                            ->label('Total Integrantes')
                            ->numeric()
                            ->minValue(1)
                            ->default(4)
                            ->required(),
                        TextInput::make('female_members')
                            ->label('Integrantes Mujeres')
                            ->numeric()
                            ->minValue(0)
                            ->default(2)
                            ->required(),
                        TextInput::make('male_members')
                            ->label('Integrantes Hombres')
                            ->numeric()
                            ->minValue(0)
                            ->default(2)
                            ->required(),
                        TextInput::make('dependent_children')
                            ->label('Menores o Dependientes')
                            ->numeric()
                            ->minValue(0)
                            ->default(1)
                            ->required(),
                        Toggle::make('female_headed')
                            ->label('Hogar con Jefatura Económica o de Cuidados Femenina')
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Observaciones del Caso / Contexto Longitudinal')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
