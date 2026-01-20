<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\VacationRequest;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HrStatsOverview extends BaseWidget
{
    protected ?string $heading = 'HR Overview';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();

        $pendingStartingToday = VacationRequest::query()
            ->where('status', 'pending')
            ->whereDate('start_date', $today)
            ->count();

        $activeEmployees = Employee::query()
            ->where('is_active', true)
            ->count();

        $startOfWeek = Carbon::now()->startOfWeek(); // lunes por defecto
        $endOfWeek = Carbon::now()->endOfWeek();

        // “Vacaciones activas esta semana” = solicitudes aprobadas que se solapan con la semana
        $approvedOverlappingThisWeek = VacationRequest::query()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $endOfWeek)
            ->whereDate('end_date', '>=', $startOfWeek)
            ->count();

        return [
            Stat::make('Pending starting today', $pendingStartingToday)
                ->description('Requests with start date = today')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Active employees', $activeEmployees)
                ->description('Employees marked as active')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Approved vacations this week', $approvedOverlappingThisWeek)
                ->description('Approved requests overlapping this week')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
