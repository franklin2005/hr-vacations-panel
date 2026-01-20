<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\VacationRequest;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HrStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'HR Overview';

    // Opcional: orden en el dashboard
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();

        // 1) Solicitudes pendientes que empiezan hoy
        $pendingStartingToday = VacationRequest::query()
            ->where('status', 'pending')
            ->whereDate('start_date', $today)
            ->count();

        // 2) Empleados activos
        $activeEmployees = Employee::query()
            ->where('is_active', true)
            ->count();

        // 3) Vacaciones aprobadas activas esta semana (solapadas con la semana actual)
        $startOfWeek = Carbon::now()->startOfWeek(); // lunes
        $endOfWeek = Carbon::now()->endOfWeek();     // domingo

        $approvedOverlappingThisWeek = VacationRequest::query()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $endOfWeek)
            ->whereDate('end_date', '>=', $startOfWeek)
            ->count();

        return [
            Stat::make('Pending today', $pendingStartingToday)
                ->description('Requests starting today')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Active employees', $activeEmployees)
                ->description('Currently active staff')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Vacations this week', $approvedOverlappingThisWeek)
                ->description('Approved vacations overlapping this week')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
