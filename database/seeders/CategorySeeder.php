<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Academic Improvement',
                'remark' => 'Ideas for enhancing academic programs and teaching methods',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Campus Facilities',
                'remark' => 'Suggestions for improving campus infrastructure and facilities',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Student Services',
                'remark' => 'Ideas related to student support and services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Research & Innovation',
                'remark' => 'Proposals for research initiatives and innovative projects',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Student Life',
                'remark' => 'Ideas for improving student activities and campus life',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Technology',
                'remark' => 'Suggestions for technological improvements and digital solutions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Sustainability',
                'remark' => 'Ideas for environmental and sustainable campus initiatives',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Community Engagement',
                'remark' => 'Proposals for university-community collaboration and outreach',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('categories')->insert($categories);
    }
} 