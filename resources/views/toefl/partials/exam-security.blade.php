{{-- Exam Security: fullscreen prompt + anti-cheat --}}
<div id="exam-fs-overlay"
    style="position:fixed;inset:0;z-index:9999;background:#1e293b;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px;color:#f8fafc;">
    <svg style="width:48px;height:48px;color:#60a5fa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
    </svg>
    <p style="font-size:1.125rem;font-weight:600;margin:0">Ujian akan berjalan dalam mode layar penuh</p>
    <p style="font-size:0.875rem;color:#94a3b8;margin:0">Klik tombol di bawah untuk memulai</p>
    <button id="exam-fs-btn"
        style="margin-top:8px;padding:12px 32px;background:#3b82f6;color:#fff;border:none;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;">
        Mulai Ujian
    </button>
</div>

<script>
(function () {
    const overlay = document.getElementById('exam-fs-overlay');
    const btn     = document.getElementById('exam-fs-btn');

    function enterFullscreen() {
        const el  = document.documentElement;
        const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
        if (req) req.call(el).catch(() => {});
    }

    btn.addEventListener('click', function () {
        enterFullscreen();
        overlay.style.display = 'none';
        applyAntiCheat();
        // signal listening page to start audio
        window.dispatchEvent(new Event('exam-start'));
        if (typeof window._examStart === 'function') window._examStart();
    });

    function applyAntiCheat() {
        /* block right-click */
        document.addEventListener('contextmenu', e => e.preventDefault());

        /* block copy / cut / select */
        document.addEventListener('copy',        e => e.preventDefault());
        document.addEventListener('cut',         e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());

        /* no-select CSS */
        document.body.style.userSelect       = 'none';
        document.body.style.webkitUserSelect = 'none';

        /* block keyboard shortcuts */
        document.addEventListener('keydown', function (e) {
            const ctrl = e.ctrlKey || e.metaKey;
            if (e.key === 'F12')                                              { e.preventDefault(); return; }
            if (ctrl && e.shiftKey && ['i','j','c'].includes(e.key.toLowerCase())) { e.preventDefault(); return; }
            if (ctrl && ['u','s','p','a','c','x'].includes(e.key.toLowerCase()))   { e.preventDefault(); return; }
        });

        /* re-enter fullscreen if user exits */
        document.addEventListener('fullscreenchange', function () {
            if (!document.fullscreenElement) enterFullscreen();
        });
    }
}());
</script>
