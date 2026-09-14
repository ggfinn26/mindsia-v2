@extends('layouts.dashboard')

@section('title', 'Pengaturan Tanda Tangan Dokumen')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Tanda Tangan Dokumen</h1>
        <p class="mt-1 text-sm text-gray-500">Nama dan jabatan penandatangan institusional per tipe dokumen.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('attendance.document-signature.upsert') }}">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg divide-y divide-gray-100">
            @foreach ($documentTypes as $type)
                @php $s = $settings[$type] ?? null; @endphp
                <div class="px-6 py-5">
                    <div class="flex items-center mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 uppercase tracking-wide">
                            {{ $type }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">
                                Nama Penandatangan
                            </label>
                            <input type="text"
                                   name="signers[{{ $type }}][signer_name]"
                                   value="{{ old("signers.$type.signer_name", $s?->signer_name) }}"
                                   class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Nama lengkap penandatangan">
                            @error("signers.$type.signer_name")
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">
                                Jabatan
                            </label>
                            <input type="text"
                                   name="signers[{{ $type }}][signer_title]"
                                   value="{{ old("signers.$type.signer_title", $s?->signer_title) }}"
                                   class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Jabatan / posisi">
                            @error("signers.$type.signer_title")
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                Simpan Semua
            </button>
        </div>
    </form>
</div>
@endsection
