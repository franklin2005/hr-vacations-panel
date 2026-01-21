<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
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
                ->unique(ignoreRecord: true)
                ->rule(function ($record) {
                    $employeeId = $record?->id;

                    return function (string $attribute, $value, $fail) use ($employeeId) {
                        $adminConflict = User::where('email', $value)
                            ->where('role', 'admin')
                            ->exists();

                        if ($adminConflict) {
                            $fail('This email is already used by an admin user. Please use a different email.');
                            return;
                        }

                        $otherEmployeeUser = User::where('email', $value)
                            ->where('role', '!=', 'admin')
                            ->whereNotNull('employee_id')
                            ->when($employeeId, fn ($query) => $query->where('employee_id', '!=', $employeeId))
                            ->exists();

                        if ($otherEmployeeUser) {
                            $fail('This email is already linked to another employee account.');
                        }
                    };
                }),

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

            Section::make('Account')
                ->columns(2)
                ->schema([
                    TextInput::make('user_password')
                        ->label('New password')
                        ->password()
                        ->revealable()
                        ->minLength(8)
                        ->helperText('Leave blank to auto-generate.')
                        ->dehydrated(false),

                    TextInput::make('user_password_confirmation')
                        ->label('Confirm password')
                        ->password()
                        ->revealable()
                        ->same('user_password')
                        ->dehydrated(false),
                ]),
        ]);
    }
}
