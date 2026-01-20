<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Human Resources',
            'Engineering',
            'Marketing',
            'Finance',
            'Customer Support',
        ];

        foreach ($departments as $name) {
            Department::create([
                'name' => $name,
            ]);
        }
    }
}
