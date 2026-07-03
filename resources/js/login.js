/**
 * Login Form Script
 * Handles password visibility toggle, form interactions,
 * remember me, and lockout countdown.
 */

document.addEventListener('DOMContentLoaded', function () {
    initPasswordToggle();
    initFormInteractions();
    initRememberMe();
    initLockoutCountdown();
});

/**
 * Initialize password visibility toggle.
 * Mendukung dua cara: tombol #togglePassword (blade inline)
 * dan tombol dengan class .toggle-password (pendekatan generik).
 */
function initPasswordToggle() {
    // Tombol spesifik di login.blade.php
    const toggleBtn = document.getElementById('togglePassword');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('visibilityIcon');
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    }

    // Pendekatan generik dengan class .toggle-password
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const input = document.getElementById('passwordInput') || document.getElementById('password');
            const icon = this.querySelector('.visibility-icon');
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                if (icon) icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                if (icon) icon.textContent = 'visibility';
            }
        });
    });
}

/**
 * Initialize form interactions.
 * Validasi dilakukan sepenuhnya di JS agar pesan konsisten
 * dan tooltip bawaan browser tidak muncul (form harus pakai novalidate).
 */
function initFormInteractions() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    loginForm.addEventListener('submit', function (e) {
        const usernameInput = document.getElementById('identity');
        const passwordInput = document.getElementById('passwordInput');

        const usernameEmpty = usernameInput.value.trim() === '';
        const passwordEmpty = passwordInput.value.trim() === '';

        // Bersihkan error lama sebelum validasi baru
        loginClearFieldError(usernameInput);
        loginClearFieldError(passwordInput);
        removeClientErrorBox();

        if (usernameEmpty && passwordEmpty) {
            e.preventDefault();
            showClientErrorBox('Harap isi username dan password.');
            return;
        }

        if (usernameEmpty) {
            e.preventDefault();
            showClientErrorBox('Username tidak boleh kosong.');
            return;
        }

        if (passwordEmpty) {
            e.preventDefault();
            showClientErrorBox('Password tidak boleh kosong.');
            return;
        }
    });

    // Hapus pesan error saat user mulai mengetik kembali
    const inputs = loginForm.querySelectorAll('input[type="text"], input[type="password"]');
    inputs.forEach(input => {
        input.addEventListener('input', function () {
            this.classList.remove('ring-2', 'ring-error');
            removeClientErrorBox();
        });
    });
}

/**
 * Tampilkan error box client-side (tampilan sama dengan server error box).
 */
function showClientErrorBox(message) {
    removeClientErrorBox();

    const box = document.createElement('div');
    box.id = 'clientErrorBox';
    box.className = 'p-4 bg-red-50 border border-red-300 rounded-xl';
    box.innerHTML = `
        <ul class="list-disc pl-5 space-y-1">
            <li class="text-red-700 text-sm font-medium">${message}</li>
        </ul>`;

    const loginForm = document.getElementById('loginForm');
    loginForm.insertBefore(box, loginForm.firstChild);
}

/**
 * Hapus error box client-side jika ada.
 */
function removeClientErrorBox() {
    const existing = document.getElementById('clientErrorBox');
    if (existing) existing.remove();
}

/**
 * Validate individual input (dipakai untuk blur validation opsional)
 */
function validateInput(input) {
    const value = input.value.trim();

    if (input.name === 'username') {
        if (value.length === 0) {
            loginShowFieldError(input, 'Username tidak boleh kosong.');
        } else {
            loginClearFieldError(input);
        }
    }

    if (input.name === 'password') {
        if (value.length === 0) {
            loginShowFieldError(input, 'Password tidak boleh kosong.');
        } else {
            loginClearFieldError(input);
        }
    }
}

/**
 * Show field error
 */
