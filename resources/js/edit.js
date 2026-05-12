// ─────────────────────────────────────────────────────────────────────────────
// edit.js  –  Script untuk halaman Edit Barang (edit_item.blade.php)
// ─────────────────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {

    // ── Image Upload ──────────────────────────────────────────────────────────
    const imageInput    = document.getElementById('image');
    const uploadBox     = document.getElementById('imageUploadBox');
    const previewImage  = document.getElementById('previewImage');
    const uploadSuccess = document.getElementById('uploadSuccess');

    if (uploadBox) {
        uploadBox.addEventListener('click', () => imageInput.click());

        uploadBox.addEventListener('dragover', e => e.preventDefault());

        uploadBox.addEventListener('drop', e => {
            e.preventDefault();
            if (e.dataTransfer.files.length > 0) {
                imageInput.files = e.dataTransfer.files;
                handleImageChange();
            }
        });

        imageInput.addEventListener('change', handleImageChange);
    }

    function handleImageChange() {
        const file = imageInput.files[0];
        if (!file) return;

        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            alert('Hanya file JPG dan PNG yang diizinkan');
            imageInput.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file tidak boleh lebih dari 5MB');
            imageInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            previewImage.src = e.target.result;
            uploadSuccess.classList.remove('hidden');
            uploadSuccess.classList.add('flex');
        };
        reader.readAsDataURL(file);
    }

    // ── Unit Autocomplete ─────────────────────────────────────────────────────
    const wrapper   = document.getElementById('unitWrapper');
    const unitInput = document.getElementById('unitInput');
    const unitId    = document.getElementById('unitId');

    if (!wrapper || !unitInput || !unitId) return;

    // Data unit + nilai awal di-inject dari blade lewat window.EDIT_CONFIG
    const cfg      = window.EDIT_CONFIG || {};
    const allUnits = cfg.units || [];

    // Pre-fill unit jika produk sudah punya unit
    if (cfg.currentUnitId && cfg.currentUnitName) {
        unitInput.value = cfg.currentUnitName;
        unitId.value    = cfg.currentUnitId;
    }

    // Buat dropdown, pasang ke <body> agar tidak terpotong overflow
    const dropdown = document.createElement('ul');
    dropdown.id = 'unitDropdown';
    dropdown.style.cssText = [
        'display:none',
        'position:fixed',
        'z-index:9999',
        'background:#ffffff',
        'border-radius:12px',
        'box-shadow:0 8px 32px rgba(0,0,0,0.13)',
        'border:1px solid #e0e0e0',
        'overflow-y:auto',
        'max-height:220px',
        'padding:4px 0',
        'list-style:none',
        'margin:0',
    ].join(';');
    document.body.appendChild(dropdown);

    let activeIndex = -1;
    let isOpen      = false;
    let isSelected  = !!cfg.currentUnitId; // sudah terpilih jika ada nilai awal

    function positionDropdown() {
        const rect = unitInput.getBoundingClientRect();
        dropdown.style.top   = (rect.bottom + 4) + 'px';
        dropdown.style.left  = rect.left + 'px';
        dropdown.style.width = rect.width + 'px';
    }

    function openDropdown(results) {
        dropdown.innerHTML = '';
        activeIndex = -1;

        if (results.length === 0) {
            const empty = document.createElement('li');
            empty.style.cssText = 'display:flex;align-items:center;gap:8px;padding:12px 16px;font-size:14px;color:#888;';
            empty.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px">search_off</span> Tidak ada satuan yang cocok';
            dropdown.appendChild(empty);
        } else {
            results.forEach(unit => {
                const li = document.createElement('li');
                li.dataset.id   = unit.id;
                li.dataset.name = unit.name_unit;
                li.style.cssText = 'display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:14px;cursor:pointer;transition:background .15s;';
                li.innerHTML = `
                    <span class="material-symbols-outlined" style="font-size:16px;color:var(--md-sys-color-primary,#1b6b5a)">straighten</span>
                    <span style="font-weight:500">${highlight(unit.name_unit, unitInput.value)}</span>
                    <span style="margin-left:auto;font-size:12px;color:#9e9e9e">${unit.slug ?? ''}</span>`;
                li.addEventListener('mouseover', () => li.style.background = '#f5f5f5');
                li.addEventListener('mouseout',  () => li.style.background = '');
                li.addEventListener('mousedown', e => { e.preventDefault(); selectUnit(unit); });
                dropdown.appendChild(li);
            });
        }

        positionDropdown();
        dropdown.style.display = 'block';
        isOpen = true;
    }

    function closeDropdown() {
        dropdown.style.display = 'none';
        dropdown.innerHTML = '';
        activeIndex = -1;
        isOpen = false;
    }

    function highlight(text, query) {
        if (!query) return text;
        const esc = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return text.replace(new RegExp(`(${esc})`, 'gi'),
            '<mark style="background:rgba(180,230,210,.6);border-radius:3px;padding:0 2px">$1</mark>');
    }

    function selectUnit(unit) {
        unitInput.value = unit.name_unit;
        unitId.value    = unit.id;
        isSelected      = true;
        closeDropdown();
    }

    function getFiltered() {
        const q = unitInput.value.trim().toLowerCase();
        return q
            ? allUnits.filter(u =>
                u.name_unit.toLowerCase().includes(q) ||
                (u.slug && u.slug.toLowerCase().includes(q)))
            : allUnits;
    }

    function updateActive(newIndex, items) {
        items.forEach(li => li.style.background = '');
        activeIndex = newIndex;
        if (newIndex >= 0 && items[newIndex]) {
            items[newIndex].style.background = '#f0f0f0';
            items[newIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    unitInput.addEventListener('focus', () => openDropdown(getFiltered()));

    unitInput.addEventListener('input', () => {
        isSelected   = false;
        unitId.value = '';
        openDropdown(getFiltered());
    });

    unitInput.addEventListener('blur', () => {
        setTimeout(() => {
            closeDropdown();
            // Jika user mengetik tapi tidak memilih dari dropdown, kosongkan unit_id
            if (!isSelected) unitId.value = '';
        }, 160);
    });

    unitInput.addEventListener('keydown', e => {
        if (!isOpen) return;
        const items = [...dropdown.querySelectorAll('li[data-id]')];
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            updateActive(Math.min(activeIndex + 1, items.length - 1), items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            updateActive(Math.max(activeIndex - 1, 0), items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && items[activeIndex]) {
                selectUnit({ id: items[activeIndex].dataset.id, name_unit: items[activeIndex].dataset.name });
            }
        } else if (e.key === 'Escape') {
            closeDropdown();
        }
    });

    window.addEventListener('scroll', () => { if (isOpen) positionDropdown(); }, true);
    window.addEventListener('resize', () => { if (isOpen) positionDropdown(); });

    document.addEventListener('click', e => {
        if (!wrapper.contains(e.target) && e.target !== dropdown && !dropdown.contains(e.target)) {
            closeDropdown();
        }
    });

});