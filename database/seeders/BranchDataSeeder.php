<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Province;
use App\Models\Region;
use Illuminate\Database\Seeder;

class BranchDataSeeder extends Seeder
{
    // Source: AREA-REGION-DAN DAFTAR-CABANG.md
    // Region 1 logically spans Banten+Jabar but schema only allows one province_id → assigned to Banten
    public function run(): void
    {
        $structure = [
            [
                'province' => 'Banten',
                'region' => 'Region 1',
                'areas' => [
                    [
                        'name' => 'Area 1',
                        'branches' => [
                            ['code' => 'A1.', 'name' => 'Serang',           'lat' => -6.1221513809558745,  'lng' => 106.16832937974758,  'url' => 'https://maps.app.goo.gl/FFznK25k1DUfFC1TA?g_st=ic'],
                            ['code' => 'A2.', 'name' => 'Cilegon',          'lat' => -6.022306324696469,   'lng' => 106.04591519324514,  'url' => 'https://maps.app.goo.gl/ULMtwfV8UujTjHnC9?g_st=awb'],
                            ['code' => 'A3.', 'name' => 'Pandeglang',       'lat' => -6.307237298575515,   'lng' => 106.1040918779155,   'url' => 'https://share.google/CQ3PT4yK5fkyQI9m5'],
                            ['code' => 'A6.', 'name' => 'Rangkasbitung',    'lat' => -6.359135270714687,   'lng' => 106.25446970233696,  'url' => 'https://share.google/zpMVxrkkFtXw2V04k'],
                        ],
                    ],
                    [
                        'name' => 'Area 2',
                        'branches' => [
                            ['code' => 'A4.', 'name' => 'Tangerang Selatan', 'lat' => -6.305723706090739,  'lng' => 106.758476437401,    'url' => 'https://share.google/U2AGBUxOAXagVzDFh'],
                            ['code' => 'A5.', 'name' => 'Kota Tangerang',    'lat' => -6.167137547727367,  'lng' => 106.62706655784697,  'url' => 'https://maps.app.goo.gl/a391hUrKbuRS9cFX9'],
                            ['code' => 'B18.', 'name' => 'Bogor',            'lat' => -6.591707170800572,  'lng' => 106.78621142208179,  'url' => 'https://maps.app.goo.gl/PRVCd295zTPts1SP7?g_st=ac'],
                            ['code' => 'B20.', 'name' => 'Cibinong',         'lat' => -6.476643743696853,  'lng' => 106.85504276624377,  'url' => 'https://maps.app.goo.gl/SguDJjUCH91fS1Fs8'],
                        ],
                    ],
                    [
                        'name' => 'Area 3',
                        'branches' => [
                            ['code' => 'B3.', 'name' => 'Purwakarta',  'lat' => -6.514370481232455,   'lng' => 107.44430968155643,  'url' => 'https://maps.app.goo.gl/Y153N41dmiyrSdsi8?g_st=ic'],
                            ['code' => 'B4.', 'name' => 'Karawang',    'lat' => -6.298616963191858,   'lng' => 107.30278576258428,  'url' => 'https://maps.app.goo.gl/YsNvy9B1uj5BXPR3A?g_st=ic'],
                            ['code' => 'B5.', 'name' => 'Cikarang',    'lat' => -6.2968090140431805,  'lng' => 107.16716057974702,  'url' => 'https://maps.app.goo.gl/EzkLdLvysK2sMRqn8?g_st=ic'],
                            ['code' => 'B6.', 'name' => 'Depok',       'lat' => -6.401696120726071,   'lng' => 106.84007156441145,  'url' => 'https://maps.app.goo.gl/wNaf2JPmJLJTL9Vq8'],
                            ['code' => 'B17.', 'name' => 'Bekasi',     'lat' => -6.268107038467717,   'lng' => 106.9749622797391,   'url' => 'https://maps.app.goo.gl/bgu9iqL2JbQcKzc88'],
                        ],
                    ],
                ],
            ],
            [
                'province' => 'Jawa Barat',
                'region' => 'Region 2',
                'areas' => [
                    [
                        'name' => 'Area 4',
                        'branches' => [
                            ['code' => 'B1.I.',   'name' => 'Cipadung',   'lat' => -6.925324063620179,   'lng' => 107.71949182208294,  'url' => 'https://maps.app.goo.gl/JxRECGzhRAJovxLc7?g_st=awb'],
                            ['code' => 'B1.II.',  'name' => 'Cihapit',    'lat' => -6.907885891441036,   'lng' => 107.62788000496187,  'url' => 'https://maps.app.goo.gl/RwTnRqTzyRmedmrP8?g_st=iw'],
                            ['code' => 'B1.III.', 'name' => 'Panyileukan', 'lat' => -6.93883975845093,    'lng' => 107.71546837406802,  'url' => 'https://maps.app.goo.gl/9ZNXMFijzDooKuXf9'],
                            ['code' => 'B2.',     'name' => 'Cimahi',     'lat' => -6.868399474263653,   'lng' => 107.53954878959046,  'url' => 'https://maps.app.goo.gl/VyV6Y4x1owEEtiUV8?g_st=ac'],
                        ],
                    ],
                    [
                        'name' => 'Area 5',
                        'branches' => [
                            ['code' => 'B7.', 'name' => 'Subang',    'lat' => -6.573557679791727,  'lng' => 107.76306446440995,  'url' => 'https://share.google/S9ziI2ShBuZPozjg2'],
                            ['code' => 'B8.', 'name' => 'Cianjur',   'lat' => -6.818982055662078,  'lng' => 107.13675419649645,  'url' => 'https://maps.app.goo.gl/AYQukB7g77ustnNKA?g_st=ic'],
                            ['code' => 'B9.', 'name' => 'Sumedang',  'lat' => -6.861655338249519,  'lng' => 107.92258250675377,  'url' => 'https://share.google/pZeF6OQjik7fqMwpv'],
                        ],
                    ],
                    [
                        'name' => 'Area 6',
                        'branches' => [
                            ['code' => 'B10.', 'name' => 'Garut',       'lat' => -7.205635607789782,   'lng' => 107.90048602208094,  'url' => 'https://maps.app.goo.gl/2hXbdBrfS4udU4Bx9'],
                            ['code' => 'B11.', 'name' => 'Tasikmalaya', 'lat' => -7.3196431340956645,  'lng' => 108.2291831932469,   'url' => 'https://maps.app.goo.gl/SYYQCQV8UzWpojSG8'],
                            ['code' => 'B19.', 'name' => 'Singaparna',  'lat' => -7.349641484650128,   'lng' => 108.10886435090546,  'url' => 'https://maps.app.goo.gl/nKmCMwSsmq1ph2Ar7'],
                        ],
                    ],
                ],
            ],
            [
                'province' => 'Jawa Tengah',
                'region' => 'Region 3',
                'areas' => [
                    [
                        'name' => 'Area 7',
                        'branches' => [
                            ['code' => 'B12.', 'name' => 'Ciamis',  'lat' => -7.325341671575267,  'lng' => 108.34324794909774,  'url' => 'https://maps.app.goo.gl/hWBR2WRYsKXVKPv48'],
                            ['code' => 'C4.',  'name' => 'Banjar',   'lat' => -7.36956221700765,   'lng' => 108.53885497791451,  'url' => 'https://maps.app.goo.gl/p9dVubRGT4RTVS3Z7'],
                            ['code' => 'C5.',  'name' => 'Cilacap',  'lat' => -7.703938480189878,  'lng' => 109.02275360676236,  'url' => 'https://maps.app.goo.gl/9ZcpUHPLqudkPg2s9?g_st=ac'],
                        ],
                    ],
                    [
                        'name' => 'Area 8',
                        'branches' => [
                            ['code' => 'B13.', 'name' => 'Cirebon',    'lat' => -6.7116119768189915,  'lng' => 108.53702030857666,  'url' => 'https://maps.app.goo.gl/B6rJrLc2Ee6EqKvM8'],
                            ['code' => 'B14.', 'name' => 'Indramayu',  'lat' => -6.3242317150998035,  'lng' => 108.3240692067427,   'url' => 'https://maps.app.goo.gl/N1Yaz9WnZxnyhxLu7?g_st=ac'],
                            ['code' => 'B15.', 'name' => 'Kuningan',   'lat' => -6.974049336784399,   'lng' => 108.48681681038907,  'url' => 'https://maps.app.goo.gl/2mP7yNmTSmqoXeC77'],
                            ['code' => 'B16.', 'name' => 'Majalengka', 'lat' => -6.8382477911071495,  'lng' => 108.23500706442408,  'url' => 'https://maps.app.goo.gl/nCxU7hsH1iqxbyXr7'],
                        ],
                    ],
                    [
                        'name' => 'Area 9',
                        'branches' => [
                            ['code' => 'C1.', 'name' => 'Brebes',     'lat' => -6.8748910437254525,  'lng' => 109.04325269324289,  'url' => 'https://maps.app.goo.gl/VHwoUZaNmAjMq8hB9'],
                            ['code' => 'C2.', 'name' => 'Tegal',      'lat' => -6.877860490096986,   'lng' => 109.13282986259136,  'url' => 'https://maps.app.goo.gl/94Hj1T8cukbG1qCL8'],
                            ['code' => 'C3.', 'name' => 'Slawi',      'lat' => -6.973715039915537,   'lng' => 109.13265637612179,  'url' => 'https://maps.app.goo.gl/D7URbeG3Hcbm5E348'],
                            ['code' => 'C6.', 'name' => 'Pekalongan', 'lat' => -6.8996435051407285,  'lng' => 109.66405804908604,  'url' => 'https://share.google/bbJOct3PpXqnTKcjA'],
                            ['code' => 'C7.', 'name' => 'Pemalang',   'lat' => -6.896980999336133,   'lng' => 109.3847396644227,   'url' => 'https://maps.app.goo.gl/FwWTJ71h6cARYnyN9?g_st=ic'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($structure as $prov) {
            $province = Province::firstOrCreate(['name' => $prov['province']]);
            $region = Region::firstOrCreate(
                ['province_id' => $province->id, 'name' => $prov['region']]
            );

            foreach ($prov['areas'] as $areaData) {
                $area = Area::firstOrCreate(
                    ['region_id' => $region->id, 'name' => $areaData['name']]
                );

                foreach ($areaData['branches'] as $b) {
                    Branch::updateOrCreate(
                        ['code_branches' => $b['code']],
                        [
                            'areas_id' => $area->id,
                            'branch_name' => $b['name'],
                            'gmaps_url' => $b['url'],
                            'latitude' => $b['lat'],
                            'longitude' => $b['lng'],
                            'is_active' => true,
                            'radius_meters' => 500,
                            'address' => '-',
                            'whatsapp' => '08xxxxxxxx',
                            'instagram' => 'Mindsia',
                        ]
                    );
                }
            }
        }
    }
}
