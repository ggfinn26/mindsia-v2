<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $item->item_name }} — MINDSIA</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }

        @media print { body { display: none !important; } }
        body { margin: 0; overflow: hidden; }
        
        #pdf-pages canvas {
            display: block;
            width: 100%;
            pointer-events: none;
            margin-bottom: 12px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div id="fullscreen-mask" class="fixed inset-0 z-50 bg-gray-50 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200 text-gray-900 shrink-0">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#5586DB]">menu_book</span>
                <div>
                    <h1 class="text-sm font-semibold text-gray-900 font-jakarta">{{ $item->item_name }}</h1>
                    <p class="text-xs text-gray-500">{{ $classroom->class_name }}</p>
                </div>
            </div>
            <a href="{{ route('member.curriculum.index') }}" class="text-gray-500 hover:text-gray-800 flex items-center text-sm font-medium transition-colors">
                ✕ Tutup
            </a>
        </div>

        <!-- Viewer Area -->
        <div class="flex-1 relative overflow-auto" id="viewer-area" style="user-select: none; -webkit-user-select: none;">
            @if ($fileType === 'pdf')
                <div id="pdf-container" class="w-full max-w-4xl mx-auto px-4 py-6 flex flex-col items-center">
                    <div id="pdf-pages" class="w-full"></div>
                    <p id="pdf-loading" class="text-gray-500 my-10 flex flex-col items-center gap-3">
                        <span class="material-symbols-outlined animate-spin text-3xl">autorenew</span>
                        Memuat dokumen PDF...
                    </p>
                </div>
            @elseif ($fileType === 'video')
                <div class="w-full h-full flex items-center justify-center relative">
                <video id="mat-video" class="max-h-full max-w-full" controlsList="nodownload nofullscreen" disablePictureInPicture oncontextmenu="return false" playsinline controls>
                    <source src="{{ $mediaUrl }}" />
                    Browser Anda tidak mendukung pemutaran video.
                </video>
                <!-- Watermark canvas over video -->
                <canvas id="wm-canvas" class="absolute inset-0 w-full h-full pointer-events-none" style="opacity:0.18;"></canvas>
                </div>
            @elseif ($fileType === 'audio')
                <div class="w-full h-full flex items-center justify-center relative">
                <div class="bg-white p-8 rounded-2xl border border-gray-200 w-full max-w-md text-center shadow-2xl relative">
                    <span class="material-symbols-outlined text-6xl text-gray-400 mb-4 block">headphones</span>
                    <h3 class="text-lg font-medium text-gray-800 font-jakarta mb-6">{{ $item->item_name }}</h3>
                    <audio id="mat-audio" controls controlsList="nodownload" class="w-full">
                        <source src="{{ $mediaUrl }}">
                        Browser Anda tidak mendukung pemutaran audio.
                    </audio>
                    <!-- Watermark canvas over audio box -->
                    <canvas id="wm-canvas" class="absolute inset-0 w-full h-full pointer-events-none" style="opacity:0.12;"></canvas>
                </div>
                </div>
            @else
                <div class="text-center text-gray-500 mt-10 px-6">
                    <p class="text-lg font-semibold text-gray-800 font-jakarta mb-2">{{ $item->item_name }}</p>
                    <p class="text-sm">Format <strong class="text-gray-900">{{ strtoupper($fileType) }}</strong> tidak dapat ditampilkan langsung.</p>
                </div>
            @endif
        </div>

        <!-- Anti-leak Blur Overlay -->
        <div id="blur-overlay" class="hidden fixed inset-0 z-[60] backdrop-blur-xl bg-gray-900/60 flex items-center justify-center">
            <div class="bg-white border border-gray-200 rounded-xl p-8 max-w-sm text-center shadow-2xl mx-4">
                <span class="material-symbols-outlined text-4xl text-red-500 mb-3 block">security</span>
                <p class="text-lg font-semibold text-gray-900 font-jakarta mb-2">Materi Dilindungi</p>
                <p class="text-sm text-gray-600 mb-6">Aktivitas mencurigakan terdeteksi. Harap tidak merekam layar atau meninggalkan area tampilan materi.</p>
                <button id="blur-dismiss" class="bg-[#5586DB] text-white px-6 py-2.5 rounded-lg font-medium hover:bg-[#4370BC] transition-colors w-full">Kembali ke Materi</button>
            </div>
        </div>
    </div>

    @if ($fileType === 'pdf')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @endif


    <script>
    (function() {
        const logo = new Image();
        logo.src = '{{ asset("img/logo/mindsia-logo.webp") }}';
        window.mindsiaLogo = logo;
    })();
    </script>
        <script>
    (function() {
        const MEDIA_URL = @json($mediaUrl);
        const USER_NAME = @json($memberName);
        const FILE_TYPE = @json($fileType);
        
        const overlay = document.getElementById('blur-overlay');
        const video = document.getElementById('mat-video');
        const audio = document.getElementById('mat-audio');

        function showBlur() {
            if (video && typeof video.pause === 'function') video.pause();
            if (audio && typeof audio.pause === 'function') audio.pause();
            overlay.classList.remove('hidden');
        }

        function hideBlur() {
            overlay.classList.add('hidden');
        }

        document.getElementById('blur-dismiss').addEventListener('click', hideBlur);

        document.getElementById('fullscreen-mask').addEventListener('mouseleave', () => {
            showBlur();
        });

        window.addEventListener('blur', showBlur);
        window.addEventListener('focus', hideBlur);

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) showBlur();
            else hideBlur();
        });

        const SCREENSHOT_COMBOS = [
            e => e.metaKey && e.shiftKey && ['3','4','5'].includes(e.key),
            e => e.metaKey && e.ctrlKey && e.shiftKey && ['3','4'].includes(e.key),
            e => e.key === 'PrintScreen',
            e => e.altKey && e.key === 'PrintScreen',
            e => e.metaKey && e.shiftKey && e.key === 's',
            e => e.metaKey && e.key === 'g',
        ];

        document.addEventListener('keydown', (e) => {
            if (SCREENSHOT_COMBOS.some(fn => fn(e))) {
                e.preventDefault();
                showBlur();
            }
        });

        document.addEventListener('contextmenu', e => e.preventDefault());

        function drawWatermarkCanvas(ctx, w, h) {
            ctx.clearRect(0, 0, w, h);
            const text = `${USER_NAME} • ${new Date().toISOString().slice(0, 10)}`;
            const fontSize = Math.max(12, Math.round(w / 35));
            ctx.save();
            ctx.globalAlpha = FILE_TYPE === 'pdf' ? 0.18 : 0.4;
            ctx.fillStyle = FILE_TYPE === 'pdf' ? '#1e3a5f' : '#ffffff';
            ctx.font = `bold ${fontSize}px Arial`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            const stepX = Math.round(w * 0.55);
            const stepY = Math.round(h * 0.22);
            for (let y = stepY / 2; y < h + stepY; y += stepY) {
                for (let x = -stepX / 2; x < w + stepX; x += stepX) {
                    ctx.save();
                    ctx.translate(x, y);
                    ctx.rotate(-Math.PI / 8);
                    ctx.fillText(text, 0, 0);
                    ctx.restore();
                }
            }
            ctx.restore();
            
            // Draw logo in the center of the canvas
            if (typeof window.mindsiaLogo !== 'undefined' && window.mindsiaLogo.complete && window.mindsiaLogo.naturalWidth > 0) {
                ctx.save();
                // Lower opacity for central watermark so it doesn't obstruct reading too much
                ctx.globalAlpha = FILE_TYPE === 'pdf' ? 0.08 : 0.2;
                
                // Make the logo larger since it's centered
                const logoW = Math.max(150, w * 0.4);
                const ratio = window.mindsiaLogo.height / window.mindsiaLogo.width;
                const logoH = logoW * ratio;
                
                // Calculate center coordinates
                const x = (w - logoW) / 2;
                const y = (h - logoH) / 2;
                
                ctx.drawImage(window.mindsiaLogo, x, y, logoW, logoH);
                ctx.restore();
            }
        }

        // Draw watermark for video/audio
        if (FILE_TYPE === 'video' || FILE_TYPE === 'audio') {
            function updateWm() {
                const canvas = document.getElementById('wm-canvas');
                if (!canvas) return;
                const rect = canvas.parentElement.getBoundingClientRect();
                canvas.width = rect.width;
                canvas.height = rect.height;
                drawWatermarkCanvas(canvas.getContext('2d'), rect.width, rect.height);
            }
            updateWm();
            window.mindsiaLogo.addEventListener('load', updateWm);
            window.addEventListener('resize', updateWm);
        }

        // PDF renderer
        if (FILE_TYPE === 'pdf' && typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            const pagesContainer = document.getElementById('pdf-pages');
            const loadingEl = document.getElementById('pdf-loading');

            pdfjsLib.getDocument({ url: MEDIA_URL, withCredentials: true }).promise
                .then(async (pdf) => {
                    loadingEl.remove();
                    const containerWidth = document.getElementById('pdf-container').offsetWidth || window.innerWidth;

                    for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                        const page = await pdf.getPage(pageNum);
                        
                        const naturalViewport = page.getViewport({ scale: 1 });
                        const scale = Math.min((containerWidth - 32) / naturalViewport.width, 2.0);
                        const viewport = page.getViewport({ scale });

                        const wrapper = document.createElement('div');
                        wrapper.style.cssText = 'position:relative; margin-bottom:16px; width:100%; border-radius:8px; overflow:hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.5);';

                        const canvas = document.createElement('canvas');
                        const dpr = window.devicePixelRatio || 1;
                        canvas.width = viewport.width * dpr;
                        canvas.height = viewport.height * dpr;
                        canvas.style.cssText = 'display:block; width:100%; pointer-events:none; background:white;';
                        wrapper.appendChild(canvas);
                        const ctx = canvas.getContext('2d');
                        ctx.scale(dpr, dpr);

                        const wm = document.createElement('canvas');
                        wm.width = viewport.width;
                        wm.height = viewport.height;
                        wm.style.cssText = 'position:absolute; top:0; left:0; width:100%; pointer-events:none;';
                        wrapper.appendChild(wm);

                        pagesContainer.appendChild(wrapper);

                        await page.render({ canvasContext: ctx, viewport }).promise;

                        drawWatermarkCanvas(wm.getContext('2d'), viewport.width, viewport.height);
                        window.mindsiaLogo.addEventListener('load', () => drawWatermarkCanvas(wm.getContext('2d'), viewport.width, viewport.height));
                    }
                })
                .catch((e) => {
                    console.error(e);
                    loadingEl.innerHTML = '<span class="material-symbols-outlined text-red-500 text-3xl">error</span> Gagal memuat materi. Pastikan file tersedia.';
                });
        }
    })();
    </script>
</body>
</html>
