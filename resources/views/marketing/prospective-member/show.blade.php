@extends('layouts.dashboard')

@section('title', 'Detail Prospective Member')

@section('content')
@php
    $statusColors = [
        'ALMOST' => 'bg-yellow-100 text-yellow-700',
        'YES'    => 'bg-blue-100 text-blue-700',
        'FIXED'  => 'bg-green-100 text-green-700',
        'NO'     => 'bg-red-100 text-red-700',
    ];
    $statusDescriptions = [
        'ALMOST' => 'Hampir — masih perlu follow-up',
        'YES'    => 'Berminat — sedang proses',
        'FIXED'  => 'Pasti daftar',
        'NO'     => 'Tidak jadi',
    ];
@endphp
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('prospective-members.index') }}" class="text-gray-500 hover:text-gray-700">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Detail Lead</h1>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Profil Prospective Member --}}
        <div class="bg-white shadow rounded-lg p-5 md:col-span-1 space-y-4">
            <div class="flex items-center justify-between border-b pb-2">
                <h2 class="text-base font-semibold text-gray-800">Profil Lead</h2>
                <a href="{{ route('prospective-members.edit', $lead) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-sm">
                    <span class="material-symbols-outlined text-[14px]">edit</span> Edit
                </a>
            </div>

            <div>
                <p class="text-xs text-gray-500">Nama</p>
                <p class="text-sm font-semibold text-gray-900">{{ $lead->full_name }}</p>
            </div>
            <div x-data="{
                open: false,
                templates: [],
                selected: null,
                leadName: {{ Illuminate\Support\Js::from($lead->full_name) }},
                marketerName: {{ Illuminate\Support\Js::from(auth()->user()->employee?->full_name ?? auth()->user()->name) }},
                waNumber: {{ Illuminate\Support\Js::from(preg_replace('/[^0-9]/', '', ltrim($lead->whatsapp_number ?? '', '0'))) }},
                init() {
                    fetch('{{ route('wa-templates.json') }}')
                        .then(r => r.json())
                        .then(data => { this.templates = data; });
                },
                renderedBody() {
                    if (!this.selected) return '';
                    return this.selected.body
                        .replace(/\{nama\}/g, this.leadName)
                        .replace(/\{marketer\}/g, this.marketerName);
                },
                openWa() {
                    const text = this.renderedBody();
                    const url = 'https://wa.me/' + this.waNumber + (text ? '?text=' + encodeURIComponent(text) : '');
                    window.open(url, '_blank');
                    this.open = false;
                }
            }">
                <p class="text-xs text-gray-500">WhatsApp</p>
                <p class="text-sm text-gray-700 font-medium mb-1">{{ $lead->whatsapp_number }}</p>
                <button @click="open = !open"
                    class="inline-flex items-center gap-1 text-xs px-2 py-1 bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100">
                    <span class="material-symbols-outlined text-[14px]">chat</span>
                    Hubungi via WA
                </button>
                <div x-show="open" @click.outside="open = false" class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200 space-y-2" style="display:none">
                    <template x-if="templates.length === 0">
                        <p class="text-xs text-gray-400 italic">Belum ada template. <a href="{{ route('wa-templates.index') }}" class="text-indigo-600 underline">Buat template</a></p>
                    </template>
                    <template x-if="templates.length > 0">
                        <div class="space-y-2">
                            <select @change="selected = templates.find(t => t.id == $event.target.value) || null"
                                class="block w-full border-gray-300 rounded-md text-xs focus:ring-indigo-400 focus:border-indigo-400">
                                <option value="">— Pilih Template —</option>
                                <template x-for="t in templates" :key="t.id">
                                    <option :value="t.id" x-text="t.name"></option>
                                </template>
                            </select>
                            <p x-show="selected" x-text="renderedBody()" class="text-xs text-gray-600 bg-white p-2 rounded border border-gray-200 whitespace-pre-wrap"></p>
                        </div>
                    </template>
                    <button @click="openWa()"
                        class="w-full text-xs px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Buka WhatsApp
                    </button>
                </div>
            </div>
            @if ($lead->email)
                <div>
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="text-sm text-gray-800">{{ $lead->email }}</p>
                </div>
            @endif
            @if ($lead->instagram)
                <div>
                    <p class="text-xs text-gray-500">Instagram</p>
                    <p class="text-sm text-gray-800">{{ $lead->instagram }}</p>
                </div>
            @endif
            <div>
                <p class="text-xs text-gray-500">Jenis Kelamin</p>
                <p class="text-sm text-gray-800">{{ $lead->gender === 'L' ? 'Laki-laki' : ($lead->gender === 'P' ? 'Perempuan' : '-') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Sekolah</p>
                <p class="text-sm text-gray-800">
                    {{ $lead->institution?->institution_name ?? $lead->institution_name ?? '-' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Status</p>
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $lead->status }}
                </span>
                <p class="text-xs text-gray-500 mt-1">{{ $statusDescriptions[$lead->status] ?? '' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">PIC</p>
                <p class="text-sm text-gray-800">{{ $lead->capturedBy->full_name ?? '-' }}</p>
            </div>
            @if ($lead->socialization)
                <div>
                    <p class="text-xs text-gray-500">Dari Sosialisasi</p>
                    <a href="{{ route('socializations.show', $lead->socialization) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                        {{ $lead->socialization->location_name }}
                    </a>
                </div>
            @endif
            @if ($lead->member)
                <div class="pt-2 border-t">
                    <p class="text-xs text-green-600 font-medium">Sudah terdaftar sebagai Member</p>
                </div>
            @endif

            {{-- Update status --}}
            @canany(['marketing.prospective_member.update', 'marketing.prospective.view_area', 'marketing.prospective.view_national'])
                <div class="pt-3 border-t" x-data="{ showStatusForm: false }">
                    <button @click="showStatusForm = !showStatusForm" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Update Status
                    </button>
                    <form x-show="showStatusForm" action="{{ route('prospective-members.update-status', $lead) }}" method="POST"
                          class="mt-3 space-y-3" style="display: none;">
                        @csrf @method('PATCH')
                        <select name="status" required
                            class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach (array_keys($statusColors) as $val)
                                <option value="{{ $val }}" {{ $lead->status === $val ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="change_reason" placeholder="Alasan perubahan (opsional)"
                            class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="w-full px-3 py-2 text-sm text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            Simpan
                        </button>
                    </form>
                </div>
            @endcanany
        </div>

        {{-- Log Follow-Up --}}
        <div class="bg-white shadow rounded-lg p-5 md:col-span-2 flex flex-col max-h-[600px]">
            <h2 class="text-base font-semibold text-gray-800 border-b pb-2 mb-4">Log Follow-Up</h2>

            <div class="flex-1 overflow-y-auto space-y-3 mb-4 pr-1">
                @forelse ($lead->statusHistories->sortByDesc('created_at') as $history)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-xs text-gray-500">{{ $history->created_at->format('d M Y H:i') }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $statusColors[$history->new_status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $history->new_status }}
                            </span>
                        </div>
                        @if ($history->change_reason)
                            <p class="text-sm text-gray-700">{{ $history->change_reason }}</p>
                        @endif
                        @if ($history->changed_by_employee_id)
                            <p class="text-xs text-gray-400 mt-1">oleh {{ $history->changedBy->full_name ?? '-' }}</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-gray-500 text-sm py-8">Belum ada log follow-up.</div>
                @endforelse
            </div>

            {{-- Form tambah catatan --}}
            <div class="border-t pt-4">
                <form action="{{ route('prospective-members.follow-ups.store', $lead) }}" method="POST" class="space-y-3">
                    @csrf
                    <textarea name="note" rows="3" required placeholder="Catatan hasil follow-up..."
                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            Simpan Catatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
