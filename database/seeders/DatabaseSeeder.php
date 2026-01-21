<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $this->call([
        DepartmentSeeder::class,
        EmployeeSeeder::class,
        VacationRequestSeeder::class,
    ]);
        $department = Department::first() ?? Department::create(['name' => 'IT']);

        $employee = Employee::updateOrCreate(
            ['email' => 'employee@test.com'],
            [
                'full_name'     => 'John Employee',
                'department_id' => $department->id,
                'hire_date'     => now()->subYears(2),
                'is_active'     => true,
            ]
        );

        // 3) Usuario ADMIN
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'        => 'Admin User',
                'password'    => Hash::make('password'),
                'role'        => 'admin',
                'employee_id' => null,
            ]
        );

        // 4) Usuario EMPLOYEE (vinculado al employee arriba)
        User::updateOrCreate(
            ['email' => 'employee@test.com'],
            [
                'name'        => 'Employee User',
                'password'    => Hash::make('password'),
                'role'        => 'employee',
                'employee_id' => $employee->id,
            ]
        );
}
}