<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // refresh user table
        DB::table('users')->truncate();

        // To get existing ID from role and department
        $rolesKeyValue = DB::table("roles")->pluck('id', 'role_name')->toArray();
        $departmentKeyValue = DB::table('departments')->pluck('id', 'department_name')->toArray();

        $users = [
            [
                'user_name' => 'admin',
                'password' => Hash::make('admin'),
                'email' => 'admin@gmail.com',
                'phone_no' => '12345',
                'role_id' => $rolesKeyValue['admin'] ?? null,
                'department_id' => $departmentKeyValue['University Affairs'] ?? null,
                'remark' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_name' => 'manager',
                'password' => Hash::make('manager'),
                'email' => 'manager@gmail.com',
                'phone_no' => '12345',
                'role_id' => $rolesKeyValue['manager'] ?? null,
                'department_id' => $departmentKeyValue['University Affairs'] ?? null,
                'remark' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_name' => 'mathematics coordinator',
                'password' => Hash::make('mathematics coordinator'),
                'email' => 'mathsCoordinator@gmail.com',
                'phone_no' => '12345',
                'role_id' => $rolesKeyValue['coordinator'] ?? null,
                'department_id' => $departmentKeyValue['Mathematics'] ?? null,
                'remark' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_name' => 'cs coordinator',
                'password' => Hash::make('cs coordinator'),
                'email' => 'csCoordinator@gmail.com',
                'phone_no' => '12345',
                'role_id' => $rolesKeyValue['coordinator'] ?? null,
                'department_id' => $departmentKeyValue['Computer Science'] ?? null,
                'remark' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_name' => 'math staff 1',
                'password' => Hash::make('math staff 1'),
                'email' => 'mathStaff1@gmail.com',
                'phone_no' => '12345',
                'role_id' => $rolesKeyValue['staff'] ?? null,
                'department_id' => $departmentKeyValue['Mathematics'] ?? null,
                'remark' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_name' => 'math staff 2',
                'password' => Hash::make('math staff 2'),
                'email' => 'mathStaff2@gmail.com',
                'phone_no' => '12345',
                'role_id' => $rolesKeyValue['staff'] ?? null,
                'department_id' => $departmentKeyValue['Mathematics'] ?? null,
                'remark' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_name' => 'cs staff 1',
                'password' => Hash::make('cs staff 1'),
                'email' => 'csStaff1@gmail.com',
                'phone_no' => '12345',
                'role_id' => $rolesKeyValue['staff'] ?? null,
                'department_id' => $departmentKeyValue['Computer Science'] ?? null,
                'remark' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                [
                    'user_name' => $user['user_name'],
                    'password'=> $user['password'],
                    'phone_no' => $user['phone_no'],
                    'role_id' => $user['role_id'],
                    'department_id' => $user['department_id'],
                    'remark' => $user['remark'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
