<?php

namespace App\Filament\Resources\PovertyEvaluations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PovertyEvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('1. Hogar, Año de Evaluación y Fuente')
                    ->columns(3)
                    ->schema([
                        Select::make('household_id')
                            ->label('Hogar / Sujeto de Estudio')
                            ->relationship('household', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('year')
                            ->label('Año Evaluado')
                            ->numeric()
                            ->minValue(1990)
                            ->maxValue(2100)
                            ->default((int) date('Y'))
                            ->required(),
                        Select::make('evaluation_source')
                            ->label('Origen de los Datos')
                            ->options([
                                'cuestionario' => 'Cuestionario Integrado Directo',
                                'importacion_enigh' => 'Estudio Oficial / Encuesta ENIGH',
                            ])
                            ->default('cuestionario')
                            ->required(),
                    ]),
                Section::make('2. Ingresos, Gastos y Segmentación por Género (MXN Mensuales)')
                    ->description('El motor calcula automáticamente la relación ingreso/gasto y la brecha entre pobreza general, femenina y masculina.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('monthly_income_female')
                            ->label('Ingreso Mensual Femenino ($)')
                            ->numeric()
                            ->minValue(0)
                            ->default(5200)
                            ->required(),
                        TextInput::make('monthly_income_male')
                            ->label('Ingreso Mensual Masculino ($)')
                            ->numeric()
                            ->minValue(0)
                            ->default(6800)
                            ->required(),
                        TextInput::make('monthly_income_total')
                            ->label('Ingreso Mensual Total del Hogar ($)')
                            ->helperText('Si se deja en 0 se suma el ingreso femenino + masculino.')
                            ->numeric()
                            ->minValue(0)
                            ->default(12000),
                        TextInput::make('monthly_expense_food')
                            ->label('Gasto Mensual en Alimentación ($)')
                            ->numeric()
                            ->minValue(0)
                            ->default(5100)
                            ->required(),
                        TextInput::make('monthly_expense_housing_services')
                            ->label('Gasto en Vivienda, Agua, Luz y Transporte ($)')
                            ->numeric()
                            ->minValue(0)
                            ->default(3400)
                            ->required(),
                        TextInput::make('monthly_expense_health_education')
                            ->label('Gasto en Salud, Educación y Vestido ($)')
                            ->numeric()
                            ->minValue(0)
                            ->default(1800)
                            ->required(),
                        TextInput::make('female_unpaid_care_hours_weekly')
                            ->label('Horas Semanales de Cuidado No Remunerado (Mujeres)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(110)
                            ->default(32)
                            ->required(),
                        TextInput::make('male_unpaid_care_hours_weekly')
                            ->label('Horas Semanales de Cuidado No Remunerado (Hombres)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(110)
                            ->default(10)
                            ->required(),
                    ]),
                Section::make('3. Condiciones Físicas de la Vivienda y Carencias Multidimensionales (0 = Sin carencia, 100 = Carencia Crítica)')
                    ->columns(3)
                    ->schema([
                        TextInput::make('housing_floor_wall_deprivation')
                            ->label('Material de Pisos, Techos y Muros (0-100)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(25)
                            ->required(),
                        TextInput::make('housing_overcrowding_deprivation')
                            ->label('Hacinamiento en la Vivienda (0-100)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(30)
                            ->required(),
                        TextInput::make('basic_services_deprivation')
                            ->label('Carencia de Servicios Básicos: Agua, Drenaje, Luz (0-100)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(20)
                            ->required(),
                        TextInput::make('education_lag_deprivation')
                            ->label('Rezago Educativo en el Hogar (0-100)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(25)
                            ->required(),
                        TextInput::make('health_security_deprivation')
                            ->label('Carencia de Salud y Seguridad Social (0-100)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(40)
                            ->required(),
                        TextInput::make('food_insecurity_deprivation')
                            ->label('Inseguridad Alimentaria (0-100)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(30)
                            ->required(),
                    ]),
            ]);
    }
}
