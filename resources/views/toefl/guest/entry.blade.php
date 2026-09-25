@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-lg">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            {{ $toeflTest->test_name }}
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Isi data diri untuk memulai tes TOEFL trial.
        </p>
        <p class="mt-1 text-center text-xs text-gray-400">
            Listening {{ $toeflTest->listening_time_limit }}m &bull;
            Structure {{ $toeflTest->structure_time_limit }}m &bull;
            Reading {{ $toeflTest->reading_time_limit }}m
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">

            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('toefl.guest.start', $toeflTest) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input name="guest_name" type="text" required value="{{ old('guest_name') }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('guest_name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">No. WhatsApp <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-bold">+62</span>
                        <input name="guest_whatsapp" type="text" required
                            value="{{ old('guest_whatsapp') ? ltrim(preg_replace('/^\+?62/', '', old('guest_whatsapp')), '0') : '' }}"
                            placeholder="8xxxxxxxxxx"
                            @input="$el.value = $el.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                            class="flex-1 block w-full border border-gray-300 rounded-r-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" minlength="8" maxlength="15">
                    </div>
                    @error('guest_whatsapp') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Kota Domisili <span class="text-red-500">*</span></label>
                    <input name="guest_city" type="text" required value="{{ old('guest_city') }}"
                        placeholder="Contoh: Bandung"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('guest_city') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input name="guest_email" type="email" value="{{ old('guest_email') }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('guest_email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Instagram <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input name="guest_instagram" type="text" value="{{ old('guest_instagram') }}"
                        placeholder="@username"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('guest_instagram') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Instansi / Asal Sekolah <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input name="guest_institution" type="text" value="{{ old('guest_institution') }}"
                        placeholder="Nama sekolah / kampus"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('guest_institution') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                @if ($toeflTest->password)
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700">Password Test <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative">
                            <input name="password" :type="show ? 'text' : 'password'" required
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 pl-3 pr-10 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @else
                    <div class="flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        Test ini tidak memerlukan password
                    </div>
                @endif

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Mulai Ujian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
