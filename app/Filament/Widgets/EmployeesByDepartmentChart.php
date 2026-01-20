<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use Filament\Widgets\ChartWidget;

class EmployeesByDepartmentChart extends ChartWidget
{
    protected ?string $heading = 'Employees by department';

    protected static ?int $sort = 2;
    //tamaño del widget en el dashboard
    protected int|string|array $columnSpan = [
    'default' => 1,
    'md' => 2,
    'lg' => 2,

];
//  En Filament es 5 max height
    protected ?string $maxHeight = '420px';

    // Chart.js: controla el alto visual
    protected ?array $options = [
        'maintainAspectRatio' => true,
        'aspectRatio' => 2, 
    ];


    protected function getData(): array
    {
        $rows = Department::query()
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Employees',
                    'data' => $rows->pluck('employees_count')->all(),
                ],
            ],
            'labels' => $rows->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
