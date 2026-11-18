<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identidad y Credenciales')
                    ->description('Configure los datos del usuario, su rol en el sistema y su institución de adscripción.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('role')
                            ->label('Rol del Sistema')
                            ->options(User::roleLabels())
                            ->required()
                            ->default(User::ROLE_INDIVIDUAL),
                        TextInput::make('institution')
                            ->label('Institución / Organización')
                            ->placeholder('Ej. INEGI, CONEVAL, UNAM o Uso Personal')
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Teléfono de Contacto')
                            ->maxLength(50),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8),
                        Toggle::make('is_active')
                            ->label('Acceso Activo a la Plataforma')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
