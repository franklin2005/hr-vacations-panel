<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected ?string $userPassword = null;
    protected ?string $generatedPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $formState = $this->form->getRawState();
        $this->userPassword = $formState['data']['user_password'] ?? $formState['user_password'] ?? null;

        $this->assertEmailAvailable($data['email']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $employee = $this->record;

        $user = User::where('email', $employee->email)->first();

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
                ->title('User not linked')
                ->warning()
                ->body('This email belongs to an admin account. Use a different email for the employee user.')
                ->send();

            return;
        }

        $password = $this->userPassword ?: $this->generatePassword();

        if (! $user) {
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

        $user->employee_id = $employee->id;
        $user->name = $employee->full_name;
        $user->role = $user->role === 'admin' ? 'admin' : 'employee';
        $user->password = $password;
        $user->save();

        Notification::make()
            ->title('Employee user account updated')
            ->success()
            ->body($this->generatedPassword ? "Existing user linked. Generated password: {$this->generatedPassword}" : 'Existing user linked and password updated.')
            ->send();
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
