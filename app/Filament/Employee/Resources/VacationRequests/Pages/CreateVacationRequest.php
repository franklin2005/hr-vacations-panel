<?php

namespace App\Filament\Employee\Resources\VacationRequests\Pages;

use App\Filament\Employee\Resources\VacationRequests\VacationRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVacationRequest extends CreateRecord
{
    protected static string $resource = VacationRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
