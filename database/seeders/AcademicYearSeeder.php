<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // Current Academic Year
        AcademicYear::create([
            'academic_name' => '2023-2024',
            'start_date' => '2023-09-01',
            'end_date' => '2024-08-31',
            'closure_date' => '2024-06-30',
            'final_closure_date' => '2024-07-31',
            'remark' => 'Current academic year'
        ]);

        // Next Academic Year
        AcademicYear::create([
            'academic_name' => '2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-08-31',
            'closure_date' => '2025-06-30',
            'final_closure_date' => '2025-07-31',
            'remark' => 'Next academic year'
        ]);

        // Previous Academic Year
        AcademicYear::create([
            'academic_name' => '2022-2023',
            'start_date' => '2022-09-01',
            'end_date' => '2023-08-31',
            'closure_date' => '2023-06-30',
            'final_closure_date' => '2023-07-31',
            'remark' => 'Previous academic year'
        ]);
    }
} 