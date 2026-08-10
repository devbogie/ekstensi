<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Floor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! User::where('username', 'admin')->exists()) {
            $password = Str::password(20);

            User::create([
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => $password,
            ]);

            $this->command?->info("Akun admin dibuat. Username: admin | Password: {$password}");
        }

        $departments = ['IT & Telekomunikasi', 'Finance & Accounting', 'HRD & General Affair'];

        foreach ($departments as $name) {
            Department::firstOrCreate(['name' => $name]);
        }

        $floors = ['Lantai 1', 'Lantai 2', 'Lantai 3'];

        foreach ($floors as $name) {
            Floor::firstOrCreate(['name' => $name]);
        }

        $branches = ['Head Office', 'Cabang Surabaya'];

        foreach ($branches as $name) {
            Branch::firstOrCreate(['name' => $name]);
        }
    }
}
