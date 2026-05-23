/**
 * datamaster.js
 * Interactivity for the Data Master Portal page.
 */

/* ============================================================
   MODAL HELPERS
   ============================================================ */

/**
 * Open the shared edit modal.
 * @param {string} title       - Modal header text
 * @param {string} action      - Form action URL
 * @param {string} inputName   - The hidden name attribute sent to the server
 * @param {string} currentValue - Pre-filled value
 */
function openEditModal(title, action, inputName, currentValue) {
    const overlay   = document.getElementById('editModal');
    const form      = document.getElementById('editForm');
    const input     = document.getElementById('editInput');
    const titleEl   = document.getElementById('editModalTitle');

    titleEl.textContent    = title;
    form.action            = action;
    input.name             = inputName;
    input.value            = currentValue;

    overlay.classList.add('is-open');
    // Focus after transition
    setTimeout(() => input.focus(), 120);
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('is-open');
}

/* ============================================================
   CATEGORY EDIT
   ============================================================ */
function startEditCategory(id, currentName) {
    // Route: PUT /datamaster/category/{id}
    const action = `/datamaster/category/${id}`;
    openEditModal('Edit Kategori', action, 'name_categories', currentName);
}

/* ============================================================
   UNIT EDIT
   ============================================================ */
function startEditUnit(id, currentName) {
    // Route: PUT /datamaster/unit/{id}
    const action = `/datamaster/unit/${id}`;
    openEditModal('Edit Satuan', action, 'name_unit', currentName);
}

/* ============================================================
   CLOSE MODAL ON OVERLAY CLICK
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('editModal');

    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeEditModal();
        });
    }

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEditModal();
    });

    /* ============================================================
       BUTTON RIPPLE EFFECT
       ============================================================ */
    document.querySelectorAll('.dm-btn').forEach((btn) => {
        btn.addEventListener('click', function (e) {
            const rect   = this.getBoundingClientRect();
            const size   = Math.max(rect.width, rect.height);
            const x      = e.clientX - rect.left - size / 2;
            const y      = e.clientY - rect.top  - size / 2;

            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                border-radius: 50%;
                background: rgba(255,255,255,0.25);
                transform: scale(0);
                animation: dm-ripple 0.5s ease-out forwards;
                pointer-events: none;
            `;

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            ripple.addEventListener('animationend', () => ripple.remove());
        });
    });

    /* ============================================================
       FLASH MESSAGE AUTO-DISMISS
       ============================================================ */
    const alerts = document.querySelectorAll('.dm-alert');
    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity    = '0';
            alert.style.transform  = 'translateY(-8px)';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    /* ============================================================
       INPUT FOCUS EXPAND
       ============================================================ */
    document.querySelectorAll('.dm-form .dm-input').forEach((input) => {
        input.addEventListener('focus', () => {
            input.closest('.dm-form').style.transition = 'transform 0.2s ease';
            input.closest('.dm-form').style.transform  = 'scale(1.01)';
        });
        input.addEventListener('blur', () => {
            input.closest('.dm-form').style.transform = 'scale(1)';
        });
    });
});

/* ============================================================
   RIPPLE KEYFRAME (injected once)
   ============================================================ */
(function injectRippleKeyframes() {
    if (document.getElementById('dm-ripple-styles')) return;
    const style = document.createElement('style');
    style.id    = 'dm-ripple-styles';
    style.textContent = `
        @keyframes dm-ripple {
            to {
                transform: scale(2.5);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
})();