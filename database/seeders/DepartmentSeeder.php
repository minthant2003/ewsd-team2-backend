<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['department_name' => 'Computer Science', 'remark' => null],
            ['department_name' => 'Mathematics', 'remark' => null],
            ['department_name' => 'Marketing', 'remark' => null],
            ['department_name' => 'Economics', 'remark' => null],
            ['department_name' => 'Architecture', 'remark' => null],
            ['department_name' => 'University Affairs', 'remark' => null],
        ];
        foreach ($departments as $department) {
            DB::table('departments')->updateOrInsert(
                ['department_name' => $department['department_name']],
                [
                    'remark' => $department['remark'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
