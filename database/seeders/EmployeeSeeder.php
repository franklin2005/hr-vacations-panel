<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();

        foreach (range(1, 10) as $i) {
            Employee::create([
                'full_name'     => fake()->name(),
                'email'         => fake()->unique()->safeEmail(),
                'hire_date'     => fake()->date(),
                'is_active'     => fake()->boolean(90),
                'department_id' => $departments->random()->id,
            ]);
        }
    }
}
