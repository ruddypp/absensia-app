<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminUser = User::updateOrCreate(
            ['email' => 'ruddy@absensi.app'],
            [
                'name' => 'Ruddy Paninggalan',
                'password' => 'ganteng123',
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->syncRoles(['super_admin']);

        $hrDept = Department::where('code', 'HR')->first();
        $hrPos = Position::where('name', 'Manager HR')->first();

        $hrdUser = User::updateOrCreate(
            ['email' => 'sabina@absensi.app'],
            [
                'name' => 'Sabina Panjahitan',
                'password' => 'ganteng123',
                'email_verified_at' => now(),
            ]
        );
        $hrdUser->syncRoles(['hrd']);

        Employee::updateOrCreate(
            ['employee_code' => 'EMP001'],
            [
                'user_id' => $hrdUser->id,
                'department_id' => $hrDept?->id ?? 1,
                'position_id' => $hrPos?->id ?? 1,
                'employee_code' => 'EMP001',
                'email' => 'sabina@absensi.app',
                'name' => 'Sabina Panjahitan',
                'phone' => '08100000001',
                'join_date' => '2022-01-01',
                'status' => 'active',
                'gender' => 'female',
                'address' => 'Medan, Indonesia',
            ]
        );

        $itDept = Department::where('code', 'IT')->first();
        $mgrPos = Position::where('name', 'Manager IT')->first();

        $kepalaUser = User::updateOrCreate(
            ['email' => 'dimas@absensi.app'],
            [
                'name' => 'Dimas Pratama',
                'password' => 'ganteng123',
                'email_verified_at' => now(),
            ]
        );
        $kepalaUser->syncRoles(['kepala_departemen']);

        Employee::updateOrCreate(
            ['employee_code' => 'EMP002'],
            [
                'user_id' => $kepalaUser->id,
                'department_id' => $itDept?->id ?? 2,
                'position_id' => $mgrPos?->id ?? 3,
                'employee_code' => 'EMP002',
                'email' => 'dimas@absensi.app',
                'name' => 'Dimas Pratama',
                'phone' => '08100000002',
                'join_date' => '2022-03-01',
                'status' => 'active',
                'gender' => 'male',
                'address' => 'Bandung, Indonesia',
            ]
        );

        $devPos = Position::where('name', 'Junior Developer')->first();

        $karyawanUser = User::updateOrCreate(
            ['email' => 'sinta@absensi.app'],
            [
                'name' => 'Sinta Maharani',
                'password' => 'ganteng123',
                'email_verified_at' => now(),
            ]
        );
        $karyawanUser->syncRoles(['karyawan']);

        Employee::updateOrCreate(
            ['employee_code' => 'EMP003'],
            [
                'user_id' => $karyawanUser->id,
                'department_id' => $itDept?->id ?? 2,
                'position_id' => $devPos?->id ?? 5,
                'employee_code' => 'EMP003',
                'email' => 'sinta@absensi.app',
                'name' => 'Sinta Maharani',
                'phone' => '08100000003',
                'join_date' => '2023-06-01',
                'status' => 'active',
                'gender' => 'female',
                'address' => 'Surabaya, Indonesia',
            ]
        );
    }
}
