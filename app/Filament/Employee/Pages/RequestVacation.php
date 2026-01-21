<?php

namespace App\Filament\Employee\Pages;

use App\Models\Employee;
use App\Models\VacationRequest;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class RequestVacation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Request Vacation';
    protected static ?string $title = 'Request Vacation';
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.employee.pages.request-vacation';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => null,
            'end_date' => null,
            'reason' => null,
        ]);
    }

    public function form(Schema $schema): Schema

    {
        return $schema->statePath('data')
            ->components([
                Placeholder::make('employee')
                    ->label('Employee')
                    ->content(fn () => auth()->user()->employee?->full_name ?? 'Unknown'),

                Placeholder::make('remaining_days')
                    ->label('Vacation days remaining')
                    ->content(function (callable $get) {
                        $employee = auth()->user()->employee;

                        if (! $employee) {
                            return 'No employee linked to this account.';
                        }

                        $start = $get('start_date');
                        $year = $start ? Carbon::parse($start)->year : now()->year;

                        $remaining = $employee->remainingVacationDays($year, 30);

                        return "{$remaining} days remaining for {$year}.";
                    }),

                Placeholder::make('requested_days')
                    ->label('Requested days')
                    ->content(function (callable $get) {
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
                    ->required(),

                DatePicker::make('end_date')
                    ->label('End date')
                    ->native(false)
                    ->live()
                    ->required()
                    ->rule(fn (callable $get) => $get('start_date') ? 'after_or_equal:' . $get('start_date') : null),

                Textarea::make('reason')
                    ->label('Reason (optional)')
                    ->rows(4)
                    ->maxLength(1000)
                    ->nullable(),
            ]);
    }

    public function submit(): void
    {
        /** @var Employee|null $employee */
        $employee = auth()->user()->employee;

        if (! $employee) {
            Notification::make()
                ->title('Account not linked to an employee')
                ->danger()
                ->send();
            return;
        }

        $start = $this->data['start_date'] ?? null;
        $end = $this->data['end_date'] ?? null;

        if (! $start || ! $end) {
            Notification::make()
                ->title('Please select start and end dates')
                ->warning()
                ->send();
            return;
        }

        $startC = Carbon::parse($start);
        $endC = Carbon::parse($end);

        if ($endC->lt($startC)) {
            Notification::make()
                ->title('Invalid date range')
                ->body('End date must be after or equal to start date.')
                ->danger()
                ->send();
            return;
        }

        $requestedDays = $startC->diffInDays($endC) + 1;
        $year = $startC->year;

        $remaining = $employee->remainingVacationDays($year, 30);

        if ($requestedDays > $remaining) {
            Notification::make()
                ->title('Not enough vacation days')
                ->body("Requested: {$requestedDays}. Remaining for {$year}: {$remaining}.")
                ->danger()
                ->send();
            return;
        }

        VacationRequest::create([
            'employee_id' => $employee->id,
            'start_date' => $startC,
            'end_date' => $endC,
            'status' => 'pending',
            'reason' => $this->data['reason'] ?? null,
        ]);

        $this->form->fill([
            'start_date' => null,
            'end_date' => null,
            'reason' => null,
        ]);

        Notification::make()
            ->title('Vacation request submitted')
            ->success()
            ->send();
    }
}
