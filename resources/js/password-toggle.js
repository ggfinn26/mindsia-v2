window.toggleAllPasswords = function(btn) {
    // Identify all password inputs on the page
    // First time, they will have type="password"
    document.querySelectorAll('input[type="password"]').forEach(el => {
        el.setAttribute('data-is-pwd', '1');
    });
    
    const allPasswordInputs = document.querySelectorAll('input[data-is-pwd="1"]');
    if (allPasswordInputs.length === 0) return;
    
    // Determine target type based on the first one
    const targetType = allPasswordInputs[0].type === 'password' ? 'text' : 'password';
    
    // Update all inputs
    allPasswordInputs.forEach(el => {
        el.type = targetType;
    });
    
    // Update all buttons
    document.querySelectorAll('button.pwd-toggle').forEach(b => {
        if (b.innerHTML.includes('material-symbols-outlined')) {
            // Material icons
            b.innerHTML = targetType === 'password' 
                ? '<span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>' 
                : '<span class="material-symbols-outlined text-[18px] text-gray-500">visibility_off</span>';
        } else {
            // SVGs
            const eye = b.querySelector('.eye');
            const eyeOff = b.querySelector('.eye-off');
            if(eye && eyeOff) {
                if (targetType === 'text') {
                    eye.classList.add('hidden');
                    eyeOff.classList.remove('hidden');
                } else {
                    eye.classList.remove('hidden');
                    eyeOff.classList.add('hidden');
                }
            }
        }
    });
};
