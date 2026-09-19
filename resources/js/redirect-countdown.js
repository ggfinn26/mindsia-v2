const el = document.getElementById("countdown");
const redirectUrl = document.getElementById("countdown")?.dataset.href;

if (el && redirectUrl) {
    let seconds = parseInt(el.textContent, 10) || 5;
    const timer = setInterval(() => {
        seconds--;
        el.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            window.location.href = redirectUrl;
        }
    }, 1000);
}
