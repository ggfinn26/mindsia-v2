@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Daftar Akun Karyawan</h1>
        <p class="text-gray-600">Langkah 1 dari 2: Verifikasi Kode Karyawan</p>
    </div>

    <div
        x-data="{
            code: '{{ old('employee_code') }}',
            error: '{{ $errors->first('employee_code') }}',
            loading: false,
            async submit() {
                this.error = '';
                this.loading = true;
                try {
                    const res = await fetch('{{ route('register.verify') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ employee_code: this.code }),
                    });
                    if (res.ok) {
                        const data = await res.json();
                        window.location = data.redirect ?? '{{ route('register.step2') }}';
                    } else {
                        const data = await res.json();
                        this.error = data.errors?.employee_code?.[0] ?? 'Terjadi kesalahan, coba lagi.';
                    }
                } catch {
                    this.error = 'Tidak dapat terhubung ke server.';
                } finally {
                    this.loading = false;
                }
            }
        }"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="employee_code" class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Karyawan
                </label>
                <input
                    type="text"
                    id="employee_code"
                    name="employee_code"
                    x-model="code"
                    :class="error ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:border-transparent transition"
                    placeholder="Masukkan kode karyawan Anda"
                    required
                >

                <div
                    x-show="error"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="mt-3 flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3"
                >
                    <svg class="h-5 w-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium text-red-700" x-text="error"></p>
                </div>
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-medium py-2 px-4 rounded-lg transition"
            >
                <span x-show="!loading">Lanjut ke Langkah 2</span>
                <span x-show="loading">Memeriksa...</span>
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-gray-600">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Masuk di sini</a>
    </p>
</div>
@endsection
