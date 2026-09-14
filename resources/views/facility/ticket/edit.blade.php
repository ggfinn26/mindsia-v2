@extends('layouts.dashboard')

@section('title', 'Edit Tiket Fasilitas')
@section('header_title', 'Tiket Fasilitas')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('facility.tickets.show', $facilityTicket) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $facilityTicket->title }}
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Edit Tiket Fasilitas</h2>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('facility.tickets.update', $facilityTicket) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="border border-slate-200 bg-white p-5 space-y-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Informasi Tiket</h3>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Judul</label>
                <input type="text" name="title" required maxlength="255" value="{{ old('title', $facilityTicket->title) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Prioritas</label>
                    <select name="priority" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                        <option value="low" {{ old('priority', $facilityTicket->priority) === 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ old('priority', $facilityTicket->priority) === 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ old('priority', $facilityTicket->priority) === 'high' ? 'selected' : '' }}>Tinggi</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Biaya Penanganan (Rp)</label>
                    <input type="number" name="cost_amount" min="0" step="0.01" value="{{ old('cost_amount', $facilityTicket->cost_amount) }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Deskripsi</label>
                <textarea name="description" rows="4" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">{{ old('description', $facilityTicket->description) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('facility.tickets.show', $facilityTicket) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-10 items-center bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
        </div>
    </form>
</div>
@endsection