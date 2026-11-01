{{-- Isi form penugasan: target (role/posisi/pegawai) + tanggal berlaku. Dipakai di halaman detail dan form buat aturan. --}}
@php($prefix = $prefix ?? '')
                    <input type="hidden" name="{{ $prefix }}assignable_type" :value="type">

                    {{-- Step 1: Pilih target --}}
                    <div class="relative max-w-xs" @click.outside="open = false">
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Berlaku untuk @unless($optional ?? false)<span class="text-red-500">*</span>@endunless
                        </label>
                        <div @click="open = !open"
                             class="w-full px-3.5 py-2.5 rounded-lg text-sm border flex justify-between items-center cursor-pointer transition-all min-h-[42px]"
                             :style="open
                                 ? 'background-color: var(--dash-input-bg); border-color: var(--dash-primary); box-shadow: 0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent);'
                                 : 'background-color: var(--dash-input-bg); border-color: var(--dash-input-border);'">
                            <span class="truncate block pr-2"
                                  :style="type ? 'color: var(--dash-text-heading); font-weight: 500;' : 'color: var(--dash-text-muted);'"
                                  x-text="typeLabel || 'Pilih target...'"></span>
                            <span class="material-symbols-outlined text-[18px] transition-transform duration-200"
                                  style="color: var(--dash-text-muted);"
                                  :class="open ? 'rotate-180' : ''">expand_more</span>
                        </div>
                        <div x-show="open" x-transition
                             class="absolute z-50 w-full mt-1.5 rounded-xl shadow-xl border overflow-hidden"
                             style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                            <ul class="py-1">
                                <template x-for="opt in options" :key="opt.value">
                                    <li @click="pick(opt.value, opt.label)"
                                        class="px-4 py-2.5 text-xs font-semibold cursor-pointer transition-colors"
                                        :style="type === opt.value
                                            ? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text);'
                                            : 'color: var(--dash-text-body);'"
                                        @mouseover="$el.style.backgroundColor = 'var(--dash-nav-hover-bg)'"
                                        @mouseout="$el.style.backgroundColor = type === opt.value ? 'var(--dash-nav-hover-bg)' : 'transparent'"
                                        x-text="opt.label"></li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    {{-- Step 2: Checklist options --}}
                    <template x-if="type === 'role'">
                        <div class="rounded-xl border overflow-hidden"
                             style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);">
                            <div class="flex items-center justify-between px-4 py-2.5 border-b"
                                 style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">Daftar Role</span>
                                <button type="button"
                                        @click="$el.closest('[x-data]').querySelectorAll('input[name=\'{{ $prefix }}assignable_id[]\']').forEach(i => i.checked = true)"
                                        class="text-xs font-semibold hover:underline"
                                        style="color: var(--dash-primary-text);">
                                    Pilih Semua
                                </button>
                            </div>
                            <div class="divide-y max-h-56 overflow-y-auto" style="border-color: var(--dash-card-border);">
                                @foreach($roles as $role)
                                    <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='transparent'">
                                        <input type="checkbox" name="{{ $prefix }}assignable_id[]" value="{{ $role->id }}"
                                               class="w-4 h-4 rounded cursor-pointer"
                                               style="accent-color: var(--dash-primary);">
                                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </template>

                    <template x-if="type === 'position'">
                        <div class="rounded-xl border overflow-hidden"
                             style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);">
                            <div class="flex items-center justify-between px-4 py-2.5 border-b"
                                 style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">Daftar Posisi</span>
                                <button type="button"
                                        @click="$el.closest('[x-data]').querySelectorAll('input[name=\'{{ $prefix }}assignable_id[]\']').forEach(i => i.checked = true)"
                                        class="text-xs font-semibold hover:underline"
                                        style="color: var(--dash-primary-text);">
                                    Pilih Semua
                                </button>
                            </div>
                            <div class="divide-y max-h-56 overflow-y-auto" style="border-color: var(--dash-card-border);">
                                @foreach($positions as $position)
                                    <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='transparent'">
                                        <input type="checkbox" name="{{ $prefix }}assignable_id[]" value="{{ $position->id }}"
                                               class="w-4 h-4 rounded cursor-pointer"
                                               style="accent-color: var(--dash-primary);">
                                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">{{ $position->position_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </template>

                    <template x-if="type === 'employee'">
                        <div class="rounded-xl border overflow-hidden"
                             style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);">
                            <div class="flex items-center justify-between px-4 py-2.5 border-b"
                                 style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">Daftar Pegawai</span>
                                <button type="button"
                                        @click="$el.closest('[x-data]').querySelectorAll('input[name=\'{{ $prefix }}assignable_id[]\']').forEach(i => i.checked = true)"
                                        class="text-xs font-semibold hover:underline"
                                        style="color: var(--dash-primary-text);">
                                    Pilih Semua
                                </button>
                            </div>
                            <div class="divide-y max-h-56 overflow-y-auto" style="border-color: var(--dash-card-border);">
                                @foreach($employees as $employee)
                                    <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='transparent'">
                                        <input type="checkbox" name="{{ $prefix }}assignable_id[]" value="{{ $employee->id }}"
                                               class="w-4 h-4 rounded cursor-pointer"
                                               style="accent-color: var(--dash-primary);">
                                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">{{ $employee->full_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </template>

                    {{-- Step 3: Tanggal Berlaku & Aktif --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                Berlaku Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="{{ $prefix }}effective_start_date" required value="{{ now()->toDateString() }}"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                Berlaku Sampai <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(kosong = selamanya)</span>
                            </label>
                            <input type="date" name="{{ $prefix }}effective_end_date"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                    </div>

                    <div class="pt-1">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="{{ $prefix }}is_active" value="0">
                            <input type="checkbox" name="{{ $prefix }}is_active" id="{{ $prefix }}is_active" value="1" checked
                                   class="w-4 h-4 rounded cursor-pointer"
                                   style="accent-color: var(--dash-primary);">
                            <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">Aktifkan penugasan ini sekarang</span>
                        </label>
                    </div>
