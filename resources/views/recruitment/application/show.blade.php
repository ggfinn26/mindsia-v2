@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto" x-data="{
    showRejectModal: false,
    showPsikotestModal: false,
    showInterviewModal: false,
}">

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Aplikasi Lowongan</h1>
        <a href="{{ route('recruitment.application.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Sidebar: Profil Pelamar --}}
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 md:col-span-1">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Profil Pelamar</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Nama Lengkap</h3>
                    <p class="text-gray-900 font-medium">{{ $jobApplication->applicant->full_name ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Email</h3>
                    <p class="text-gray-900 font-medium">{{ $jobApplication->applicant->email ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">WhatsApp</h3>
                    <p class="text-gray-900 font-medium">
                        {{ $jobApplication->applicant->whatsapp_number ? '+62'.$jobApplication->applicant->whatsapp_number : '-' }}
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Tanggal Lamar</h3>
                    <p class="text-gray-900 font-medium">
                        {{ $jobApplication->applied_at?->format('d M Y H:i') ?? '-' }}
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Sumber</h3>
                    <p class="text-gray-900 font-medium capitalize">{{ str_replace('_', ' ', $jobApplication->application_source) }}</p>
                </div>
                @if($jobApplication->applicant?->educations?->count())
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-2">Pendidikan</h3>
                        @foreach($jobApplication->applicant->educations as $edu)
                            <p class="text-sm text-gray-700">{{ $edu->institution_name }} — {{ $edu->major ?? '-' }} ({{ $edu->graduation_year ?? '-' }})</p>
                        @endforeach
                    </div>
                @endif
                @if($jobApplication->applicant?->workExperiences?->count())
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-2">Pengalaman Kerja</h3>
                        @foreach($jobApplication->applicant->workExperiences as $exp)
                            <p class="text-sm text-gray-700">{{ $exp->company_name }} — {{ $exp->position }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Main: Tahapan --}}
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 md:col-span-2">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-1">Progres Recruitment</h2>
                    <p class="text-sm text-gray-500">Posisi: <span class="font-semibold text-gray-700">{{ $jobApplication->posting->title ?? 'Reserve/Manual' }}</span></p>
                </div>
                @php
                    $statusColors = [
                        'applied'   => 'bg-gray-100 text-gray-800',
                        'screening' => 'bg-blue-100 text-blue-800',
                        'interview' => 'bg-yellow-100 text-yellow-800',
                        'offering'  => 'bg-purple-100 text-purple-800',
                        'hired'     => 'bg-green-100 text-green-800',
                        'rejected'  => 'bg-red-100 text-red-800',
                    ];
                    $statusColor = $statusColors[$jobApplication->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColor }}">
                    {{ Str::title($jobApplication->status) }}
                </span>
            </div>

            {{-- Timeline stages --}}
            <div class="relative border-l-2 border-indigo-200 ml-3 mb-8">
                @forelse($jobApplication->stages as $stage)
                    <div class="mb-6 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 {{ $stage->result === 'rejected' ? 'bg-red-400' : 'bg-indigo-500' }} rounded-full -left-3 ring-4 ring-white"></span>
                        <h3 class="font-bold text-gray-800 capitalize">{{ str_replace('_', ' ', $stage->stage_name) }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $stage->created_at->format('d M Y H:i') }}</p>
                        @if($stage->notes)
                            <p class="text-sm text-gray-600 mt-1">{{ $stage->notes }}</p>
                        @endif
                    </div>
                @empty
                    <div class="mb-6 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-200 rounded-full -left-3 ring-4 ring-white"></span>
                        <p class="text-sm text-gray-500">Belum ada tahapan tercatat.</p>
                    </div>
                @endforelse
            </div>

            {{-- Psikotest info (jika sudah ada) --}}
            @if($jobApplication->psikotest)
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h3 class="font-semibold text-blue-800 mb-2">Psikotest</h3>
                    <p class="text-sm text-blue-700">
                        Tanggal: {{ $jobApplication->psikotest->psikotest_date ? \Carbon\Carbon::parse($jobApplication->psikotest->psikotest_date)->format('d M Y') : '-' }}
                        @if($jobApplication->psikotest->psikotest_link)
                            | <a href="{{ $jobApplication->psikotest->psikotest_link }}" target="_blank" class="underline">Link Tes</a>
                        @endif
                    </p>
                </div>
            @endif

            {{-- Interview schedules (jika ada) --}}
            @if($jobApplication->interviewSchedules->count())
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-3">Jadwal Interview</h3>
                    @foreach($jobApplication->interviewSchedules as $schedule)
                        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200 mb-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-yellow-800">
                                        {{ \Carbon\Carbon::parse($schedule->scheduled_at)->format('d M Y H:i') }}
                                        ({{ Str::title($schedule->interview_type) }})
                                    </p>
                                    @if($schedule->location)
                                        <p class="text-xs text-yellow-700 mt-1">Lokasi: {{ $schedule->location }}</p>
                                    @endif
                                    @if($schedule->meeting_link)
                                        <p class="text-xs text-yellow-700 mt-1">Link: <a href="{{ $schedule->meeting_link }}" target="_blank" class="underline">Meeting</a></p>
                                    @endif
                                    @if($schedule->interviewer)
                                        <p class="text-xs text-yellow-700 mt-1">Interviewer: {{ $schedule->interviewer->full_name }}</p>
                                    @endif
                                </div>
                                @if(!$schedule->evaluation)
                                    @can('recruitment.interview.delete')
                                        <form action="{{ route('recruitment.interviews.destroy', $schedule) }}" method="POST" class="ml-2" onsubmit="return confirm('Hapus jadwal interview ini?')"  >
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                            @if($schedule->evaluation)
                                <div class="mt-3 pt-3 border-t border-yellow-300">
                                    <p class="text-xs font-semibold text-yellow-800">Evaluasi: Score {{ $schedule->evaluation->total_score }}/20 — {{ Str::title($schedule->evaluation->decision) }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Action buttons --}}
            @if(!in_array($jobApplication->status, ['hired', 'rejected']))
                <div class="mt-6 pt-6 border-t border-gray-200 flex flex-wrap justify-end gap-3">

                    {{-- Reject --}}
                    @can('recruitment.job_application.review')
                        <button type="button" @click="showRejectModal = true" class="btn bg-red-500 hover:bg-red-600 text-white">
                            Reject Pelamar
                        </button>
                    @endcan

                    {{-- Advance: applied → psikotest --}}
                    @if($jobApplication->status === 'applied')
                        @can('recruitment.psikotest.create')
                            <button type="button" @click="showPsikotestModal = true" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                                Mulai Psikotest
                            </button>
                        @endcan
                    @endif

                    {{-- Advance: screening → interview --}}
                    @if($jobApplication->status === 'screening')
                        @can('recruitment.interview.create')
                            <button type="button" @click="showInterviewModal = true" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                                Jadwalkan Interview
                            </button>
                        @endcan
                    @endif

                    {{-- Interview: sudah ada jadwal, bisa tambah lagi --}}
                    @if($jobApplication->status === 'interview')
                        @can('recruitment.interview.create')
                            <button type="button" @click="showInterviewModal = true" class="btn bg-yellow-500 hover:bg-yellow-600 text-white">
                                + Jadwal Interview
                            </button>
                        @endcan
                    @endif

                </div>
            @endif
        </div>
    </div>

    {{-- Modal: Reject --}}
    <div x-show="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto" style="display:none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showRejectModal = false" class="fixed inset-0 bg-gray-500 opacity-75"></div>
            <div class="relative z-10 bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Pelamar</h3>
                <form action="{{ route('recruitment.application.reject', $jobApplication->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan</label>
                        <textarea name="reason" rows="3" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showRejectModal = false" class="btn bg-white border-gray-300 text-gray-700">Batal</button>
                        <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white">Tolak Lamaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Psikotest --}}
    <div x-show="showPsikotestModal" class="fixed inset-0 z-50 overflow-y-auto" style="display:none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showPsikotestModal = false" class="fixed inset-0 bg-gray-500 opacity-75"></div>
            <div class="relative z-10 bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Jadwalkan Psikotest</h3>
                <form action="{{ route('recruitment.application.psikotest.store', $jobApplication->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Psikotest</label>
                        <input type="date" name="psikotest_date" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Tes (opsional)</label>
                        <input type="url" name="psikotest_link" placeholder="https://..." class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showPsikotestModal = false" class="btn bg-white border-gray-300 text-gray-700">Batal</button>
                        <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Interview --}}
    <div x-show="showInterviewModal" class="fixed inset-0 z-50 overflow-y-auto" style="display:none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showInterviewModal = false" class="fixed inset-0 bg-gray-500 opacity-75"></div>
            <div class="relative z-10 bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Jadwalkan Interview</h3>
                <form action="{{ route('recruitment.application.interviews.store', $jobApplication->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Interview <span class="text-red-500">*</span></label>
                        <select name="interview_type" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" x-model="interviewType" x-init="interviewType = 'offline'" @change="interviewType = $event.target.value">
                            <option value="offline">Offline</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Interview <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    </div>
                    <div class="mb-4" x-show="interviewType === 'offline'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <input type="text" name="location" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    </div>
                    <div class="mb-4" x-show="interviewType === 'online'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Meeting</label>
                        <input type="url" name="meeting_link" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Interviewer <span class="text-red-500">*</span></label>
                        <select name="interviewer_employee_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Pilih interviewer...</option>
                            @foreach(\App\Models\Employee::orderBy('full_name')->get() as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showInterviewModal = false" class="btn bg-white border-gray-300 text-gray-700">Batal</button>
                        <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Jadwalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
