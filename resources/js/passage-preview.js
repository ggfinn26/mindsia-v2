function renderPassagePreview() {
    const raw = document.getElementById('p-body-text')?.value ?? '';
    const container = document.getElementById('passage-preview-render');
    if (!container) return;
    const lines = raw.split('\n').filter(l => l.trim() !== '');
    container.innerHTML = lines.map((line, i) => {
        const safe = line.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        const rendered = safe.replace(/\[U\](.*?)\[\/U\]/g, '<u>$1</u>');
        return `<div class="flex gap-2.5 mb-0.5">
            <span class="text-[10px] text-gray-300 font-mono w-5 text-right shrink-0 pt-[3px] select-none">${i + 1}</span>
            <span class="flex-1">${rendered}</span>
        </div>`;
    }).join('');
}

document.addEventListener('alpine:init', () => {
    document.getElementById('p-body-text')?.addEventListener('input', renderPassagePreview);
    document.addEventListener('click', e => {
        if (e.target.closest('button[data-passage-preview]')) setTimeout(renderPassagePreview, 10);
    });
});
