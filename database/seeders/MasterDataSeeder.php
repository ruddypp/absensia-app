<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use App\Models\WorkLocation;
use App\Models\WorkSchedule;
use App\Models\SalaryComponent;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Departemen
        $dept = [
            ['name' => 'Human Resources', 'code' => 'HR'],
            ['name' => 'Teknologi Informasi', 'code' => 'IT'],
            ['name' => 'Keuangan', 'code' => 'FIN'],
            ['name' => 'Operasional', 'code' => 'OPS'],
            ['name' => 'Marketing', 'code' => 'MKT'],
        ];
        foreach ($dept as $d) {
            Department::firstOrCreate(['code' => $d['code']], $d);
        }

        // Jabatan per departemen
        $positions = [
            ['HR', 'Manager HR', 12000000],
            ['HR', 'Staff HR', 6000000],
            ['IT', 'Manager IT', 15000000],
            ['IT', 'Senior Developer', 12000000],
            ['IT', 'Junior Developer', 7000000],
            ['FIN', 'Manager Keuangan', 13000000],
            ['FIN', 'Staff Akuntansi', 6500000],
            ['OPS', 'Supervisor Operasional', 9000000],
            ['OPS', 'Staff Operasional', 5500000],
            ['MKT', 'Manager Marketing', 12000000],
            ['MKT', 'Digital Marketing', 7000000],
        ];
        foreach ($positions as [$code, $name, $salary]) {
            $dept = Department::where('code', $code)->first();
            if ($dept) {
                Position::firstOrCreate(
                    ['department_id' => $dept->id, 'name' => $name],
                    ['base_salary' => $salary]
                );
            }
        }

        // Lokasi Kerja
        WorkLocation::firstOrCreate(
            ['name' => 'Kantor Pusat'],
            [
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'radius_meters' => 100,
            ]
        );

        // Jadwal Kerja
        WorkSchedule::firstOrCreate(
            ['name' => 'Shift Normal'],
            [
                'check_in_start' => '07:30:00',
                'check_in_end' => '09:00:00',
                'check_out_start' => '17:00:00',
                'check_out_end' => '20:00:00',
                'work_hours' => 8,
                'is_default' => true,
            ]
        );

        // Komponen Gaji
        $components = [
            ['name' => 'Tunjangan Makan', 'type' => 'allowance', 'calculation_type' => 'fixed', 'amount' => 750000, 'is_taxable' => false],
            ['name' => 'Tunjangan Transport', 'type' => 'allowance', 'calculation_type' => 'fixed', 'amount' => 500000, 'is_taxable' => false],
            ['name' => 'Tunjangan Komunikasi', 'type' => 'allowance', 'calculation_type' => 'fixed', 'amount' => 300000, 'is_taxable' => false],
            ['name' => 'BPJS Kesehatan (4%)', 'type' => 'deduction', 'calculation_type' => 'percentage', 'amount' => 4, 'is_taxable' => false],
            ['name' => 'BPJS Ketenagakerjaan (2%)', 'type' => 'deduction', 'calculation_type' => 'percentage', 'amount' => 2, 'is_taxable' => false],
            ['name' => 'Potongan Keterlambatan', 'type' => 'deduction', 'calculation_type' => 'fixed', 'amount' => 0, 'is_taxable' => false],
        ];
        foreach ($components as $comp) {
            SalaryComponent::firstOrCreate(['name' => $comp['name']], $comp);
        }
    }
}
