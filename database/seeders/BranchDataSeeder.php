<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Province;
use App\Models\Region;
use Illuminate\Database\Seeder;

class BranchDataSeeder extends Seeder
{
    public function run(): void
    {
        $json = '[
  {
    "province": "Banten",
    "region": "Kota/Kabupaten Serang",
    "code": "A1.",
    "name": "Serang",
    "url": "https://maps.app.goo.gl/FFznK25k1DUfFC1TA?g_st=ic",
    "lat": "-6.1221513809558745",
    "lng": "106.16832937974758"
  },
  {
    "province": "Banten",
    "region": "Kota/Kabupaten Cilegon",
    "code": "A2.",
    "name": "Cilegon",
    "url": "https://maps.app.goo.gl/ULMtwfV8UujTjHnC9?g_st=awb",
    "lat": "-6.022306324696469",
    "lng": "106.04591519324514"
  },
  {
    "province": "Banten",
    "region": "Kota/Kabupaten Pandeglang",
    "code": "A3.",
    "name": "Pandeglang",
    "url": "https://share.google/CQ3PT4yK5fkyQI9m5",
    "lat": "-6.307237298575515",
    "lng": "106.1040918779155"
  },
  {
    "province": "Banten",
    "region": "Kota/Kabupaten Tangerang Selatan",
    "code": "A4.",
    "name": "Tangerang Selatan",
    "url": "https://share.google/U2AGBUxOAXagVzDFh",
    "lat": "-6.305723706090739",
    "lng": "106.758476437401"
  },
  {
    "province": "Banten",
    "region": "Kota Tangerang",
    "code": "A5.",
    "name": "Kota Tangerang",
    "url": "https://maps.app.goo.gl/a391hUrKbuRS9cFX9",
    "lat": "-6.167137547727367",
    "lng": "106.62706655784697"
  },
  {
    "province": "Banten",
    "region": "Kabupaten Lebak",
    "code": "A6.",
    "name": "Rangkasbitung",
    "url": "https://share.google/zpMVxrkkFtXw2V04k",
    "lat": "-6.359135270714687",
    "lng": "106.25446970233696"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota Bandung",
    "code": "B1.I.",
    "name": "Cipadung",
    "url": "https://maps.app.goo.gl/JxRECGzhRAJovxLc7?g_st=awb",
    "lat": "-6.925324063620179",
    "lng": "107.71949182208294"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota Bandung",
    "code": "B1.II.",
    "name": "Cihapit",
    "url": "https://maps.app.goo.gl/RwTnRqTzyRmedmrP8?g_st=iw",
    "lat": "-6.907885891441036",
    "lng": "107.62788000496187"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota Bandung",
    "code": "B1.III.",
    "name": "Panyileukan",
    "url": "https://maps.app.goo.gl/9ZNXMFijzDooKuXf9",
    "lat": "-6.93883975845093",
    "lng": "107.71546837406802"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Cimahi",
    "code": "B2.",
    "name": "Cimahi",
    "url": "https://maps.app.goo.gl/VyV6Y4x1owEEtiUV8?g_st=ac",
    "lat": "-6.868399474263653",
    "lng": "107.53954878959046"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Purwakarta",
    "code": "B3.",
    "name": "Purwakarta",
    "url": "https://maps.app.goo.gl/Y153N41dmiyrSdsi8?g_st=ic",
    "lat": "-6.514370481232455",
    "lng": "107.44430968155643"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Karawang",
    "code": "B4.",
    "name": "Karawang",
    "url": "https://maps.app.goo.gl/YsNvy9B1uj5BXPR3A?g_st=ic",
    "lat": "-6.298616963191858",
    "lng": "107.30278576258428"
  },
  {
    "province": "Jawa Barat",
    "region": "Kabupaten Bekasi",
    "code": "B5.",
    "name": "Cikarang",
    "url": "https://maps.app.goo.gl/EzkLdLvysK2sMRqn8?g_st=ic",
    "lat": "-6.2968090140431805",
    "lng": "107.16716057974702"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Depok",
    "code": "B6.",
    "name": "Depok",
    "url": "https://maps.app.goo.gl/wNaf2JPmJLJTL9Vq8",
    "lat": "-6.401696120726071",
    "lng": "106.84007156441145"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Subang",
    "code": "B7.",
    "name": "Subang",
    "url": "https://share.google/S9ziI2ShBuZPozjg2",
    "lat": "-6.573557679791727",
    "lng": "107.76306446440995"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Cianjur",
    "code": "B8.",
    "name": "Cianjur",
    "url": "https://maps.app.goo.gl/AYQukB7g77ustnNKA?g_st=ic",
    "lat": "-6.818982055662078",
    "lng": "107.13675419649645"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Sumedang",
    "code": "B9.",
    "name": "Sumedang",
    "url": "https://share.google/pZeF6OQjik7fqMwpv",
    "lat": "-6.861655338249519",
    "lng": "107.92258250675377"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Garut",
    "code": "B10.",
    "name": "Garut",
    "url": "https://maps.app.goo.gl/2hXbdBrfS4udU4Bx9",
    "lat": "-7.205635607789782",
    "lng": "107.90048602208094"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Tasikmalaya",
    "code": "B11.",
    "name": "Tasikmalaya",
    "url": "https://maps.app.goo.gl/SYYQCQV8UzWpojSG8",
    "lat": "-7.3196431340956645",
    "lng": "108.2291831932469"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Ciamis",
    "code": "B12.",
    "name": "Ciamis",
    "url": "https://maps.app.goo.gl/hWBR2WRYsKXVKPv48",
    "lat": "-7.325341671575267",
    "lng": "108.34324794909774"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Cirebon",
    "code": "B13.",
    "name": "Cirebon",
    "url": "https://maps.app.goo.gl/B6rJrLc2Ee6EqKvM8",
    "lat": "-6.7116119768189915",
    "lng": "108.53702030857666"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Indramayu",
    "code": "B14.",
    "name": "Indramayu",
    "url": "https://maps.app.goo.gl/N1Yaz9WnZxnyhxLu7?g_st=ac",
    "lat": "-6.3242317150998035",
    "lng": "108.3240692067427"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Kuningan",
    "code": "B15.",
    "name": "Kuningan",
    "url": "https://maps.app.goo.gl/2mP7yNmTSmqoXeC77",
    "lat": "-6.974049336784399",
    "lng": "108.48681681038907"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Majalengka",
    "code": "B16.",
    "name": "Majalengka",
    "url": "https://maps.app.goo.gl/nCxU7hsH1iqxbyXr7",
    "lat": "-6.8382477911071495",
    "lng": "108.23500706442408"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Bekasi",
    "code": "B17.",
    "name": "Bekasi",
    "url": "https://maps.app.goo.gl/bgu9iqL2JbQcKzc88",
    "lat": "-6.268107038467717",
    "lng": "106.9749622797391"
  },
  {
    "province": "Jawa Barat",
    "region": "Kota/Kabupaten Bogor",
    "code": "B18.",
    "name": "Bogor",
    "url": "https://maps.app.goo.gl/PRVCd295zTPts1SP7?g_st=ac",
    "lat": "-6.591707170800572",
    "lng": "106.78621142208179"
  },
  {
    "province": "Jawa Barat",
    "region": "Kabupaten Tasikmalaya",
    "code": "B19.",
    "name": "Singaparna",
    "url": "https://maps.app.goo.gl/nKmCMwSsmq1ph2Ar7",
    "lat": "-7.349641484650128",
    "lng": "108.10886435090546"
  },
  {
    "province": "Jawa Barat",
    "region": "Kabupaten Bogor",
    "code": "B20.",
    "name": "Cibinong",
    "url": "https://maps.app.goo.gl/SguDJjUCH91fS1Fs8",
    "lat": "-6.476643743696853",
    "lng": "106.85504276624377"
  },
  {
    "province": "Jawa Tengah",
    "region": "Kota/Kabupaten Brebes",
    "code": "C1.",
    "name": "Brebes",
    "url": "https://maps.app.goo.gl/VHwoUZaNmAjMq8hB9",
    "lat": "-6.8748910437254525",
    "lng": "109.04325269324289"
  },
  {
    "province": "Jawa Tengah",
    "region": "Kota/Kabupaten Tegal",
    "code": "C2.",
    "name": "Tegal",
    "url": "https://maps.app.goo.gl/94Hj1T8cukbG1qCL8",
    "lat": "-6.877860490096986",
    "lng": "109.13282986259136"
  },
  {
    "province": "Jawa Tengah",
    "region": "Kabupaten Tegal",
    "code": "C3.",
    "name": "Slawi",
    "url": "https://maps.app.goo.gl/D7URbeG3Hcbm5E348",
    "lat": "-6.973715039915537",
    "lng": "109.13265637612179"
  },
  {
    "province": "Jawa Tengah",
    "region": "Kota/Kabupaten Banjar",
    "code": "C4.",
    "name": "Banjar",
    "url": "https://maps.app.goo.gl/p9dVubRGT4RTVS3Z7",
    "lat": "-7.36956221700765",
    "lng": "108.53885497791451"
  },
  {
    "province": "Jawa Tengah",
    "region": "Kota/Kabupaten Cilacap",
    "code": "C5.",
    "name": "Cilacap",
    "url": "https://maps.app.goo.gl/9ZcpUHPLqudkPg2s9?g_st=ac",
    "lat": "-7.703938480189878",
    "lng": "109.02275360676236"
  },
  {
    "province": "Jawa Tengah",
    "region": "Kota/Kabupaten Pekalongan",
    "code": "C6.",
    "name": "Pekalongan",
    "url": "https://share.google/bbJOct3PpXqnTKcjA",
    "lat": "-6.8996435051407285",
    "lng": "109.66405804908604"
  },
  {
    "province": "Jawa Tengah",
    "region": "Kota/Kabupaten Pemalang",
    "code": "C7.",
    "name": "Pemalang",
    "url": "https://maps.app.goo.gl/FwWTJ71h6cARYnyN9?g_st=ic",
    "lat": "-6.896980999336133",
    "lng": "109.3847396644227"
  }
]';

        $data = json_decode($json, true);

        foreach ($data as $item) {
            $province = Province::firstOrCreate(['name' => $item['province']]);
            $region = Region::firstOrCreate([
                'province_id' => $province->id,
                'name' => $item['region'],
            ]);
            $area = Area::firstOrCreate([
                'region_id' => $region->id,
                'name' => $item['name'],
            ]);

            Branch::updateOrCreate(
                ['code_branches' => $item['code']],
                [
                    'areas_id' => $area->id,
                    'branch_name' => $item['name'],
                    'gmaps_url' => $item['url'],
                    'latitude' => $item['lat'] ?: 0,
                    'longitude' => $item['lng'] ?: 0,
                    'is_active' => true,
                    'radius_meters' => 500, 'address' => '-', 'whatsapp' => '08xxxxxxxx', 'instagram' => 'Mindsia',
                ]
            );
        }
    }
}
