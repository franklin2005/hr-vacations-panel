<?php

namespace Database\Seeders;

use App\Models\VacationRequest;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class VacationRequestSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        foreach (range(1, 15) as $i) {
            $start = fake()->dateTimeBetween('-1 month', '+1 month');
            $end   = (clone $start)->modify('+'.rand(2, 10).' days');

            VacationRequest::create([
                'employee_id' => $employees->random()->id,
                'start_date'  => $start,
                'end_date'    => $end,
                'status'      => fake()->randomElement(['pending', 'approved', 'rejected']),
                'reason'      => fake()->sentence(),
                'reviewed_by' => null, // se rellenará cuando apruebes/ rechaces
                'reviewed_at' => null,
            ]);
        }
    }
}
