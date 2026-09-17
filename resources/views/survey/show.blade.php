@extends('layouts.dashboard')

@section('title', 'Detail Survey')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
    x-data="{
        activeTab: 'detail',
        showAssignModal: false,
        assignType: 'member',
        selectedIds: [],
        searchQuery: ''
    }">

    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('surveys.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Daftar Survey
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $survey->survey_name }}</h1>
        </div>
        <div class="flex gap-2">
            @can('survey.form.update')
                <a href="{{ route('surveys.edit', $survey) }}" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                </a>
            @endcan
            @can('survey.assignment.create')
                <button type="button" @click="showAssignModal = true; assignType = 'member'; selectedIds = []; searchQuery = ''" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                    <span class="material-symbols-outlined text-[18px]">person_add</span> Assign
                </button>
            @endcan
        </div>
    </div>

    <!-- Survey Info Card -->
    <div class="bg-white shadow sm:rounded-lg border border-gray-200 mb-6">
        <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Batas Waktu</p>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $survey->deadline_at ? $survey->deadline_at->format('d M Y H:i') : 'Tanpa Batas' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Status</p>
                    <p class="mt-1">
                        @if($survey->isExpired())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Berakhir</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Deskripsi</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $survey->survey_description ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button type="button" @click="activeTab = 'detail'" :class="activeTab === 'detail' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Pertanyaan</button>
            <button type="button" @click="activeTab = 'members'" :class="activeTab === 'members' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Member ({{ $survey->memberSurveys->count() }})</button>
            <button type="button" @click="activeTab = 'employees'" :class="activeTab === 'employees' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Pegawai ({{ $survey->employeeSurveys->count() }})</button>
        </nav>
    </div>

    <!-- Tab: Detail / Questions -->
    <div x-show="activeTab === 'detail'" x-transition>
        <div class="space-y-4">
            @forelse($survey->questions as $qi => $question)
                <div class="bg-white shadow sm:rounded-lg border border-gray-200">
                    <div class="px-6 py-4">
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">{{ $qi + 1 }}</span>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $question->question_text }}</p>
                                <span class="inline-flex mt-1 px-2 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    @if($question->question_type === 'single_choice') Pilihan Tunggal
                                    @elseif($question->question_type === 'multiple_choice') Pilihan Ganda
                                    @elseif($question->question_type === 'scale') Skala ({{ $question->scale_min }}–{{ $question->scale_max }})
                                    @else Teks Bebas
                                    @endif
                                </span>

                                @if($question->question_type === 'scale')
                                    <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                                        <span>{{ $question->scale_min_label ?: $question->scale_min }}</span>
                                        <div class="flex-1 border-t border-dashed border-gray-300"></div>
                                        <span>{{ $question->scale_max_label ?: $question->scale_max }}</span>
                                    </div>
                                @endif

                                @if($question->choices->isNotEmpty())
                                    <div class="mt-2 space-y-1">
                                        @foreach($question->choices as $ci => $choice)
                                            <p class="text-xs text-gray-600">
                                                <span class="font-medium text-gray-500">{{ chr(65 + $ci) }}.</span> {{ $choice->choice_text }}
                                            </p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-8">Belum ada pertanyaan.</p>
            @endforelse
        </div>
    </div>

    <!-- Tab: Members -->
    <div x-show="activeTab === 'members'" x-transition>
        <div class="bg-white shadow sm:rounded-lg border border-gray-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">Nama Member</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-200">
                        @forelse($survey->memberSurveys as $msi => $memberSurvey)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $msi + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $memberSurvey->member?->full_name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($memberSurvey->isSubmitted())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Sudah Isi</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Isi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada member yang di-assign.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab: Employees -->
    <div x-show="activeTab === 'employees'" x-transition>
        <div class="bg-white shadow sm:rounded-lg border border-gray-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">Nama Pegawai</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-200">
                        @forelse($survey->employeeSurveys as $esi => $employeeSurvey)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $esi + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $employeeSurvey->employee?->full_name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($employeeSurvey->isSubmitted())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Sudah Isi</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Isi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada pegawai yang di-assign.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assign Modal -->
    <div x-show="showAssignModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showAssignModal" @click="showAssignModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div x-show="showAssignModal" class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-6 pt-5 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Assign Survey</h3>
                        <button type="button" @click="showAssignModal = false" class="text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Assign Type Toggle -->
                    <div class="flex gap-2 mb-4">
                        <button type="button" @click="assignType = 'member'; selectedIds = []; searchQuery = ''" :class="assignType === 'member' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium">Member</button>
                        <button type="button" @click="assignType = 'employee'; selectedIds = []; searchQuery = ''" :class="assignType === 'employee' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium">Pegawai</button>
                    </div>

                    <!-- Search -->
                    <input type="text" x-model="searchQuery" placeholder="Cari nama..." class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm mb-4 focus:border-indigo-500 focus:ring-indigo-500">

                    <!-- Select All -->
                    <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-200">
                        <input type="checkbox" id="selectAll" @change="
                            const items = assignType === 'member' ? {{ $members->pluck('id')->toJson() }} : {{ $employees->pluck('id')->toJson() }};
                            selectedIds = $event.target.checked ? items : [];
                        " :checked="selectedIds.length > 0 && (assignType === 'member' ? selectedIds.length === {{ $members->count() }} : selectedIds.length === {{ $employees->count() }})" class="border-gray-300 rounded text-indigo-600 focus:ring-indigo-500">
                        <label for="selectAll" class="text-sm font-medium text-gray-700">Pilih Semua</label>
                    </div>

                    <!-- List -->
                    <div class="max-h-64 overflow-y-auto space-y-1">
                        <template x-if="assignType === 'member'">
                            <div>
                                @foreach($members as $member)
                                    <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 cursor-pointer" x-show="!searchQuery || '{{ strtolower($member->full_name) }}'.includes(searchQuery.toLowerCase())">
                                        <input type="checkbox" value="{{ $member->id }}" x-model.number="selectedIds" class="border-gray-300 rounded text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">{{ $member->full_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </template>
                        <template x-if="assignType === 'employee'">
                            <div>
                                @foreach($employees as $employee)
                                    <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 cursor-pointer" x-show="!searchQuery || '{{ strtolower($employee->full_name) }}'.includes(searchQuery.toLowerCase())">
                                        <input type="checkbox" value="{{ $employee->id }}" x-model.number="selectedIds" class="border-gray-300 rounded text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">{{ $employee->full_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </template>
                    </div>

                    <p class="mt-3 text-xs text-gray-500"><span x-text="selectedIds.length"></span> dipilih</p>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex justify-end gap-3">
                    <button type="button" @click="showAssignModal = false" class="border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md">Batal</button>
                    <button type="button" @click="$refs.assignForm.submit()" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <span class="material-symbols-outlined text-[18px]">send</span> Assign
                    </button>
                </div>

                <!-- Hidden form for member assign -->
                <form x-ref="assignForm" x-show="false" :action="assignType === 'member' ? '{{ route('surveys.assign-members', $survey) }}' : '{{ route('surveys.assign-employees', $survey) }}'" method="POST">
                    @csrf
                    <template x-for="id in selectedIds">
                        <div>
                            <input type="hidden" :name="assignType === 'member' ? 'member_ids[]' : 'employee_ids[]'" :value="id">
                        </div>
                    </template>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
