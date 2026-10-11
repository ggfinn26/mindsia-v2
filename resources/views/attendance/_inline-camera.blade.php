{{-- Inline camera section — must be inside an attendanceCamera() x-data scope --}}
{{-- Shown/hidden via showModal state, triggered by open() --}}

{{-- Location status --}}
<div class="flex items-center gap-1.5 text-xs mb-3">
    <template x-if="locationReady">
        <span class="flex items-center gap-1" style="color: #059669;">
            <span class="material-symbols-outlined text-sm">location_on</span>
            Lokasi siap
        </span>
    </template>
    <template x-if="!locationReady && !locationError">
        <span class="flex items-center gap-1" style="color: var(--dash-text-muted);">
            <span class="material-symbols-outlined text-sm">sync</span>
            Mendapatkan lokasi...
        </span>
    </template>
    <template x-if="locationError">
        <span class="flex items-center gap-1" style="color: #dc2626;">
            <span class="material-symbols-outlined text-sm">location_off</span>
            <span x-text="locationError"></span>
        </span>
    </template>
</div>

{{-- Camera view --}}
<div x-show="step === 'camera'" class="relative rounded-xl overflow-hidden bg-black" style="aspect-ratio: 4/3;">
    <video x-ref="cameraVideo" autoplay playsinline muted
           class="w-full h-full object-cover scale-x-[-1]"></video>
    <div x-show="error" class="absolute inset-0 flex items-center justify-center bg-black/60 p-4">
        <p class="text-white text-xs text-center" x-text="error"></p>
    </div>
</div>

{{-- Captured preview --}}
<div x-show="step === 'captured' || step === 'submitting'"
     class="rounded-xl overflow-hidden bg-black" style="aspect-ratio: 4/3;">
    <img :src="capturedDataUrl" class="w-full h-full object-cover scale-x-[-1]" alt="Foto selfie">
</div>

<canvas x-ref="cameraCanvas" class="hidden"></canvas>

{{-- Notes --}}
<div x-show="step === 'captured'" class="mt-3">
    <textarea x-model="notes" rows="2" maxlength="500"
              class="block w-full rounded-xl text-sm px-3 py-2 focus:outline-none resize-none"
              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);"
              placeholder="Catatan opsional..."></textarea>
</div>

{{-- Error --}}
<p x-show="error && step !== 'camera'" x-text="error"
   class="text-xs mt-2" style="color: #dc2626;"></p>

{{-- Submitting --}}
<div x-show="step === 'submitting'"
     class="flex items-center gap-2 mt-3 text-sm" style="color: var(--dash-text-muted);">
    <span class="material-symbols-outlined text-base animate-spin">sync</span>
    Menyimpan...
</div>

{{-- Buttons --}}
<div class="flex gap-2 mt-4">
    <template x-if="step === 'camera'">
        <button type="button" @click="capture()" :disabled="!!error"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none bg-[var(--dash-primary)] text-white hover:opacity-90 border border-transparent shadow-sm flex-1 active:opacity-80 disabled:opacity-40 disabled:cursor-not-allowed"
                style="background-color: var(--dash-primary);"><span class="material-symbols-outlined text-[16px]">photo_camera</span> <span>Ambil Foto</span></button>
    </template>
    <template x-if="step === 'captured'">
        <button type="button" @click="retake()"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold transition-colors focus:outline-none flex-1"
                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[16px]">replay</span>
            <span>Ulangi</span>
        </button>
    </template>
    <template x-if="step === 'captured'">
        <button type="button" @click="submit()" :disabled="!locationReady"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none bg-[var(--dash-primary)] text-white hover:opacity-90 border border-transparent shadow-sm flex-1 hover: disabled:opacity-40 disabled:cursor-not-allowed"><span class="material-symbols-outlined text-[16px]">check</span> <span>Kirim</span></button>
    </template>
</div>
