<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view dashboard',
            // Karyawan
            'view employees', 'create employees', 'edit employees', 'delete employees',
            // Departemen
            'view departments', 'create departments', 'edit departments', 'delete departments',
            // Jabatan
            'view positions', 'create positions', 'edit positions', 'delete positions',
            // Absensi
            'view all attendances', 'view own attendance', 'create attendance',
            'edit attendance', 'correct attendance',
            // Cuti
            'view all leaves', 'view own leaves', 'create leave',
            'approve leave', 'reject leave',
            // Lembur
            'view all overtimes', 'view own overtimes', 'create overtime',
            'approve overtime', 'reject overtime',
            // Komponen Gaji
            'view salary components', 'create salary components',
            'edit salary components', 'delete salary components',
            // Payroll
            'view all payrolls', 'view own payroll', 'generate payroll',
            'approve payroll', 'mark payroll paid',
            // Bonus
            'view all bonuses', 'view own bonuses', 'create bonus',
            'approve bonus', 'delete bonus',
            // Laporan
            'view reports', 'export reports',
            // Pengaturan
            'view settings', 'edit settings',
            // Lokasi Kerja
            'view work locations', 'create work locations',
            'edit work locations', 'delete work locations',
            // Jadwal Kerja
            'view work schedules', 'create work schedules',
            'edit work schedules', 'delete work schedules',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin — semua permission
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // HRD — hampir semua kecuali setting sistem
        $hrd = Role::firstOrCreate(['name' => 'hrd']);
        $hrd->syncPermissions([
            'view dashboard',
            'view employees', 'create employees', 'edit employees',
            'view departments', 'create departments', 'edit departments',
            'view positions', 'create positions', 'edit positions',
            'view all attendances', 'correct attendance',
            'view all leaves', 'approve leave', 'reject leave',
            'view all overtimes', 'approve overtime', 'reject overtime',
            'view salary components', 'create salary components', 'edit salary components',
            'view all payrolls', 'generate payroll', 'approve payroll', 'mark payroll paid',
            'view all bonuses', 'create bonus', 'approve bonus',
            'view reports', 'export reports',
            'view work locations', 'create work locations', 'edit work locations',
            'view work schedules', 'create work schedules', 'edit work schedules',
        ]);

        // Kepala Departemen
        $kepala = Role::firstOrCreate(['name' => 'kepala_departemen']);
        $kepala->syncPermissions([
            'view dashboard',
            'view employees',
            'view all attendances', 'view own attendance', 'create attendance',
            'view all leaves', 'view own leaves', 'create leave', 'approve leave', 'reject leave',
            'view all overtimes', 'view own overtimes', 'create overtime', 'approve overtime', 'reject overtime',
            'view own payroll',
            'view own bonuses',
            'view reports',
        ]);

        // Karyawan
        $karyawan = Role::firstOrCreate(['name' => 'karyawan']);
        $karyawan->syncPermissions([
            'view dashboard',
            'view own attendance', 'create attendance',
            'view own leaves', 'create leave',
            'view own overtimes', 'create overtime',
            'view own payroll',
            'view own bonuses',
        ]);
    }
}