function loginShowFieldError(input, message) {
    const container = input.closest('.space-y-2');
    if (!container) return;

    const existingError = container.querySelector('.text-error.text-sm');
    if (existingError) {
        existingError.remove();
    }

    // Add error class to input
    input.classList.add('ring-2', 'ring-error');

    // Add error message
    const errorEl = document.createElement('p');
    errorEl.className = 'text-error text-sm';
    errorEl.textContent = message;
    container.appendChild(errorEl);
}

/**
 * Clear field error
 */
function loginClearFieldError(input) {
    const container = input.closest('.space-y-2');
    if (!container) return;

    // Remove error classes
    input.classList.remove('ring-2', 'ring-error');

    // Remove error message
    const errorEl = container.querySelector('.text-error.text-sm');
    if (errorEl) {
        errorEl.remove();
    }
}

/**
 * Initialize remember me functionality
 */
function initRememberMe() {
    const rememberCheckbox = document.getElementById('remember');
    const usernameInput = document.getElementById('identity');

    if (rememberCheckbox && usernameInput) {
        // Load saved username if exists
        const savedUsername = localStorage.getItem('remember_username');
        if (savedUsername) {
            usernameInput.value = savedUsername;
            rememberCheckbox.checked = true;
        }

        // Save username on form submit if remember me is checked
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function () {
                if (rememberCheckbox.checked) {
                    localStorage.setItem('remember_username', usernameInput.value);
                } else {
                    localStorage.removeItem('remember_username');
                }
            });
        }
    }
}

/**
 * Initialize lockout countdown.
 * Membaca data-lockout-seconds dari #serverErrorBox (di-render oleh blade),
 * lalu menjalankan hitung mundur realtime dan menonaktifkan form.
 */
function initLockoutCountdown() {
    const errorBox = document.getElementById('serverErrorBox');
    if (!errorBox) return;

    const lockoutSeconds = parseInt(errorBox.getAttribute('data-lockout-seconds') || '0');
    if (lockoutSeconds <= 0) return;

    const submitBtn    = document.getElementById('submitBtn');
    const submitText   = document.getElementById('submitText');
    const submitIcon   = document.getElementById('submitIcon');
    const errorMsg     = document.getElementById('errorMessage');
    const usernameInput = document.getElementById('identity');
    const passwordInput = document.getElementById('passwordInput');

    // Nonaktifkan form selama lockout
    if (submitBtn)     submitBtn.disabled = true;
    if (usernameInput) usernameInput.disabled = true;
    if (passwordInput) passwordInput.disabled = true;

    let remaining = lockoutSeconds;

    function updateCountdown() {
        if (remaining <= 0) {
            // Buka kunci form
            if (submitBtn)     { submitBtn.disabled = false; }
            if (usernameInput) usernameInput.disabled = false;
            if (passwordInput) passwordInput.disabled = false;
            if (submitText)    submitText.textContent = 'Masuk';
            if (submitIcon)    submitIcon.style.display = '';
            errorBox.remove();
            return;
        }

        const menit = Math.floor(remaining / 60);
        const detik = remaining % 60;
        const display = menit > 0
            ? `${menit} menit ${String(detik).padStart(2, '0')} detik`
            : `${detik} detik`;

        if (errorMsg) {
            errorMsg.textContent =
                `Akun terkunci karena terlalu banyak percobaan login gagal. Coba lagi dalam ${display}.`;
        }
        if (submitText) submitText.textContent = `Tunggu ${display}`;
        if (submitIcon) submitIcon.style.display = 'none';

        remaining--;
        setTimeout(updateCountdown, 1000);
    }

    updateCountdown();
}

/**
 * Utility function to add animation classes
 */
function addAnimation(element, animationClass) {
    element.classList.add(animationClass);

    element.addEventListener('animationend', function () {
        element.classList.remove(animationClass);
    }, { once: true });
}

/**
 * Export functions for external use
 */
export {
    initPasswordToggle,
    initFormInteractions,
    initRememberMe,
    initLockoutCountdown,
    validateInput,
    loginShowFieldError,
    loginClearFieldError,
    removeClientErrorBox,
    showClientErrorBox,
    addAnimation
};