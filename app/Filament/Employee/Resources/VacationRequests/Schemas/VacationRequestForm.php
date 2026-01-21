<?php

namespace App\Filament\Employee\Resources\VacationRequests\Schemas;

use App\Models\Employee;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class VacationRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('employee_id')
                ->default(fn () => auth()->user()?->employee_id)
                ->dehydrated()
                ->required(),

            Placeholder::make('remaining_days')
                ->label('Vacation days remaining')
                ->content(function (Get $get, $record) {
                    /** @var Employee|null $employee */
                    $employee = auth()->user()?->employee;

                    if (! $employee) {
                        return 'No employee linked to this account.';
                    }

                    $start = $get('start_date');
                    $year = $start
                        ? Carbon::parse($start)->year
                        : now()->year;

                    $excludeId = $record?->getKey();

                    $remaining = $employee->remainingVacationDays($year, 30, $excludeId);

                    return "{$remaining} days remaining for {$year}.";
                }),

            Placeholder::make('requested_days')
                ->label('Requested days')
                ->content(function (Get $get) {
                    $start = $get('start_date');
                    $end = $get('end_date');

                    if (! $start || ! $end) {
                        return 'Select start and end dates.';
                    }

                    $startC = Carbon::parse($start);
                    $endC = Carbon::parse($end);

                    if ($endC->lt($startC)) {
                        return 'End date must be after or equal to start date.';
                    }

                    $days = $startC->diffInDays($endC) + 1;

                    return "{$days} day(s).";
                }),

            DatePicker::make('start_date')
                ->label('Start date')
                ->native(false)
                ->live()
                ->required()
                ->disabled(fn ($record) => $record && $record->status !== 'pending'),

            DatePicker::make('end_date')
                ->label('End date')
                ->native(false)
                ->live()
                ->required()
                ->rule(fn (callable $get) =>
                    $get('start_date') ? 'after_or_equal:' . $get('start_date') : null
                )
                ->rule(function (callable $get, $record) {
                    return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                        /** @var Employee|null $employee */
                        $employee = auth()->user()?->employee;

                        $start = $get('start_date');
                        $end = $value;

                        if (! $employee || ! $start || ! $end) {
                            return;
                        }

                        $startC = Carbon::parse($start);
                        $endC = Carbon::parse($end);

                        if ($endC->lt($startC)) {
                            return;
                        }

                        $requestedDays = $startC->diffInDays($endC) + 1;
                        $year = $startC->year;
                        $excludeId = $record?->getKey();

                        $remaining = $employee->remainingVacationDays($year, 30, $excludeId);

                        if ($requestedDays > $remaining) {
                            $fail("Not enough vacation days. Requested: {$requestedDays}. Remaining for {$year}: {$remaining}.");
                        }
                    };
                })
                ->disabled(fn ($record) => $record && $record->status !== 'pending'),

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
                ->nullable()
                ->disabled(fn ($record) => $record && $record->status !== 'pending'),
        ]);
    }
}
