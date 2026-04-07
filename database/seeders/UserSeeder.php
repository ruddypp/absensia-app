<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@absensi.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('super_admin');

        // HRD
        $hrDept = Department::where('code', 'HR')->first();
        $hrPos = Position::where('name', 'Manager HR')->first();

        $hrdUser = User::firstOrCreate(
            ['email' => 'hrd@absensi.local'],
            ['name' => 'Budi HRD', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $hrdUser->assignRole('hrd');

        Employee::firstOrCreate(
            ['email' => 'hrd@absensi.local'],
            [
                'user_id' => $hrdUser->id,
                'department_id' => $hrDept?->id ?? 1,
                'position_id' => $hrPos?->id ?? 1,
                'employee_code' => 'EMP001',
                'name' => 'Budi HRD',
                'phone' => '08100000001',
                'join_date' => '2022-01-01',
                'status' => 'active',
                'gender' => 'male',
            ]
        );

        // Kepala Departemen IT
        $itDept = Department::where('code', 'IT')->first();
        $mgrPos = Position::where('name', 'Manager IT')->first();

        $kepalaUser = User::firstOrCreate(
            ['email' => 'kepala.it@absensi.local'],
            ['name' => 'Siti Kepala IT', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $kepalaUser->assignRole('kepala_departemen');

        Employee::firstOrCreate(
            ['email' => 'kepala.it@absensi.local'],
            [
                'user_id' => $kepalaUser->id,
                'department_id' => $itDept?->id ?? 2,
                'position_id' => $mgrPos?->id ?? 3,
                'employee_code' => 'EMP002',
                'name' => 'Siti Kepala IT',
                'phone' => '08100000002',
                'join_date' => '2022-03-01',
                'status' => 'active',
                'gender' => 'female',
            ]
        );

        // Karyawan biasa
        $devPos = Position::where('name', 'Junior Developer')->first();

        $karyawanUser = User::firstOrCreate(
            ['email' => 'karyawan@absensi.local'],
            ['name' => 'Andi Karyawan', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $karyawanUser->assignRole('karyawan');

        Employee::firstOrCreate(
            ['email' => 'karyawan@absensi.local'],
            [
                'user_id' => $karyawanUser->id,
                'department_id' => $itDept?->id ?? 2,
                'position_id' => $devPos?->id ?? 5,
                'employee_code' => 'EMP003',
                'name' => 'Andi Karyawan',
                'phone' => '08100000003',
                'join_date' => '2023-06-01',
                'status' => 'active',
                'gender' => 'male',
            ]
        );
    }
}
