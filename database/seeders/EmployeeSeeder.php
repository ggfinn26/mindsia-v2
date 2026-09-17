<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class EmployeeSeeder extends Seeder
{
    // Source: PEGAWAI-LIST-POSITION-LIST-AND-DIVISION-LIST.md
    // Columns: [code, name, gender, placement, position, on_boarding, off_boarding]
    // placement: 'hq' | 'area:Area N' | 'branch:BranchName'
    // gender: M | F
    // dates: DD-MM-YYYY or '-' (permanent/unknown)
    // CFO=same person as CEO, CMO=same as COO — separate Employee records (different code+email)
    // OT03 BANDUNG IV — branch belum ada, di-assign ke area:Area 4 (PIC-nya Nurhidayat Syahban)
    // Separation list: kode duplikat MA01/MA02/PIC003 diberi suffix -B
    // Adrian Ramadhan area unknown — sementara di-assign HQ sampai area diklarifikasi
    // ET0025 Ms. Cindy — tanggal kosong, pakai placeholder join date
    // ET0033 Ms. Dheta — off 19-01-2025 error data, dikoreksi 19-01-2026
    private array $employees = [
        // C-Level — HQ
        ['CEO',    'Muh. Indra Fakhruddin',  'M', 'hq', 'Chief Executive Officer',         '-',          '-'],
        ['COO',    'Muh. Aria Farhan',        'M', 'hq', 'Chief Operating Officer',         '-',          '-'],
        ['CHRO',   'Muh. Fuad Rumy',          'M', 'hq', 'Chief Human Resources Officer',   '-',          '-'],
        ['CPO',    'Wahyuni Amirullah',        'F', 'hq', 'Chief Product Officer',           '19-07-2025', '31-08-2026'],
        // dual-role C-level (same persons, different positions)
        ['CFO',    'Muh. Indra Fakhruddin',  'M', 'hq', 'Chief Financial Officer',         '-',          '-'],
        ['CMO',    'Muh. Aria Farhan',        'M', 'hq', 'Chief Marketing Officer',         '-',          '-'],

        // Manager Area — area-level (branch_id null, area_id + region_id set)
        ['MA01',   'Rachel Fadilah',           'F', 'area:Area 3', 'Manager Area',  '01-07-2025', '01-08-2026'],
        ['MA02',   'Ifan Mustakim',            'M', 'area:Area 8', 'Manager Area',  '01-10-2025', '01-11-2026'],
        ['MA03',   'Fikri Padilah Arham',      'M', 'area:Area 6', 'Manager Area',  '03-08-2024', '03-09-2025'],
        ['MA04',   'Muhamad Ligar Maulana',    'M', 'area:Area 7', 'Manager Area',  '24-09-2024', '24-10-2025'],
        ['PIC001', 'Faizal Aditiya Maulana',   'M', 'area:Area 5', 'Manager Area',  '14-02-2025', '14-03-2026'],
        ['PIC002', 'Haeruddin',                'M', 'area:Area 1', 'Manager Area',  '01-05-2025', '01-05-2026'],
        ['PIC003', 'Nurhidayat Syahban',       'M', 'area:Area 4', 'Manager Area',  '01-08-2025', '01-08-2026'],
        ['PIC004', 'Sulis Putri Shofura',      'F', 'area:Area 2', 'Manager Area',  '01-07-2025', '01-07-2026'],
        ['PIC005', 'Mr. Bim',                  'M', 'area:Area 7', 'Person In Charge', '01-09-2025', '01-09-2026'],
        ['MO02',   'Wardiman',                 'M', 'area:Area 9', 'Person In Charge', '01-07-2025', '01-08-2026'],
        ['MO03',   'Ifani Febianti',           'F', 'area:Area 3', 'Person In Charge', '01-07-2025', '01-08-2026'],
        ['MO04',   'Fitriadi Akbar',           'M', 'area:Area 9', 'Person In Charge', '01-10-2025', '01-11-2026'],

        // Marketing — branch-level
        ['MO01',   'Fani Septiani Rahayu',     'F', 'branch:Garut',    'Marketing', '01-07-2025', '01-08-2026'],
        ['MO05',   'Muh. Ka\'ab Akbar',        'M', 'branch:Tegal',    'Marketing', '03-08-2024', '03-09-2025'],
        ['MO06',   'Upi Aupiya Madal Ayami',   'F', 'branch:Kuningan', 'Marketing', '24-09-2024', '24-10-2025'],

        // Regular Tutors — branch-level
        // BANDUNG I=Cipadung, BANDUNG II=Cihapit, BANDUNG III=Panyileukan (BranchDataSeeder names)
        ['ET0012', 'Ms. Winda',      'F', 'branch:Ciamis',      'Regular Tutor', '01-02-2025', '01-09-2025'],
        ['ET0015', 'Ms. Salma',      'F', 'branch:Cimahi',      'Regular Tutor', '17-04-2025', '17-09-2025'],
        ['ET0018', 'Mr. Azkal',      'M', 'branch:Cipadung',    'Regular Tutor', '05-05-2025', '05-11-2025'],
        ['ET0021', 'Ms. Tuti',       'F', 'branch:Kuningan',    'Regular Tutor', '19-05-2025', '19-06-2026'],
        ['ET0022', 'Ms. Vannya',     'F', 'branch:Cikarang',    'Regular Tutor', '01-05-2025', '01-11-2025'],
        ['ET0027', 'Mr. Subhan',     'M', 'branch:Cirebon',     'Regular Tutor', '29-04-2025', '29-10-2025'],
        ['ET0029', 'Ms. Zayyinah',   'F', 'branch:Cihapit',     'Regular Tutor', '18-07-2025', '18-12-2025'],
        ['ET0030', 'Ms. Salsa',      'F', 'branch:Indramayu',   'Regular Tutor', '15-06-2025', '15-12-2025'],
        ['ET0031', 'Ms. Fitri',      'F', 'branch:Panyileukan', 'Regular Tutor', '18-07-2025', '18-12-2025'],
        // ET0033: off 19-01-2025 typo error — dikoreksi 19-01-2026 (kontrak 6 bulan)
        ['ET0033', 'Ms. Dheta',      'F', 'branch:Banjar',      'Regular Tutor', '19-07-2025', '19-01-2026'],
        ['ET0035', 'Mr. Rafli',      'M', 'branch:Garut',       'Regular Tutor', '14-07-2025', '14-07-2026'],
        ['ET0036', 'Ms. Gadis',      'F', 'branch:Slawi',       'Regular Tutor', '11-06-2025', '03-08-2026'],
        ['ET0037', 'Mr. Faruq',      'M', 'branch:Garut',       'Regular Tutor', '25-08-2025', '25-09-2026'],
        ['ET0038', 'Ms. Wiwin',      'F', 'branch:Cimahi',      'Regular Tutor', '15-08-2025', '15-02-2026'],
        ['ET0039', 'Ms. Nita',       'F', 'branch:Karawang',    'Regular Tutor', '12-08-2025', '12-02-2026'],
        ['ET0040', 'Mr. Henda',      'M', 'branch:Garut',       'Regular Tutor', '01-08-2025', '01-08-2026'],
        ['ET0041', 'Ms. Winda',      'F', 'branch:Majalengka',  'Regular Tutor', '20-08-2025', '20-09-2026'],
        ['ET0042', 'Ms. Tisa',       'F', 'branch:Majalengka',  'Regular Tutor', '29-08-2025', '29-09-2026'],
        ['ET0043', 'Ms. Khai',       'F', 'branch:Banjar',      'Regular Tutor', '06-10-2025', '06-04-2026'],
        ['ET0044', 'Ms. Eva',        'F', 'branch:Tegal',       'Regular Tutor', '06-10-2025', '06-04-2026'],
        ['ET0045', 'Ms. Sarah',      'F', 'branch:Cianjur',     'Regular Tutor', '30-09-2025', '30-03-2026'],
        ['ET0046', 'Ms. Dian',       'F', 'branch:Brebes',      'Regular Tutor', '07-09-2025', '07-03-2026'],
        ['ET0047', 'Ms. Annisa',     'F', 'branch:Karawang',    'Regular Tutor', '20-10-2025', '20-04-2026'],
        ['ET0048', 'Mr. Fakhri',     'M', 'branch:Indramayu',   'Regular Tutor', '01-11-2025', '01-05-2026'],
        ['ET0049', 'Ms. Ninda',      'F', 'branch:Bekasi',      'Regular Tutor', '31-10-2025', '31-10-2026'],
        ['ET0050', 'Mrs. Wirdah',    'F', 'branch:Depok',       'Regular Tutor', '03-11-2025', '03-05-2026'],

        // Official Tutors — branch-level
        // OT03 BANDUNG IV: branch belum ada, di-assign area:Area 4 (PIC: Nurhidayat Syahban)
        ['OT01',   'Mr. Bryan',   'M', 'branch:Purwakarta',  'Official Tutor', '04-08-2025', '04-02-2026'],
        ['OT02',   'Ms. Safa',    'F', 'branch:Subang',      'Official Tutor', '04-08-2025', '04-08-2025'],
        ['OT03',   'Ms. Upi',     'F', 'area:Area 4',        'Official Tutor', '04-08-2025', '04-08-2025'],
        ['OT04',   'Mr. Indra',   'M', 'branch:Cianjur',     'Official Tutor', '04-08-2025', '04-08-2025'],
        ['OT05',   'Mr. Fajar',   'M', 'branch:Garut',       'Official Tutor', '04-08-2025', '04-08-2025'],
        ['OT06',   'Mr. Qim',     'M', 'branch:Indramayu',   'Official Tutor', '04-08-2025', '04-08-2025'],
        ['OT07',   'Mr. Soni',    'M', 'branch:Sumedang',    'Official Tutor', '03-08-2025', '03-11-2025'],
        ['OT08',   'Ms. Rahayu',  'F', 'branch:Kuningan',    'Official Tutor', '04-08-2025', '04-08-2025'],
        ['OT09',   'Ms. Fijar',   'F', 'branch:Tasikmalaya', 'Official Tutor', '04-08-2025', '04-08-2025'],

        // Pegawai Dipisahkan — ada di PEGAWAI-LIST tapi data tidak lengkap/konflik
        // Kode duplikat diberi suffix -B
        ['MA01-B',   'Puteri Evi Handayani', 'F', 'area:Area 6', 'Manager Area',     '01-07-2025', '01-08-2026'],
        ['MA02-B',   'Silvy Amelliany',      'F', 'area:Area 5', 'Manager Area',     '01-07-2025', '01-08-2026'],
        ['PIC003-B', 'Adrian Ramadhan',      'M', 'hq',          'Person In Charge', '01-07-2025', '01-07-2026'],
        ['ET0025',   'Ms. Cindy',            'F', 'branch:Serang', 'Regular Tutor',  '-',          '-'],
    ];

    public function run(): void
    {
        $today = Carbon::today();
        $positions = Position::all()->keyBy('position_name');
        $areas = Area::with('region')->get()->keyBy('name');

        foreach ($this->employees as [$code, $name, $gender, $placement, $positionName, $onBoarding, $offBoarding]) {
            $position = $positions->get($positionName);

            if (! $position) {
                $this->command->warn("Position not found: {$positionName} — skipping {$code}");

                continue;
            }

            [$branchId, $areaId, $regionId, $isHq] = $this->resolveLocation($placement, $areas, $code);

            if ($branchId === false) {
                continue; // already warned inside resolveLocation
            }

            $joinDate = $this->parseDate($onBoarding) ?? '2024-01-01';
            $contractEnd = $this->parseDate($offBoarding);
            $isActive = $contractEnd === null || Carbon::parse($contractEnd)->greaterThan($today);

            $employee = Employee::firstOrCreate(
                ['employee_code' => $code],
                [
                    'full_name' => $name,
                    'gender' => $gender,
                    'birthdate' => '1990-01-01',
                    'email' => strtolower($code).'@mindsia.id',
                    'whatsapp_number' => '08000000000',
                    'branch_id' => $branchId,
                    'area_id' => $areaId,
                    'region_id' => $regionId,
                    'is_hq' => $isHq,
                    'is_active' => $isActive,
                ]
            );

            EmploymentStatus::firstOrCreate(
                ['employees_id' => $employee->id, 'position_id' => $position->id],
                [
                    'type_employment' => $isHq ? 'permanent' : 'contract',
                    'join_date' => $joinDate,
                    'contract_start_date' => $joinDate,
                    'contract_end_date' => $contractEnd,
                    'setup_incomplete' => false,
                ]
            );
        }
    }

    /** @return array{int|null, int|null, int|null, bool}|array{false, false, false, false} */
    private function resolveLocation(string $placement, Collection $areas, string $code): array
    {
        if ($placement === 'hq') {
            return [null, null, null, true];
        }

        if (str_starts_with($placement, 'area:')) {
            $areaName = substr($placement, 5);
            $area = $areas->get($areaName);

            if (! $area) {
                $this->command->warn("Area not found: {$areaName} — skipping {$code}");

                return [false, false, false, false];
            }

            return [null, $area->id, $area->region_id, false];
        }

        if (str_starts_with($placement, 'branch:')) {
            $branchName = substr($placement, 7);
            $branch = Branch::where('branch_name', $branchName)->first();

            if (! $branch) {
                $this->command->warn("Branch not found: {$branchName} — skipping {$code}");

                return [false, false, false, false];
            }

            $area = $areas->firstWhere('id', $branch->areas_id);

            return [$branch->id, $branch->areas_id, $area?->region_id, false];
        }

        $this->command->warn("Unknown placement format: {$placement} — skipping {$code}");

        return [false, false, false, false];
    }

    private function parseDate(string $date): ?string
    {
        if ($date === '-' || $date === '') {
            return null;
        }

        return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
    }
}
