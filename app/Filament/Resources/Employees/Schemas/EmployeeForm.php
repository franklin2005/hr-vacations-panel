<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('full_name')
                ->label('Full name')
                ->required()
                ->maxLength(255)
                ->autofocus(),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            Select::make('department_id')
                ->label('Department')
                ->relationship('department', 'name')
                ->searchable()
                ->preload()
                ->required(),

            DatePicker::make('hire_date')
                ->label('Hire date')
                ->native(false)
                ->maxDate(now())
                ->nullable(),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }
}
