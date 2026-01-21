<?php

namespace App\Filament\Employee\Pages;

use App\Models\VacationRequest;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class MyRequests extends Page
{
    protected static ?string $navigationLabel = 'My Requests';
    protected static ?string $title = 'My Vacation Requests';
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedListBullet;
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.employee.pages.my-requests';

    /**
     * Devuelve solo las solicitudes del empleado autenticado
     */
    public function getRequests()
    {
        $employeeId = auth()->user()->employee_id;

        if (! $employeeId) {
            return collect();
        }

        return VacationRequest::query()
            ->where('employee_id', $employeeId)
            ->orderByDesc('start_date')
            ->get();
    }
}
