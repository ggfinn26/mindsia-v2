@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-[var(--dash-text-heading)] font-bold">Edit Periode Payroll</h1>
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[var(--dash-card-border)]">
        <form action="{{ route('payroll.periods.update', $period->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium mb-1">Nama Periode <span class="text-red-500">*</span></label>
                <input name="name" type="text" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] form-input w-full" value="{{ old('name', $period->name) }}" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input name="start_date" type="date" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] form-input w-full" value="{{ old('start_date', $period->start_date?->format('Y-m-d')) }}" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Akhir <span class="text-red-500">*</span></label>
                    <input name="end_date" type="date" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] form-input w-full" value="{{ old('end_date', $period->end_date?->format('Y-m-d')) }}" required />
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="status" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] form-select w-full">
                    <option value="draft" {{ $period->status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ $period->status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="closed" {{ $period->status == 'closed' ? 'selected' : '' }}>Selesai (Closed)</option>
                </select>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('payroll.periods.index') }}" class="btn bg-[var(--dash-card-bg)] border-[var(--dash-card-border)] hover:border-[var(--dash-input-border)] text-[var(--dash-text-body)] mr-3">Batal</a>
                <button type="submit" class="btn bg-[var(--dash-primary)] hover:opacity-90 text-white">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
