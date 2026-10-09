@extends('layouts.dashboard')

@section('title', 'Pindah Cabang')
@section('header_title', 'Pindah Cabang')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Pindah Cabang</h1>
        <p class="mt-1 text-sm text-[color:var(--dash-text-muted)]">Cabang saat ini: <strong>{{ $currentBranch?->branch_name ?? '—' }}</strong></p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex gap-3 border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($pendingRequest)
        {{-- Ada request pending — tampilkan status --}}
        <div class="overflow-hidden border border-amber-200 bg-amber-50">
            <div class="flex items-center gap-3 border-b border-amber-200 bg-amber-100 px-5 py-4">
                <span class="material-symbols-outlined text-amber-700">pending</span>
                <h3 class="font-jakarta text-sm font-bold text-amber-900">Permintaan Sedang Direview HR</h3>
            </div>
            <dl class="grid grid-cols-2 gap-4 px-5 py-5 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-[color:var(--dash-text-muted)]">Dari Cabang</dt>
                    <dd class="mt-1 font-medium text-[color:var(--dash-text-heading)]">{{ $pendingRequest->fromBranch?->branch_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-[color:var(--dash-text-muted)]">Ke Cabang</dt>
                    <dd class="mt-1 font-medium text-[color:var(--dash-primary-text,#215aac)]">{{ $pendingRequest->toBranch?->branch_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-[color:var(--dash-text-muted)]">Diajukan</dt>
                    <dd class="mt-1 text-[color:var(--dash-text-body)]">{{ $pendingRequest->created_at->translatedFormat('d F Y, H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-[color:var(--dash-text-muted)]">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Menunggu Review</span>
                    </dd>
                </div>
            </dl>
            <p class="px-5 pb-4 text-xs text-[color:var(--dash-text-muted)]">Kamu tidak bisa mengajukan permintaan baru selama ada permintaan yang sedang direview.</p>
        </div>
    @else
        {{-- Tidak ada pending — tampilkan form --}}
        @if ($errors->any())
            <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="overflow-hidden border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
            <div class="border-b border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-5 py-4">
                <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Ajukan Pindah Cabang</h3>
            </div>
            <form method="POST" action="{{ route('branch-transfers.my-request.store') }}" class="px-5 py-5 space-y-4" novalidate>
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-[color:var(--dash-text-body)]">Cabang tujuan <span class="text-rose-500">*</span></label>
                    <select name="to_branch_id" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)] @error('to_branch_id') border-rose-500 @enderror">
                        <option value="">Pilih Cabang Tujuan</option>
                        @foreach ($otherBranches as $branch)
                            <option value="{{ $branch->id }}" @selected(old('to_branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                    @error('to_branch_id')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                    @if ($otherBranches->isEmpty())
                        <p class="mt-1 text-xs text-amber-600">Tidak ada cabang aktif lain yang tersedia.</p>
                    @endif
                </div>
                <div class="flex gap-3 border-t border-[color:var(--dash-card-border)] pt-4">
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Ajukan Permintaan
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Riwayat permintaan --}}
    @if ($recentRequests->isNotEmpty())
        <div class="overflow-hidden border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
            <div class="border-b border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-5 py-4">
                <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Riwayat Permintaan</h3>
            </div>
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] text-xs font-semibold uppercase tracking-wider text-[color:var(--dash-text-muted)]">
                    <tr>
                        <th class="px-5 py-3 text-left">Ke Cabang</th>
                        <th class="px-5 py-3 text-left">Tanggal</th>
                        <th class="px-5 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                    @foreach ($recentRequests as $req)
                        <tr>
                            <td class="px-5 py-3 font-medium text-[color:var(--dash-text-heading)]">{{ $req->toBranch?->branch_name ?? '—' }}</td>
                            <td class="px-5 py-3 text-[color:var(--dash-text-muted)]">{{ $req->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3">
                                @if ($req->status === 'review')
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Menunggu</span>
                                @elseif ($req->status === 'approved')
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Disetujui</span>
                                @else
                                    <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection
