<?php

namespace Database\Seeders;

use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();
        $now = Carbon::now();

        // province_code (BPS) → region_id
        $provinceCodes = ['36' => 1, '32' => 2, '33' => 3]; // Banten, Jawa Barat, Jawa Tengah

        // province name fallback → region_id
        $provinceNameMap = [];
        foreach (Region::with('province')->get() as $region) {
            $key = strtolower(trim($region->province->name ?? ''));
            if ($key && ! isset($provinceNameMap[$key])) {
                $provinceNameMap[$key] = $region->id;
            }
        }

        $resolveRegion = function (string $code, string $provinceName) use ($provinceCodes, $provinceNameMap): ?int {
            if (isset($provinceCodes[$code])) {
                return $provinceCodes[$code];
            }
            $key = strtolower(trim(preg_replace('/^prov\.?\s*/i', '', $provinceName)));

            return $provinceNameMap[$key] ?? null;
        };

        $baseDir = '/Users/rismaniswaty/Documents/MINDSIA-V2/datasekolah';
        $insertData = [];
        $chunkSize = 1000;

        $flush = function () use (&$insertData, $chunkSize) {
            foreach (array_chunk($insertData, $chunkSize) as $chunk) {
                DB::table('institutions')->insert($chunk);
            }
            $insertData = [];
        };

        // --- Merged schools ---
        $schoolsFile = "{$baseDir}/merged_schools.json";
        if (file_exists($schoolsFile)) {
            $schools = json_decode(file_get_contents($schoolsFile), true) ?? [];
            foreach ($schools as $s) {
                $regionId = $resolveRegion($s['province_code'] ?? '', $s['selected_province'] ?? '');
                if (! $regionId) {
                    continue;
                }
                $insertData[] = [
                    'jenjang_institution' => $s['jenjang'],
                    'institution_name' => $s['nama'],
                    'regions_id' => $regionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (count($insertData) >= 10000) {
                    $flush();
                }
            }
        }

        // --- Universities ---
        $uniFiles = [
            "{$baseDir}/banten_universities.json",
            "{$baseDir}/jawa_barat_universities.json",
            "{$baseDir}/jawa_tengah_universities.json",
        ];
        foreach ($uniFiles as $file) {
            if (! file_exists($file)) {
                continue;
            }
            foreach (json_decode(file_get_contents($file), true) ?? [] as $u) {
                $regionId = $resolveRegion('', $u['provinsi_pt'] ?? '');
                if (! $regionId) {
                    continue;
                }
                $insertData[] = [
                    'jenjang_institution' => 'PERGURUAN TINGGI',
                    'institution_name' => $u['nama_pt'],
                    'regions_id' => $regionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($insertData) {
            $flush();
        }

        // --- Special fallback entries per region ---
        $specialNames = ['Institusi tidak ditemukan', 'Tidak Sekolah/Sudah Lulus'];
        $realRegionIds = array_values(array_filter(
            array_unique(array_values($provinceCodes))
        ));
        $specials = [];
        foreach ($realRegionIds as $regionId) {
            foreach ($specialNames as $name) {
                $specials[] = [
                    'jenjang_institution' => 'SMA',
                    'institution_name' => $name,
                    'regions_id' => $regionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('institutions')->insert($specials);

        echo "Done seeding institutions!\n";
    }
}
