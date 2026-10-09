@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Review & NPS Member</h1>
    </div>

    <!-- Stats/Scores -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Average Rating -->
        <div class="flex flex-col bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-4">
            <h2 class="text-sm font-semibold text-[color:var(--dash-text-muted)] uppercase mb-2">Rata-rata Rating</h2>
            <div class="flex items-start">
                <div class="text-3xl font-bold text-[color:var(--dash-text-heading)] mr-2">0.0</div>
                <div class="text-sm font-semibold text-white px-1.5 bg-yellow-500 rounded-full mt-1">/ 5</div>
            </div>
        </div>
        <!-- NPS Score -->
        <div class="flex flex-col bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-4">
            <h2 class="text-sm font-semibold text-[color:var(--dash-text-muted)] uppercase mb-2">NPS Score</h2>
            <div class="flex items-start">
                <div class="text-3xl font-bold text-[color:var(--dash-text-heading)] mr-2">0</div>
            </div>
        </div>
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)]">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-[color:var(--dash-text-muted)] bg-[var(--dash-nav-hover-bg)] border-t border-b border-[color:var(--dash-card-border)]">
                    <tr>
                        <th class="px-6 py-3 text-left">Member & Program</th>
                        <th class="px-6 py-3 text-left">Rating</th>
                        <th class="px-6 py-3 text-left">Kategori NPS</th>
                        <th class="px-6 py-3 text-left">Review</th>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Status Landing Page</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-[color:var(--dash-card-border)]">
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-[color:var(--dash-text-muted)] text-center">Belum ada review NPS.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
