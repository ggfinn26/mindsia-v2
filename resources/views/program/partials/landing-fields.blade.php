@php
    $p = $program ?? null;
    $landingInit = [
        'tagline' => old('tagline', $p?->getRawOriginal('tagline') ?? ''),
        'badge' => old('badge', $p?->badge ?? ''),
        'target_level' => old('target_level', $p?->target_level ?? ''),
        'duration' => old('duration', $p?->duration ?? ''),
        'facilities' => array_values(old('facilities', $p?->facilities ?? [])),
        'benefits' => array_values(old('benefits', $p?->benefits ?? [])),
        'schedule' => array_values(old('schedule', $p?->schedule ?? [])),
        'faqs' => array_values(old('faqs', $p?->faqs ?? [])),
    ];
    // Hanya hari utama: kelas membatasi day_of_week ke PRIMARY_DAYS (pasangan hari kedua otomatis).
    $dayOptions = \App\Models\ClassRoom::PRIMARY_DAYS;
@endphp

<div class="rounded-2xl overflow-hidden mt-6" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
     x-data="@js($landingInit)">
    <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
        <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Konten Halaman Program &amp; Jadwal</h2>
        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Tampil di halaman publik /program. Jadwal dipakai saat membuat kelas.</p>
    </div>
    <div class="px-6 py-5 space-y-6">

        {{-- Teks ringkas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Tagline <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(Opsional)</span></label>
                <input type="text" name="tagline" x-model="tagline" placeholder="Kalimat singkat penarik perhatian"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Badge <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(Opsional)</span></label>
                <input type="text" name="badge" x-model="badge" placeholder="Mis. Program Favorit"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Target / Level <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(Opsional)</span></label>
                <input type="text" name="target_level" x-model="target_level" placeholder="Mis. Target Score 500+"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Durasi <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(Opsional)</span></label>
                <input type="text" name="duration" x-model="duration" placeholder="Mis. 3 Bulan Intensif"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'" onblur="this.style.borderColor='var(--dash-input-border)'" />
            </div>
        </div>

        {{-- Jadwal multi-slot --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium" style="color: var(--dash-text-body);">Jadwal Program</label>
                <button type="button" @click="schedule.push({ day: '', start: '', end: '' })"
                        class="inline-flex items-center gap-1 text-xs font-semibold" style="color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">add</span>Tambah Slot
                </button>
            </div>
            <p class="text-xs mb-3" style="color: var(--dash-text-muted);">Satu slot = satu hari + jam. Dipakai saat membuat kelas. Jika cabang punya jadwal berbeda, buat program varian.</p>
            <template x-if="schedule.length === 0">
                <p class="text-xs italic" style="color: var(--dash-text-faint);">Belum ada slot jadwal.</p>
            </template>
            <div class="space-y-3">
                <template x-for="(s, i) in schedule" :key="i">
                    <div class="grid grid-cols-[1fr_auto_auto_auto] gap-3 items-end p-3 rounded-xl" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <div>
                            <span class="block text-[11px] mb-1" style="color: var(--dash-text-muted);">Hari</span>
                            <select :name="`schedule[${i}][day]`" x-model="s.day"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                                <option value="">Pilih hari</option>
                                @foreach($dayOptions as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <span class="block text-[11px] mb-1" style="color: var(--dash-text-muted);">Mulai</span>
                            <input type="time" :name="`schedule[${i}][start]`" x-model="s.start"
                                   class="px-3 py-2 text-sm rounded-lg focus:outline-none"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);" />
                        </div>
                        <div>
                            <span class="block text-[11px] mb-1" style="color: var(--dash-text-muted);">Selesai</span>
                            <input type="time" :name="`schedule[${i}][end]`" x-model="s.end"
                                   class="px-3 py-2 text-sm rounded-lg focus:outline-none"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);" />
                        </div>
                        <button type="button" @click="schedule.splice(i, 1)" title="Hapus slot"
                                class="w-9 h-9 rounded-lg flex items-center justify-center text-white" style="background-color: #dc2626;">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Fasilitas --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium" style="color: var(--dash-text-body);">Fasilitas</label>
                <button type="button" @click="facilities.push('')" class="inline-flex items-center gap-1 text-xs font-semibold" style="color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">add</span>Tambah
                </button>
            </div>
            <div class="space-y-2">
                <template x-for="(f, i) in facilities" :key="i">
                    <div class="flex gap-2">
                        <input type="text" :name="`facilities[${i}]`" x-model="facilities[i]" placeholder="Mis. Sertifikat berlisensi"
                               class="flex-1 px-4 py-2 text-sm rounded-lg focus:outline-none"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);" />
                        <button type="button" @click="facilities.splice(i, 1)" class="w-9 h-9 shrink-0 rounded-lg flex items-center justify-center text-white" style="background-color: #dc2626;">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Keunggulan --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium" style="color: var(--dash-text-body);">Keunggulan</label>
                <button type="button" @click="benefits.push('')" class="inline-flex items-center gap-1 text-xs font-semibold" style="color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">add</span>Tambah
                </button>
            </div>
            <div class="space-y-2">
                <template x-for="(b, i) in benefits" :key="i">
                    <div class="flex gap-2">
                        <input type="text" :name="`benefits[${i}]`" x-model="benefits[i]" placeholder="Mis. Metode belajar interaktif"
                               class="flex-1 px-4 py-2 text-sm rounded-lg focus:outline-none"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);" />
                        <button type="button" @click="benefits.splice(i, 1)" class="w-9 h-9 shrink-0 rounded-lg flex items-center justify-center text-white" style="background-color: #dc2626;">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- FAQ --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium" style="color: var(--dash-text-body);">FAQ</label>
                <button type="button" @click="faqs.push({ q: '', a: '' })" class="inline-flex items-center gap-1 text-xs font-semibold" style="color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">add</span>Tambah
                </button>
            </div>
            <div class="space-y-3">
                <template x-for="(fq, i) in faqs" :key="i">
                    <div class="p-3 rounded-xl space-y-2" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <div class="flex gap-2">
                            <input type="text" :name="`faqs[${i}][q]`" x-model="fq.q" placeholder="Pertanyaan"
                                   class="flex-1 px-4 py-2 text-sm rounded-lg focus:outline-none"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);" />
                            <button type="button" @click="faqs.splice(i, 1)" class="w-9 h-9 shrink-0 rounded-lg flex items-center justify-center text-white" style="background-color: #dc2626;">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                        <textarea :name="`faqs[${i}][a]`" x-model="fq.a" rows="2" placeholder="Jawaban"
                                  class="w-full px-4 py-2 text-sm rounded-lg focus:outline-none"
                                  style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"></textarea>
                    </div>
                </template>
            </div>
        </div>

        {{-- Galeri Suasana Kelas (per program) --}}
        <div>
            <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Galeri Suasana Kelas</label>
            <p class="text-xs mb-3" style="color: var(--dash-text-muted);">Foto khusus program ini, tampil di bagian "Suasana Kelas &amp; Komunitas Belajar".</p>

            @if($p && ! empty($p->gallery))
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-3">
                    @foreach($p->gallery as $path)
                        <label class="relative block rounded-xl overflow-hidden cursor-pointer group" style="border: 1px solid var(--dash-card-border);">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($path) }}" alt="Foto galeri" class="w-full h-24 object-cover">
                            <span class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors"></span>
                            <span class="absolute top-1.5 right-1.5 flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold text-white" style="background-color: #dc2626;">
                                <input type="checkbox" name="remove_gallery[]" value="{{ $path }}" class="rounded">
                                Hapus
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif

            <input type="file" name="gallery_images[]" multiple accept="image/*"
                   class="w-full px-4 py-2 text-sm rounded-lg file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold"
                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);" />
            <p class="text-xs mt-1.5" style="color: var(--dash-text-muted);">Bisa pilih beberapa foto sekaligus. Centang foto di atas untuk menghapusnya saat menyimpan.</p>
            @error('gallery_images.*')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

    </div>
</div>
