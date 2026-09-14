<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Pendaftaran Siswa Baru - MINDSIA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="w-full flex flex-col md:flex-row bg-white text-ky-text font-sans antialiased min-h-screen">
    <!-- Left Form -->
    <div class="w-full md:w-[55%] lg:w-1/2 flex flex-col p-8 md:p-12 overflow-y-auto bg-white border-r border-ky-border/50 shadow-[10px_0_30px_rgba(0,0,0,0.02)] z-10 relative">
        <a href="{{ url('/') }}" class="mb-10 inline-block">
            <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-8 w-auto">
        </a>
        <h1 class="font-sans font-black text-[32px] md:text-[36px] text-[#06122B] mb-2 tracking-tight">Pendaftaran Siswa Baru</h1>
        <p class="text-[15px] text-ky-text/60 mb-8">Lengkapi formulir pendaftaran di bawah ini untuk memulai.</p>

        <form action="{{ route('public.store') }}" method="POST" class="bg-ky-surface border border-ky-border/50 rounded-2xl p-6 md:p-8 shadow-sm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block mb-2 text-[12px] font-bold text-ky-text/80 uppercase tracking-wider">Cabang *</label>
                    <select name="branch_id" class="w-full px-4 py-3 bg-white border border-ky-border/50 rounded-xl text-[14px] focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-colors">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-[12px] font-bold text-ky-text/80 uppercase tracking-wider">Program *</label>
                    <select name="program_id" class="w-full px-4 py-3 bg-white border border-ky-border/50 rounded-xl text-[14px] focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-colors">
                        <option value="">-- Pilih Program --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program') == $program->id ? 'selected' : '' }}>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-[12px] font-bold text-ky-text/80 uppercase tracking-wider">Nama Lengkap *</label>
                <input name="name" type="text" class="w-full px-4 py-3 bg-white border border-ky-border/50 rounded-xl text-[14px] focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-colors" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block mb-2 text-[12px] font-bold text-ky-text/80 uppercase tracking-wider">Email *</label>
                    <input name="email" type="email" class="w-full px-4 py-3 bg-white border border-ky-border/50 rounded-xl text-[14px] focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-colors" required>
                </div>
                <div>
                    <label class="block mb-2 text-[12px] font-bold text-ky-text/80 uppercase tracking-wider">Asal Kota *</label>
                    <input name="city" type="text" class="w-full px-4 py-3 bg-white border border-ky-border/50 rounded-xl text-[14px] focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-colors" required>
                </div>
            </div>

            <div class="mb-8">
                <label class="block mb-2 text-[12px] font-bold text-ky-text/80 uppercase tracking-wider">WhatsApp *</label>
                <input name="whatsapp" type="tel" class="w-full px-4 py-3 bg-white border border-ky-border/50 rounded-xl text-[14px] focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-colors" required>
            </div>

            <div class="pt-6 border-t border-ky-border/50 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <a href="{{ url('/') }}" class="px-6 py-3.5 bg-white border border-ky-border/50 rounded-xl text-[13px] font-bold hover:bg-ky-surface transition-colors text-ky-text/70 hover:text-[#06122B] text-center">Batal</a>
                <button type="submit" class="px-8 py-3.5 bg-[#06122B] text-white rounded-xl text-[13px] font-bold shadow-md hover:bg-gold hover:text-[#06122B] transition-colors text-center">Submit Pendaftaran</button>
            </div>
        </form>
    </div>

    <!-- Right Cover -->
    <div class="hidden md:flex md:w-[45%] lg:w-1/2 flex-col items-center justify-center text-center relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-[#06122B]/85 backdrop-blur-[2px]"></div>
        
        <div class="relative z-10 max-w-md px-10">
            <div class="w-16 h-1 bg-gold mx-auto mb-8"></div>
            <h2 class="font-sans font-black text-[36px] md:text-[42px] tracking-tight text-white mb-6 leading-tight">Mulai Belajar <br>di MINDSIA</h2>
            <p class="text-[16px] text-white/70 leading-relaxed">Bergabunglah bersama kami dan tingkatkan kemampuan bahasa Inggris Anda dengan fasilitas asrama 24 jam bertaraf internasional.</p>
        </div>
    </div>
</body>
</html>
