<?php

namespace App\Filament\Resources\VacationRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VacationRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('employee_id')
                ->label('Employee')
                ->relationship('employee', 'full_name')
                ->searchable()
                ->preload()
                ->required(),

            DatePicker::make('start_date')
                ->label('Start date')
                ->native(false)
                ->required(),

            DatePicker::make('end_date')
                ->label('End date')
                ->native(false)
                ->required()
                ->rule(function (callable $get) {
                    $start = $get('start_date');
                    if (! $start) return null;

                    return 'after_or_equal:' . $start;
                }),

            Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->default('pending')
                ->required()
                ->disabled()   // se cambia con Approve/Reject
                ->dehydrated(), // se guarda aunque esté disabled

            Textarea::make('reason')
                ->label('Reason (optional)')
                ->rows(4)
                ->maxLength(1000)
                ->nullable(),
        ]);
    }
}
