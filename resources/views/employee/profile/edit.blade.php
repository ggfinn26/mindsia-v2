@extends('layouts.dashboard')

@section('title', 'Edit Profile - MINDSIA')
@section('header_title', 'Edit Profile')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Informasi Akun</h3>
                
                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Panggilan (Tampil di Dashboard)</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border" required>
                    </div>
                    
                    <div>
                        <label for="telegram_chat_id" class="block text-sm font-medium text-gray-700 mb-1">Telegram Chat ID</label>
                        <input type="text" name="telegram_chat_id" id="telegram_chat_id" value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}" placeholder="Misal: 123456789" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                        <p class="mt-1 text-xs text-gray-500">Gunakan ID ini untuk menerima notifikasi dari bot Telegram kami.</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed">
                        <p class="mt-1 text-xs text-gray-500">Email tidak dapat diubah. Hubungi IT jika perlu mengubah email.</p>
                    </div>
                </div>

                @if($employee)
                    <hr class="my-8 border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Data Karyawan</h3>
                    
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (HR)</label>
                                <input type="text" value="{{ $employee->full_name }}" disabled class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Karyawan</label>
                                <input type="text" value="{{ $employee->employee_code }}" disabled class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed">
                            </div>
                        </div>

                        <div>
                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">+62</span>
                                <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', preg_replace('/^\+?62/', '', $employee->whatsapp_number)) }}" placeholder="81234567890" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-lg border border-gray-300 focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Gunakan nomor yang aktif untuk keperluan notifikasi sistem.</p>
                        </div>
                        
                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                            <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg shadow-sm px-3 py-2 bg-white">
                            <p class="mt-1 text-xs text-gray-500">Maksimal ukuran 2MB. Format JPG, JPEG, PNG.</p>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-100">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan</label>
                            
                            @if($employee->signature_image)
                                <div class="mb-3 p-3 border border-gray-200 rounded-lg bg-gray-50 max-w-sm">
                                    <p class="text-xs text-gray-500 mb-2">Tanda Tangan Saat Ini:</p>
                                    <img src="{{ Storage::url($employee->signature_image) }}" alt="Tanda Tangan" class="h-16 object-contain">
                                </div>
                                <p class="text-xs text-gray-500 mb-2">Untuk memperbarui, silakan gambar ulang tanda tangan Anda di bawah ini:</p>
                            @else
                                <p class="text-xs text-gray-500 mb-2">Silakan gambar tanda tangan Anda di dalam kotak ini:</p>
                            @endif
                            
                            <div class="border border-gray-300 rounded-lg overflow-hidden w-full max-w-sm relative shadow-sm">
                                <canvas id="signature-pad" width="380" height="180" class="bg-white w-full touch-none cursor-crosshair"></canvas>
                                <button type="button" id="clear-signature" class="absolute top-2 right-2 text-xs bg-white text-gray-600 border border-gray-300 px-2 py-1 rounded hover:bg-gray-50 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#5586DB]">Bersihkan</button>
                            </div>
                            <input type="hidden" name="signature_base64" id="signature_base64">
                            <p class="mt-1.5 text-xs text-gray-500">Gunakan mouse atau sentuh layar untuk menggambar.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-gray-50 px-6 py-4 sm:px-8 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('employee.profile.show') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#5586DB] focus:ring-offset-1 transition-colors">
                    Batal
                </a>
                <button type="submit" id="btn-submit" class="px-4 py-2 text-sm font-medium text-white bg-[#5586DB] border border-transparent rounded-lg shadow-sm hover:bg-[#4370BC] focus:outline-none focus:ring-2 focus:ring-[#5586DB] focus:ring-offset-1 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signature-pad');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    let drawing = false;

    // Fix scaling for crisp lines
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;

    // Fill background with white
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    function getPos(e) {
        const r = canvas.getBoundingClientRect();
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        const clientY = e.clientY || (e.touches && e.touches[0].clientY);
        return {
            x: clientX - r.left,
            y: clientY - r.top
        };
    }

    function startPos(e) {
        drawing = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        if (e.cancelable) e.preventDefault();
    }

    function endPos() {
        drawing = false;
        ctx.beginPath();
    }

    function draw(e) {
        if (!drawing) return;
        const pos = getPos(e);
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#000000';
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        if (e.cancelable) e.preventDefault();
    }

    canvas.addEventListener('mousedown', startPos, {passive: false});
    canvas.addEventListener('mouseup', endPos);
    canvas.addEventListener('mousemove', draw, {passive: false});
    canvas.addEventListener('mouseleave', endPos);
    
    canvas.addEventListener('touchstart', startPos, {passive: false});
    canvas.addEventListener('touchend', endPos);
    canvas.addEventListener('touchmove', draw, {passive: false});

    document.getElementById('clear-signature').addEventListener('click', () => {
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', () => {
        // Create an empty canvas to compare with
        const blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        const blankCtx = blank.getContext('2d');
        blankCtx.fillStyle = '#ffffff';
        blankCtx.fillRect(0, 0, blank.width, blank.height);
        
        // If not blank, set the hidden field value
        if (canvas.toDataURL() !== blank.toDataURL()) {
            document.getElementById('signature_base64').value = canvas.toDataURL('image/png');
        }
    });
});
</script>
@endsection
