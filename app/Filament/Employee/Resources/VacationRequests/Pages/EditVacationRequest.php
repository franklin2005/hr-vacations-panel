<?php

namespace App\Filament\Employee\Resources\VacationRequests\Pages;

use App\Filament\Employee\Resources\VacationRequests\VacationRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;

class EditVacationRequest extends EditRecord
{
    protected static string $resource = VacationRequestResource::class;

    protected function afterFill(): void
    {
        if (! $this->record) {
            return;
        }

        if ($this->record->status !== 'pending') {
            Notification::make()
                ->title('Request is read-only')
                ->warning()
                ->body('Only pending requests can be edited or deleted.')
                ->send();
        }
    }

    protected function beforeSave(): void
    {
        if ($this->record->status === 'pending') {
            return;
        }

        Notification::make()
            ->title('Cannot update request')
            ->warning()
            ->body('Only pending requests can be edited.')
            ->send();

        throw new Halt();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn ($record) => $record->status === 'pending'),
        ];
    }
}
