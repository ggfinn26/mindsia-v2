document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('resend-form');
    const btn = document.getElementById('resend-btn');
    
    if (!form || !btn) return;
    
    const iconHtml = '<span class="material-symbols-outlined text-[16px]">forward_to_inbox</span>';
    const isLinkSent = form.getAttribute('data-link-sent') === 'true';
    
    function startTimer(seconds) {
        btn.disabled = true;
        localStorage.setItem('resend_cooldown_expiry', Date.now() + (seconds * 1000));
        
        btn.innerHTML = `<span>Kirim Ulang Kode (${seconds}s)</span>`;

        const timer = setInterval(() => {
            const remaining = Math.ceil((localStorage.getItem('resend_cooldown_expiry') - Date.now()) / 1000);
            if (remaining > 0) {
                btn.innerHTML = `<span>Kirim Ulang Kode (${remaining}s)</span>`;
            } else {
                clearInterval(timer);
                btn.disabled = false;
                btn.innerHTML = `<span>Kirim Ulang Kode</span> ${iconHtml}`;
                localStorage.removeItem('resend_cooldown_expiry');
            }
        }, 1000);
    }

    const expiry = localStorage.getItem('resend_cooldown_expiry');
    if (expiry) {
        const remaining = Math.ceil((expiry - Date.now()) / 1000);
        if (remaining > 0) {
            startTimer(remaining);
        } else {
            localStorage.removeItem('resend_cooldown_expiry');
        }
    }

    if (isLinkSent) {
        startTimer(60);
    }
    
    form.addEventListener('submit', () => {
        if (!btn.disabled) {
            startTimer(60);
        }
    });
});
