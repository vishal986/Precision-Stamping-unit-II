<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\LeaveType::insert([
            ['name' => 'Casual Leave', 'code' => 'CL', 'default_days' => 12, 'is_paid' => true, 'description' => 'For unexpected/casual needs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sick Leave', 'code' => 'SL', 'default_days' => 12, 'is_paid' => true, 'description' => 'For medical reasons', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Earned Leave', 'code' => 'EL', 'default_days' => 15, 'is_paid' => true, 'description' => 'Privilege leaves based on attendance', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Leave Without Pay', 'code' => 'LWP', 'default_days' => 0, 'is_paid' => false, 'description' => 'Unpaid leave', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
