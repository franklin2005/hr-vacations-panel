<?php

namespace App\Filament\Resources\VacationRequests\Schemas;

use App\Models\Employee;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
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
                ->live()        // actualiza dinámicamente el texto
                ->required(),

            Placeholder::make('remaining_days')
                ->label('Vacation days remaining')
                ->content(function (Get $get, $record) {

                    $employeeId = $get('employee_id');
                    $start = $get('start_date');

                    if (! $employeeId) {
                        return 'Select an employee to see remaining days.';
                    }

                    $employee = Employee::find($employeeId);
                    if (! $employee) {
                        return 'Employee not found.';
                    }

                    $year = $start
                        ? Carbon::parse($start)->year
                        : now()->year;

                    // Si se está editando, ignoramos este mismo request en el cálculo
                    $excludeId = $record?->getKey();

                    $remaining = $employee->remainingVacationDays($year, 30, $excludeId);

                    return "{$remaining} days remaining for {$year}.";
                }),

            DatePicker::make('start_date')
                ->label('Start date')
                ->native(false)
                ->live()        // actualiza el texto al cambiar fecha
                ->required(),

            DatePicker::make('end_date')
                ->label('End date')
                ->native(false)
                ->required()
                ->rule(fn (callable $get) =>
                    $get('start_date') ? 'after_or_equal:' . $get('start_date') : null
                )

                // Validación no exceder los días disponibles
                ->rule(function (callable $get, $record) {
                    return function (string $attribute, $value, \Closure $fail) use ($get, $record) {

                        $employeeId = $get('employee_id');
                        $start = $get('start_date');
                        $end = $value;

                        if (! $employeeId || ! $start || ! $end) {
                            return;
                        }

                        $startC = Carbon::parse($start);
                        $endC   = Carbon::parse($end);

                        if ($endC->lt($startC)) {
                            return;
                        }

                        $requestedDays = $startC->diffInDays($endC) + 1;
                        $year = $startC->year;

                        $employee = Employee::find($employeeId);
                        if (! $employee) {
                            return;
                        }
                        // Evitar doble conteo al editar
                        $excludeId = $record?->getKey();

                        if (method_exists($employee, 'remainingVacationDays')) {
                            $remaining = $employee->remainingVacationDays($year, 30, $excludeId);

                            if ($requestedDays > $remaining) {
                                $fail("Not enough vacation days. Requested: {$requestedDays}. Remaining for {$year}: {$remaining}.");
                            }
                        }
                    };
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
                ->disabled()
                ->dehydrated(),

            Textarea::make('reason')
                ->label('Reason (optional)')
                ->rows(4)
                ->maxLength(1000)
                ->nullable(),
        ]);
    }
}
