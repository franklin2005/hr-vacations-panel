<?php

namespace App\Filament\Employee\Resources\VacationRequests\Pages;

use App\Filament\Employee\Resources\VacationRequests\VacationRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVacationRequests extends ListRecords
{
    protected static string $resource = VacationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
