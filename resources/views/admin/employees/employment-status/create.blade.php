@extends('layouts.app')

@section('title', 'Buat Kontrak — ' . $employee->full_name)

@section('content')
    <div class="container py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Buat Kontrak</h1>
                <p class="text-gray-600 mt-2">{{ $employee->full_name }}</p>
            </div>
            <a href="{{ route('employees.show', $employee) }}"
               class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <div class="flex" role="tablist">
                    <button type="button"
                            role="tab"
                            aria-selected="true"
                            aria-controls="lengkap-panel"
                            class="tab-button px-6 py-4 font-medium text-gray-700 border-b-2 border-blue-500 active"
                            data-tab="lengkap">
                        <span class="text-lg">📋 Lengkap</span>
                        <p class="text-sm text-gray-500 mt-1">Form lengkap dengan semua detail kontrak</p>
                    </button>
                    <button type="button"
                            role="tab"
                            aria-selected="false"
                            aria-controls="cepat-panel"
                            class="tab-button px-6 py-4 font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700"
                            data-tab="cepat">
                        <span class="text-lg">⚡ Cepat</span>
                        <p class="text-sm text-gray-500 mt-1">Upload file kontrak, detail diisi nanti</p>
                    </button>
                </div>
            </div>

            <!-- Tab Panels -->
            <div class="p-8">
                <!-- Tab: Lengkap (Full Form) -->
                <form id="lengkap-form"
                      action="{{ route('employment-statuses.store', $employee) }}"
                      method="POST"
                      role="tabpanel"
                      aria-labelledby="lengkap-tab"
                      class="tab-panel active space-y-6">
                    @csrf
                    <input type="hidden" name="tab" value="lengkap">

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Type Employment -->
                        <div>
                            <label for="type_employment" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Ketenagakerjaan
                            </label>
                            <input type="text"
                                   id="type_employment"
                                   name="type_employment"
                                   value="{{ old('type_employment') }}"
                                   placeholder="Tetap, Kontrak, Magang"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type_employment') border-red-500 @enderror">
                            @error('type_employment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Join Date -->
                        <div>
                            <label for="join_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai Kerja
                            </label>
                            <input type="date"
                                   id="join_date"
                                   name="join_date"
                                   value="{{ old('join_date') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('join_date') border-red-500 @enderror">
                            @error('join_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contract Start Date -->
                        <div>
                            <label for="contract_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai Kontrak
                            </label>
                            <input type="date"
                                   id="contract_start_date"
                                   name="contract_start_date"
                                   value="{{ old('contract_start_date') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contract_start_date') border-red-500 @enderror">
                            @error('contract_start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contract End Date -->
                        <div>
                            <label for="contract_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Akhir Kontrak (opsional)
                            </label>
                            <input type="date"
                                   id="contract_end_date"
                                   name="contract_end_date"
                                   value="{{ old('contract_end_date') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contract_end_date') border-red-500 @enderror">
                            @error('contract_end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Posisi
                        </label>
                        <select id="position_id"
                                name="position_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('position_id') border-red-500 @enderror">
                            <option value="">-- Pilih Posisi --</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                                    {{ $position->position_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-3 pt-6 border-t">
                        <button type="submit"
                                class="px-6 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600">
                            Buat Kontrak
                        </button>
                        <a href="{{ route('employees.show', $employee) }}"
                           class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>
                    </div>
                </form>

                <!-- Tab: Cepat (Quick Upload) -->
                <form id="cepat-form"
                      action="{{ route('employment-statuses.store', $employee) }}"
                      method="POST"
                      enctype="multipart/form-data"
                      role="tabpanel"
                      aria-labelledby="cepat-tab"
                      class="tab-panel hidden space-y-6">
                    @csrf
                    <input type="hidden" name="tab" value="cepat">

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-blue-800">
                            <strong>Mode Cepat:</strong> Upload file kontrak terlebih dahulu. Detail kontrak dapat dilengkapi kemudian.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Type Employment -->
                        <div>
                            <label for="cepat_type_employment" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Ketenagakerjaan
                            </label>
                            <input type="text"
                                   id="cepat_type_employment"
                                   name="type_employment"
                                   value="{{ old('type_employment') }}"
                                   placeholder="Tetap, Kontrak, Magang"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type_employment') border-red-500 @enderror">
                            @error('type_employment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Join Date -->
                        <div>
                            <label for="cepat_join_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai Kerja
                            </label>
                            <input type="date"
                                   id="cepat_join_date"
                                   name="join_date"
                                   value="{{ old('join_date') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('join_date') border-red-500 @enderror">
                            @error('join_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="cepat_position_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Posisi
                        </label>
                        <select id="cepat_position_id"
                                name="position_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('position_id') border-red-500 @enderror">
                            <option value="">-- Pilih Posisi --</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                                    {{ $position->position_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label for="contract_file_path" class="block text-sm font-medium text-gray-700 mb-2">
                            File Kontrak (PDF / Word)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition">
                            <input type="file"
                                   id="contract_file_path"
                                   name="contract_file_path"
                                   accept=".pdf,.doc,.docx"
                                   class="hidden"
                                   onchange="document.getElementById('file-name').textContent = this.files[0]?.name || 'Pilih file'">
                            <p class="text-gray-600 mb-3">Drag & drop file atau klik untuk memilih</p>
                            <button type="button"
                                    onclick="document.getElementById('contract_file_path').click()"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                Pilih File
                            </button>
                            <p id="file-name" class="text-sm text-gray-500 mt-3">File belum dipilih</p>
                            <p class="text-xs text-gray-400 mt-2">Format: PDF, DOC, DOCX (maks 10 MB)</p>
                        </div>
                        @error('contract_file_path')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-3 pt-6 border-t">
                        <button type="submit"
                                class="px-6 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600">
                            Upload & Buat Kontrak
                        </button>
                        <a href="{{ route('employees.show', $employee) }}"
                           class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                const tabName = button.dataset.tab;

                // Update button states
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-gray-700');
                    btn.classList.add('border-transparent', 'text-gray-500');
                    btn.setAttribute('aria-selected', 'false');
                });
                button.classList.add('active', 'border-blue-500', 'text-gray-700');
                button.classList.remove('border-transparent', 'text-gray-500');
                button.setAttribute('aria-selected', 'true');

                // Show/hide panels
                document.querySelectorAll('.tab-panel').forEach(panel => {
                    panel.classList.add('hidden');
                });
                document.getElementById(tabName + '-form').classList.remove('hidden');
            });
        });
    </script>
@endsection
