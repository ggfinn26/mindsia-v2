<?php

namespace Database\Seeders;

use App\Enums\TestimonialType;
use App\Models\ActivityPhoto;
use App\Models\LandingTestimonial;
use Illuminate\Database\Seeder;

class LandingPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedHomeGallery();
        $this->seedCompanyGallery();
        $this->seedTestimonials();
    }

    private function seedHomeGallery(): void
    {
        $photos = [
            ['file' => 'landing/rakernas-1.webp', 'title' => 'Rakernas MINDSIA 2026', 'caption' => 'Rapat Kerja Nasional MINDSIA — menyatukan visi dan langkah untuk masa depan pendidikan Indonesia.'],
            ['file' => 'landing/rakernas-2.webp', 'title' => 'Rakernas MINDSIA 2026', 'caption' => 'Suasana diskusi dan kolaborasi antar cabang di Rakernas MINDSIA.'],
            ['file' => 'landing/rakernas-3.webp', 'title' => 'Rakernas MINDSIA 2026', 'caption' => 'Delegasi dari seluruh Indonesia berkumpul dalam Rakernas MINDSIA.'],
            ['file' => 'landing/rakernas-4.webp', 'title' => 'Rakernas MINDSIA 2026', 'caption' => 'Presentasi program kerja dan target capaian MINDSIA.'],
            ['file' => 'landing/rakernas-5.webp', 'title' => 'Rakernas MINDSIA 2026', 'caption' => 'Moment kebersamaan seluruh jajaran MINDSIA di Rakernas.'],
            ['file' => 'landing/rakernas-6.webp', 'title' => 'Rakernas MINDSIA 2026', 'caption' => 'Penutupan Rakernas MINDSIA 2026 dengan semangat baru.'],
            ['file' => 'landing/english-camp-1.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'Kegiatan English Camp — belajar bahasa Inggris sambil bersenang-senang.'],
            ['file' => 'landing/english-camp-2.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'Peserta English Camp aktif dalam sesi speaking dan conversation.'],
            ['file' => 'landing/english-camp-3.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'English Camp MINDSIA — meningkatkan kemampuan bahasa Inggris peserta.'],
            ['file' => 'landing/english-camp-4.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'Suasana outdoor activity dalam English Camp MINDSIA.'],
            ['file' => 'landing/english-camp-5.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'Learning by doing — metode praktik langsung di English Camp.'],
            ['file' => 'landing/english-camp-6.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'Group discussion dan teamwork dalam English Camp MINDSIA.'],
            ['file' => 'landing/english-camp-7.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'Peserta English Camp bersemangat mengikuti setiap sesi.'],
            ['file' => 'landing/english-camp-8.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'English Camp MINDSIA — pengalaman belajar yang tak terlupakan.'],
            ['file' => 'landing/english-camp-9.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'Moment kebersamaan peserta English Camp MINDSIA.'],
            ['file' => 'landing/english-camp-preview.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'Sneak peek kegiatan English Camp MINDSIA.'],
            ['file' => 'landing/karawang-farewell.webp', 'title' => 'Farewell Party Karawang', 'caption' => 'Farewell Party cabang Karawang — perpisahan yang penuh kenangan.'],
            ['file' => 'landing/handsign-of-mindsia.webp', 'title' => 'Hands Sign of MINDSIA', 'caption' => 'Hands Sign MINDSIA — identitas dan kebanggaan bersama.'],
        ];

        foreach ($photos as $i => $photo) {
            ActivityPhoto::create([
                'section' => 'home',
                'title' => $photo['title'],
                'caption' => $photo['caption'],
                'storage_path' => $photo['file'],
                'is_active' => true,
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function seedCompanyGallery(): void
    {
        $photos = [
            ['file' => 'landing/rakernas-1.webp', 'title' => 'Rakernas MINDSIA 2026', 'caption' => 'Rapat Kerja Nasional MINDSIA.'],
            ['file' => 'landing/rakernas-2.webp', 'title' => 'Rakernas MINDSIA 2026', 'caption' => 'Kolaborasi antar cabang di Rakernas.'],
            ['file' => 'landing/english-camp-preview.webp', 'title' => 'English Camp MINDSIA', 'caption' => 'Program unggulan English Camp.'],
            ['file' => 'landing/karawang-farewell.webp', 'title' => 'Farewell Party Karawang', 'caption' => 'Kegiatan sosial cabang Karawang.'],
            ['file' => 'landing/handsign-of-mindsia.webp', 'title' => 'Hands Sign of MINDSIA', 'caption' => 'Identitas MINDSIA.'],
        ];

        foreach ($photos as $i => $photo) {
            ActivityPhoto::create([
                'section' => 'company',
                'title' => $photo['title'],
                'caption' => $photo['caption'],
                'storage_path' => $photo['file'],
                'is_active' => true,
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            ['file' => 'landing/testimoni-1.webp', 'sort' => 1],
            ['file' => 'landing/testimoni-2.webp', 'sort' => 2],
            ['file' => 'landing/testimoni-3.webp', 'sort' => 3],
            ['file' => 'landing/testimoni-4.webp', 'sort' => 4],
            ['file' => 'landing/testimoni-5.webp', 'sort' => 5],
            ['file' => 'landing/testimoni-6.webp', 'sort' => 6],
            ['file' => 'landing/testimoni-7.webp', 'sort' => 7],
            ['file' => 'landing/testimoni-8.webp', 'sort' => 8],
            ['file' => 'landing/testimoni-9.webp', 'sort' => 9],
            ['file' => 'landing/testimoni-10.webp', 'sort' => 10],
        ];

        foreach ($testimonials as $t) {
            LandingTestimonial::create([
                'type' => TestimonialType::Image,
                'file_path' => $t['file'],
                'is_active' => true,
                'sort_order' => $t['sort'],
            ]);
        }
    }
}
