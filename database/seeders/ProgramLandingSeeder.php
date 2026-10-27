<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramLandingSeeder extends Seeder
{
    public function run(): void
    {
        // Fasilitas & keunggulan generik: fitur MINDSIA yang berlaku lintas program.
        $facilities = [
            'Area layanan bahasa Inggris 24 jam (24 HESA)',
            'Sertifikat berstandar nasional dan berlisensi',
            'Kelas bergaransi tanpa biaya tambahan',
            'Pindah kelas dan diskusi lintas cabang',
            'Tutor muda, ramah, dan komunikatif',
            'Jadwal belajar yang fleksibel',
        ];

        $benefits = [
            'Praktik berbahasa aktif sejak pertemuan pertama',
            'Metode belajar fun, interaktif, dan relevan',
            'Laporan hasil penilaian pembelajaran',
            'Komunitas alumni dan English Club',
        ];

        $schedule = [
            ['day' => 'Senin', 'start' => '08:00', 'end' => '10:00'],
            ['day' => 'Rabu', 'start' => '13:00', 'end' => '15:00'],
        ];

        $faqs = [
            ['q' => 'Apakah ada kelas pengulangan?', 'a' => 'Ada. Kelas bergaransi: Anda mendapat kelas pengulangan tanpa biaya tambahan jika target kelas belum tercapai.'],
            ['q' => 'Apakah bisa pindah atau diskusi di cabang lain?', 'a' => 'Bisa. Anda dapat berpindah kelas dan mengikuti diskusi di cabang MINDSIA lain tanpa biaya tambahan.'],
            ['q' => 'Apakah mendapat sertifikat?', 'a' => 'Ya. Anda memperoleh sertifikat berstandar nasional dan berlisensi sesuai ketentuan program.'],
            ['q' => 'Bagaimana cara mendaftar?', 'a' => 'Klik tombol Daftar Sekarang di halaman ini, atau hubungi admin via WhatsApp untuk konsultasi terlebih dahulu.'],
        ];

        $perProgram = [
            'EM' => ['badge' => 'Program Favorit', 'target_level' => 'Active English + TOEFL', 'duration' => '3 Bulan Intensif', 'tagline' => 'Program khusus mahasiswa untuk menguasai TOEFL, Speaking, Listening, Reading, dan Writing hingga fasih dan percaya diri.'],
            'EFT' => ['badge' => 'Untuk Remaja', 'target_level' => 'Pelajar SMP - SMA', 'duration' => 'Reguler', 'tagline' => 'Program bahasa Inggris yang dirancang khusus untuk remaja dengan metode menyenangkan dan interaktif.'],
            'EFP' => ['badge' => 'English Fluency', 'target_level' => 'Percakapan Aktif', 'duration' => 'Reguler', 'tagline' => 'Bangun kelancaran dan kepercayaan diri berbahasa Inggris dalam percakapan sehari-hari.'],
            'TOEFL' => ['badge' => 'Test Preparation', 'target_level' => 'Target Score 500+', 'duration' => '3 Bulan Intensif', 'tagline' => 'Persiapan TOEFL terstruktur: Listening, Structure, dan Reading Comprehension.'],
        ];

        foreach (Program::all() as $program) {
            $extra = $perProgram[$program->program_code] ?? [];

            $program->forceFill([
                'slug' => $program->slug ?: Str::slug($program->program_name),
                'tagline' => $program->getRawOriginal('tagline') ?: ($extra['tagline'] ?? null),
                'badge' => $program->badge ?: ($extra['badge'] ?? 'Program MINDSIA'),
                'target_level' => $program->target_level ?: ($extra['target_level'] ?? null),
                'duration' => $program->duration ?: ($extra['duration'] ?? null),
                'facilities' => $program->facilities ?: $facilities,
                'benefits' => $program->benefits ?: $benefits,
                'schedule' => (! empty($program->schedule) && isset($program->schedule[0]['day'])) ? $program->schedule : $schedule,
                'faqs' => $program->faqs ?: $faqs,
            ])->save();
        }
    }
}
