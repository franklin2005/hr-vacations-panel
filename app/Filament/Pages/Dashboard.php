<?php

namespace App\Filament\Pages;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use App\Filament\Widgets\EmployeesByDepartmentChart;
use App\Filament\Widgets\HrStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedHome;
    // 3 columnas en desktop
    protected int|string|array $columns = [
        'default' => 1,
        'lg' => 3,
    ];

    public function getWidgets(): array
    {
        return [
            HrStatsOverview::class,
            EmployeesByDepartmentChart::class,
        ];
    }
}
