@extends('layouts.dashboard')

@section('title', 'Kuota Cabang — ' . $program->program_name)

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ showForm: false, editingQuota: null }">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kuota Cabang</h1>
            <p class="text-sm text-gray-500 mt-1">Program: {{ $program->program_name }}</p>
        </div>
        <div class="flex gap-3">
            <button type="button" @click="showForm = true; editingQuota = null"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                Set Kuota
            </button>
            <a href="{{ route('programs.show', $program) }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if($quotas->isEmpty())
            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                Belum ada kuota yang diset untuk program ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuota</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($quotas as $quota)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $quota->branch->branch_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $quota->quota_limit }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button type="button"
                                            @click="showForm = true; editingQuota = {{ json_encode(['id' => $quota->id, 'branch_id' => $quota->branch_id, 'quota_limit' => $quota->quota_limit]) }}"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                    <form action="{{ route('quotas.destroy', [$program, $quota]) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus kuota ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Add/Edit Quota Modal -->
    <div x-show="showForm" class="relative z-10" style="display: none;">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showForm = false"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4" x-text="editingQuota ? 'Edit Kuota' : 'Set Kuota Cabang'"></h3>

                        <form id="quota-form" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
                                <select id="quota-branch" name="branch_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kuota (jumlah peserta)</label>
                                <input type="number" id="quota-limit" name="quota_limit" required min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="button" @click="showForm = false"
                                        class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.effect(() => {
            const data = Alpine.raw(document.querySelector('[x-data]').__x.$data);
            if (data.editingQuota) {
                document.getElementById('quota-branch').value = data.editingQuota.branch_id;
                document.getElementById('quota-limit').value = data.editingQuota.quota_limit;
                document.getElementById('quota-branch').disabled = true;
                document.getElementById('quota-form').action = '{{ url("programs") }}/{{ $program->id }}/quotas/' + data.editingQuota.id;
                document.getElementById('quota-form').method = 'POST';
                // Add _method=PUT for update
                let methodInput = document.getElementById('quota-form').querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    document.getElementById('quota-form').appendChild(methodInput);
                }
                methodInput.value = 'PUT';
            } else {
                document.getElementById('quota-branch').value = '';
                document.getElementById('quota-limit').value = '';
                document.getElementById('quota-branch').disabled = false;
                document.getElementById('quota-form').action = '{{ route("programs.quotas.store", $program) }}';
                let methodInput = document.getElementById('quota-form').querySelector('input[name="_method"]');
                if (methodInput) methodInput.remove();
            }
        });
    });
</script>
@endsection
