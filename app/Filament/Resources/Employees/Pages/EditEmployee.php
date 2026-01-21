<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected ?string $userPassword = null;
    protected ?string $generatedPassword = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $formState = $this->form->getRawState();
        $this->userPassword = $formState['data']['user_password'] ?? $formState['user_password'] ?? null;

        $this->assertEmailAvailable($data['email']);

        return $data;
    }

    protected function afterSave(): void
    {
        $employee = $this->record;

        $user = User::where('employee_id', $employee->id)->first();

        if (! $user) {
            $user = User::where('email', $employee->email)->first();
        }

        if ($user && $user->employee_id && $user->employee_id !== $employee->id) {
            Notification::make()
                ->title('User not linked')
                ->warning()
                ->body('This email is already linked to another employee.')
                ->send();

            return;
        }

        if ($user && $user->role === 'admin') {
            Notification::make()
                ->title('User not updated')
                ->warning()
                ->body('Email belongs to an admin account. User details were not changed.')
                ->send();

            return;
        }

        if (! $user) {
            $password = $this->userPassword ?: $this->generatePassword();

            $user = new User();
            $user->email = $employee->email;
            $user->name = $employee->full_name;
            $user->role = 'employee';
            $user->employee_id = $employee->id;
            $user->password = $password;
            $user->save();

            Notification::make()
                ->title('Employee user account created')
                ->success()
                ->body($this->generatedPassword ? "Generated password: {$this->generatedPassword}" : 'User account linked.')
                ->send();

            return;
        }

        $user->name = $employee->full_name;
        $user->email = $employee->email;
        $user->employee_id = $employee->id;
        $user->role = 'employee';

        $passwordChanged = false;

        if ($this->userPassword) {
            $user->password = $this->userPassword;
            $passwordChanged = true;
        }

        $user->save();

        $body = 'User account synced.';

        if ($passwordChanged) {
            $body .= ' Password updated.';
        }

        Notification::make()
            ->title('Employee user account updated')
            ->success()
            ->body($body)
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function assertEmailAvailable(string $email): void
    {
        $adminConflict = User::where('email', $email)
            ->where('role', 'admin')
            ->exists();

        if ($adminConflict) {
            throw ValidationException::withMessages([
                'email' => 'This email is already used by an admin user. Please use a different email.',
            ]);
        }

        $linkedToOtherEmployee = User::where('email', $email)
            ->where('role', '!=', 'admin')
            ->whereNotNull('employee_id')
            ->where('employee_id', '!=', $this->record->id)
            ->exists();

        if ($linkedToOtherEmployee) {
            throw ValidationException::withMessages([
                'email' => 'This email is already linked to another employee.',
            ]);
        }
    }

    protected function generatePassword(): string
    {
        $this->generatedPassword = Str::random(12);

        return $this->generatedPassword;
    }
}
