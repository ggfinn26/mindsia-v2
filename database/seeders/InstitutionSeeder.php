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

        // 1. Load regions map
        // To easily match string to ID
        $regions = Region::all();
        $regionMap = [];
        foreach ($regions as $r) {
            $name = strtolower($r->name);
            // KAB. BOGOR -> bogor
            $cleanName = str_replace(['kota/kabupaten ', 'kabupaten ', 'kota ', 'kab. '], '', $name);
            $regionMap[trim($cleanName)] = $r->id;
        }

        $schoolsFile = '/Users/rismaniswaty/Documents/belajar-bikin-mindsia/datasekolah/branch_schools.json';
        $uniFiles = [
            '/Users/rismaniswaty/Documents/belajar-bikin-mindsia/datasekolah/banten_universities.json',
            '/Users/rismaniswaty/Documents/belajar-bikin-mindsia/datasekolah/jawa_barat_universities.json',
            '/Users/rismaniswaty/Documents/belajar-bikin-mindsia/datasekolah/jawa_tengah_universities.json',
        ];

        $insertData = [];
        $chunkSize = 1000;

        $insertChunks = function ($data) use ($chunkSize) {
            $chunks = array_chunk($data, $chunkSize);
            foreach ($chunks as $chunk) {
                DB::table('institutions')->insert($chunk);
            }
        };

        // Parse Schools
        if (file_exists($schoolsFile)) {
            $schools = json_decode(file_get_contents($schoolsFile), true);
            foreach ($schools as $school) {
                $jenjang = null;
                $bp = strtoupper($school['bentukPendidikan'] ?? '');
                if (in_array($bp, ['SD', 'MI', 'SDLB'])) {
                    $jenjang = 'SD';
                } elseif (in_array($bp, ['SMP', 'MTS', 'SMPLB'])) {
                    $jenjang = 'SMP';
                } elseif (in_array($bp, ['SMA', 'SMK', 'MA', 'MAK', 'SMALB'])) {
                    $jenjang = 'SMA';
                }

                if (! $jenjang) {
                    continue;
                } // Skip PAUD, TK, etc.

                $regName = strtolower($school['branch_regency'] ?? '');
                $regName = str_replace(['kota/kabupaten ', 'kabupaten ', 'kota ', 'kab. '], '', $regName);

                if (isset($regionMap[trim($regName)])) {
                    $insertData[] = [
                        'jenjang_institution' => $jenjang,
                        'institution_name' => $school['nama'],
                        'regions_id' => $regionMap[trim($regName)],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (count($insertData) >= 10000) {
                    $insertChunks($insertData);
                    $insertData = [];
                }
            }
        }

        // Parse Universities
        foreach ($uniFiles as $file) {
            if (file_exists($file)) {
                $unis = json_decode(file_get_contents($file), true);
                foreach ($unis as $uni) {
                    $regName = strtolower($uni['kab_kota_pt'] ?? '');
                    $regName = str_replace(['kota/kabupaten ', 'kabupaten ', 'kota ', 'kab. '], '', $regName);

                    if (isset($regionMap[trim($regName)])) {
                        $insertData[] = [
                            'jenjang_institution' => 'PERGURUAN TINGGI',
                            'institution_name' => $uni['nama_pt'],
                            'regions_id' => $regionMap[trim($regName)],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        if (count($insertData) > 0) {
            $insertChunks($insertData);
        }

        echo "Finished seeding institutions!\n";
    }
}
