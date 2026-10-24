@php
    $isEdit = isset($policy);
    $scopeVal = old('attendance_scope', $policy->attendance_scope ?? 'branch');
    $exemptVal = (bool) old('is_attendance_exempt', $policy->is_attendance_exempt ?? false);
    $work = $policy->workScheduleConfig ?? null;
    $session = $policy->sessionConfig ?? null;
@endphp

<div class="rounded-2xl"
     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

    <div class="px-6 py-4 border-b flex items-center gap-2.5" style="border-color: var(--dash-card-border);">
        <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
        <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">{{ $isEdit ? 'Ubah Kebijakan' : 'Kebijakan Baru' }}</h3>
    </div>

    <form method="POST"
          action="{{ $isEdit ? route('attendance-policies.update', $policy) : route('attendance-policies.store') }}"
          x-data="{ scope: '{{ $scopeVal }}', exempt: {{ $exemptVal ? 'true' : 'false' }} }"
          class="px-6 py-6 space-y-6">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Nama --}}
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide mb-2" style="color: var(--dash-text-muted);">Nama Kebijakan</label>
            <input type="text" name="policy_name" value="{{ old('policy_name', $policy->policy_name ?? '') }}"
                   placeholder="Mis. Kebijakan Cabang Kuningan" required
                   class="w-full px-3 py-2 rounded-lg text-sm"
                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
        </div>

        {{-- Cakupan --}}
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide mb-2" style="color: var(--dash-text-muted);">Cakupan</label>
            <div class="flex flex-col sm:flex-row gap-3">
                <select name="attendance_scope" x-model="scope"
                        class="sm:w-40 px-3 py-2 rounded-lg text-sm"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    <option value="branch">Cabang</option>
                    <option value="area">Area</option>
                    <option value="region">Region</option>
                </select>

                <select name="branch_id" x-show="scope === 'branch'"
                        class="flex-1 px-3 py-2 rounded-lg text-sm"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    <option value="">Pilih cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" @selected((int) old('branch_id', $policy->branch_id ?? 0) === $b->id)>{{ $b->branch_name }}</option>
                    @endforeach
                </select>

                <select name="area_id" x-show="scope === 'area'" x-cloak
                        class="flex-1 px-3 py-2 rounded-lg text-sm"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    <option value="">Pilih area</option>
                    @foreach($areas as $a)
                        <option value="{{ $a->id }}" @selected((int) old('area_id', $policy->area_id ?? 0) === $a->id)>{{ $a->name }}</option>
                    @endforeach
                </select>

                <select name="region_id" x-show="scope === 'region'" x-cloak
                        class="flex-1 px-3 py-2 rounded-lg text-sm"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    <option value="">Pilih region</option>
                    @foreach($regions as $r)
                        <option value="{{ $r->id }}" @selected((int) old('region_id', $policy->region_id ?? 0) === $r->id)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Pengecualian --}}
        <div>
            <label class="flex items-center justify-between gap-4 p-4 rounded-xl cursor-pointer" style="border: 1px solid var(--dash-card-border);">
                <div>
                    <p class="text-sm font-medium" style="color: var(--dash-text-heading);">Bebas dari absensi</p>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Pegawai dalam cakupan ini tidak wajib absen</p>
                </div>
                <input type="hidden" name="is_attendance_exempt" value="0">
                <input type="checkbox" name="is_attendance_exempt" value="1" x-model="exempt" class="w-4 h-4 rounded">
            </label>
            <div x-show="exempt" x-cloak class="mt-3">
                <input type="text" name="exemption_reason" value="{{ old('exemption_reason', $policy->exemption_reason ?? '') }}"
                       placeholder="Alasan pengecualian"
                       class="w-full px-3 py-2 rounded-lg text-sm"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
            </div>
        </div>

        {{-- Absensi Jadwal Kerja --}}
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--dash-text-muted);">Absensi Jadwal Kerja</p>
            <div class="space-y-3">
                <label class="flex items-center justify-between gap-4 p-4 rounded-xl cursor-pointer" style="border: 1px solid var(--dash-card-border);">
                    <p class="text-sm font-medium" style="color: var(--dash-text-heading);">Wajib absen manual</p>
                    <input type="hidden" name="work_is_required" value="0">
                    <input type="checkbox" name="work_is_required" value="1" {{ old('work_is_required', $work->is_required ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded">
                </label>
                <div class="flex items-center gap-3">
                    <input type="number" name="work_late_tolerance_minutes" min="0" value="{{ old('work_late_tolerance_minutes', $work->late_tolerance_minutes ?? 0) }}"
                           class="w-24 px-3 py-2 rounded-lg text-sm" style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    <span class="text-sm" style="color: var(--dash-text-muted);">menit toleransi telat masuk</span>
                </div>
                <div class="flex items-center gap-3">
                    <input type="number" name="work_early_leave_tolerance_minutes" min="0" value="{{ old('work_early_leave_tolerance_minutes', $work->early_leave_tolerance_minutes ?? 0) }}"
                           class="w-24 px-3 py-2 rounded-lg text-sm" style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    <span class="text-sm" style="color: var(--dash-text-muted);">menit toleransi pulang lebih awal</span>
                </div>
            </div>
        </div>

        {{-- Absensi Sesi --}}
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--dash-text-muted);">Absensi Sesi Kelas</p>
            <div class="space-y-3">
                <label class="flex items-center justify-between gap-4 p-4 rounded-xl cursor-pointer" style="border: 1px solid var(--dash-card-border);">
                    <p class="text-sm font-medium" style="color: var(--dash-text-heading);">Wajib absen sesi</p>
                    <input type="hidden" name="session_is_required" value="0">
                    <input type="checkbox" name="session_is_required" value="1" {{ old('session_is_required', $session->is_required ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded">
                </label>
                <div class="flex items-center gap-3">
                    <input type="number" name="session_late_tolerance_minutes" min="0" value="{{ old('session_late_tolerance_minutes', $session->late_tolerance_minutes ?? 0) }}"
                           class="w-24 px-3 py-2 rounded-lg text-sm" style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    <span class="text-sm" style="color: var(--dash-text-muted);">menit toleransi telat sesi</span>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <label class="flex items-center justify-between gap-4 p-4 rounded-xl cursor-pointer" style="border: 1px solid var(--dash-card-border);">
            <div>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">Aktif</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Nonaktifkan untuk menyimpan tanpa menerapkan</p>
            </div>
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $policy->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded">
        </label>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);"><span class="material-symbols-outlined text-[16px]">save</span><span>Simpan</span></button>
            <a href="{{ route('attendance-policies.index') }}"
               class="inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium transition-colors"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"><span class="material-symbols-outlined text-[18px]">close</span></a>
        </div>
    </form>
</div>
