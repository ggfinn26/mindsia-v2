{{-- Camera modal — included inside an x-data="attendanceCamera(...)" wrapper --}}
<div x-show="showModal" x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     @keydown.escape.window="close()">

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/75 transition-opacity" @click="close()"></div>

        <!-- Panel -->
        <div class="relative transform overflow-hidden rounded-xl bg-[var(--dash-card-bg)] text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-sm z-10 border border-[var(--dash-card-border)]">
            <!-- Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-[var(--dash-card-border)]">
                <h3 class="text-sm font-semibold text-[var(--dash-text-heading)]"
                    x-text="actionType === 'check-in' ? 'Foto Selfie Check-In' : 'Foto Selfie Check-Out'"></h3>
                <button type="button" @click="close()" class="text-[var(--dash-text-muted)] hover:text-[var(--dash-text-heading)] transition-colors">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <div class="p-4 space-y-3">
                <!-- Location status -->
                <div class="flex items-center gap-2 text-xs">
                    <template x-if="locationReady">
                        <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                            <span class="material-symbols-outlined text-sm">location_on</span>
                            Lokasi siap
                        </span>
                    </template>
                    <template x-if="!locationReady && !locationError">
                        <span class="flex items-center gap-1 text-amber-600 dark:text-amber-400">
                            <span class="material-symbols-outlined text-sm animate-spin">sync</span>
                            Mendapatkan lokasi...
                        </span>
                    </template>
                    <template x-if="locationError">
                        <span class="flex items-center gap-1 text-rose-600 dark:text-rose-400">
                            <span class="material-symbols-outlined text-sm">location_off</span>
                            <span x-text="locationError"></span>
                        </span>
                    </template>
                </div>

                <!-- Camera view -->
                <div x-show="step === 'camera'" class="relative rounded-lg overflow-hidden bg-black aspect-[4/3]">
                    <video x-ref="cameraVideo" autoplay playsinline muted
                           class="w-full h-full object-cover scale-x-[-1]"></video>
                    <div x-show="error" class="absolute inset-0 flex items-center justify-center bg-black/60 p-4">
                        <p class="text-white text-xs text-center" x-text="error"></p>
                    </div>
                </div>

                <!-- Captured preview -->
                <div x-show="step === 'captured' || step === 'submitting'" class="rounded-lg overflow-hidden bg-black aspect-[4/3]">
                    <img :src="capturedDataUrl" class="w-full h-full object-cover scale-x-[-1]" alt="Foto selfie">
                </div>

                <!-- Submitting spinner overlay -->
                <div x-show="step === 'submitting'" class="flex items-center justify-center gap-2 text-sm text-[var(--dash-text-muted)]">
                    <span class="material-symbols-outlined text-lg animate-spin">sync</span>
                    Menyimpan...
                </div>

                <!-- Canvas (hidden) -->
                <canvas x-ref="cameraCanvas" class="hidden"></canvas>

                <!-- Notes field (captured step) -->
                <div x-show="step === 'captured'">
                    <label class="block text-xs font-medium text-[var(--dash-text-heading)] mb-1">Catatan (opsional)</label>
                    <textarea x-model="notes" rows="2" maxlength="500"
                        class="block w-full rounded-lg border-[var(--dash-input-border)] bg-[var(--dash-input-bg)] text-[var(--dash-text-body)] shadow-sm text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]"
                        placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>

                <!-- Error -->
                <p x-show="error && step !== 'camera'" class="text-xs text-rose-600 dark:text-rose-400" x-text="error"></p>

                <!-- Actions -->
                <div class="flex gap-2">
                    <template x-if="step === 'camera'">
                        <button type="button" @click="capture()" :disabled="!!error"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-[var(--dash-primary)] hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <span class="material-symbols-outlined text-lg mr-1">photo_camera</span>
                            Ambil Foto
                        </button>
                    </template>
                    <template x-if="step === 'captured'">
                        <button type="button" @click="retake()"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-[var(--dash-card-border)] text-sm font-medium rounded-lg text-[var(--dash-text-heading)] bg-transparent hover:bg-[var(--dash-page-bg)] transition-colors">
                            <span class="material-symbols-outlined text-lg mr-1">replay</span>
                            Ulangi
                        </button>
                    </template>
                    <template x-if="step === 'captured'">
                        <button type="button" @click="submit()" :disabled="!locationReady"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <span class="material-symbols-outlined text-lg mr-1">check</span>
                            Kirim
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
