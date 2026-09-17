<?php

namespace Database\Seeders;

use App\Models\PayrollComponent;
use Illuminate\Database\Seeder;

class PayrollComponentSeeder extends Seeder
{
    public function run(): void
    {
        $components = [
            // Earnings
            ['component_code' => 'SESSION_FEE',        'component_name' => 'Honorarium Sesi',             'component_type' => 'earning',   'calculation_method' => 'session'],
            ['component_code' => 'DAILY_SALARY',       'component_name' => 'Gaji Harian',                 'component_type' => 'earning',   'calculation_method' => 'daily'],
            ['component_code' => 'BASE_SALARY',        'component_name' => 'Gaji Pokok',                  'component_type' => 'earning',   'calculation_method' => 'fixed'],
            ['component_code' => 'TRANSPORTATION',     'component_name' => 'Tunjangan Transportasi',      'component_type' => 'earning',   'calculation_method' => 'fixed'],
            ['component_code' => 'JOB_ALLOWANCE',      'component_name' => 'Tunjangan Jabatan',           'component_type' => 'earning',   'calculation_method' => 'fixed'],
            ['component_code' => 'PROJECT_COMMISSION', 'component_name' => 'Komisi Proyek',               'component_type' => 'earning',   'calculation_method' => 'fixed'],
            ['component_code' => 'INCENTIVE',          'component_name' => 'Insentif',                    'component_type' => 'earning',   'calculation_method' => 'fixed'],
            ['component_code' => 'OVERTIME',           'component_name' => 'Lembur',                      'component_type' => 'earning',   'calculation_method' => 'manual'],
            ['component_code' => 'BONUS_MARKETING',    'component_name' => 'Bonus Marketing',             'component_type' => 'earning',   'calculation_method' => 'manual'],
            ['component_code' => 'BONUS_KPI',          'component_name' => 'Bonus KPI',                   'component_type' => 'earning',   'calculation_method' => 'manual'],
            ['component_code' => 'BONUS_SPECIAL',      'component_name' => 'Bonus Khusus',                'component_type' => 'earning',   'calculation_method' => 'manual'],

            // Deductions
            ['component_code' => 'BPJS_KESEHATAN',    'component_name' => 'BPJS Kesehatan',              'component_type' => 'deduction', 'calculation_method' => 'manual'],
            ['component_code' => 'PPH21',              'component_name' => 'PPh 21',                      'component_type' => 'deduction', 'calculation_method' => 'manual'],
            ['component_code' => 'LATE_DEDUCTION',     'component_name' => 'Potongan Terlambat',          'component_type' => 'deduction', 'calculation_method' => 'manual'],
            ['component_code' => 'ABSENT_DEDUCTION',   'component_name' => 'Potongan Absen',              'component_type' => 'deduction', 'calculation_method' => 'manual'],
            ['component_code' => 'LEAVE_DEDUCTION',    'component_name' => 'Potongan Cuti Tak Berbayar',  'component_type' => 'deduction', 'calculation_method' => 'daily'],
        ];

        foreach ($components as $data) {
            PayrollComponent::updateOrCreate(
                ['component_code' => $data['component_code']],
                array_merge($data, ['is_active' => true, 'is_system' => true])
            );
        }
    }
}
