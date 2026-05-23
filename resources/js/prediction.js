document.addEventListener("DOMContentLoaded", () => {
    if (!document.getElementById("btn-predict")) return;
    document
        .getElementById("btn-predict")
        .addEventListener("click", handlePrediction);
});

async function handlePrediction() {
    const productSelect = document.getElementById("product-select");
    const leadTimeInput = document.getElementById("lead-time");

    const productId = productSelect.value;
    const leadTime  = leadTimeInput.value;

    if (!productId) {
        showError("Silakan pilih produk terlebih dahulu.");
        return;
    }
    if (!leadTime || leadTime < 1) {
        showError("Lead time harus diisi minimal 1 hari.");
        return;
    }

    showLoading();

    try {
        const response = await fetch(PREDICT_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": CSRF_TOKEN,
                Accept: "application/json",
            },
            body: JSON.stringify({
                product_id: productId,
                lead_time: parseInt(leadTime),
            }),
        });

        const json = await response.json();

        if (!response.ok || !json.success) {
            showError(json.message || "Terjadi kesalahan saat prediksi.");
            return;
        }

        showResult(json.data);
        updateHistoryRow(json.data);   // ← update tabel riwayat tanpa reload
    } catch (error) {
        showError("Gagal terhubung ke server: " + error.message);
    }
}

/* ------------------------------------------------------------------ */
/*  Update baris tabel riwayat secara langsung setelah prediksi sukses */
/* ------------------------------------------------------------------ */
function updateHistoryRow(data) {
    const row = document.getElementById("history-row-" + data.product_id);
    if (!row) return;

    const currentStock = data.current_stock ?? 0;
    const rop          = data.rop;
    const safetyStock  = data.rekomendasi_stok;
    const isAman       = currentStock >= rop;

    // Safety Stock
    const ssEl = row.querySelector(".history-safety-stock");
    if (ssEl) {
        ssEl.outerHTML =
            `<span class="history-safety-stock font-bold text-on-surface">${safetyStock}</span>` +
            `<span class="text-xs text-on-surface-variant ml-1">unit</span>`;
    }

    // Stok Tersedia (nilai ini sudah dari DB, tidak berubah di sini — biarkan)

    // Stok ROP
    const ropEl = row.querySelector(".history-rop");
    if (ropEl) {
        ropEl.outerHTML =
            `<span class="history-rop font-bold text-on-surface">${rop}</span>` +
            `<span class="text-xs text-on-surface-variant ml-1">unit</span>`;
    }

    // Tanggal Prediksi
    const tglEl = row.querySelector(".history-tanggal");
    if (tglEl) {
        tglEl.outerHTML =
            `<span class="history-tanggal text-xs text-on-surface-variant">${data.tanggal}</span>`;
    }

    // Status
    const statusEl = row.querySelector(".history-status");
    if (statusEl) {
        if (isAman) {
            statusEl.outerHTML =
                `<span class="history-status inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">` +
                `<span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>Aman</span>`;
        } else {
            statusEl.outerHTML =
                `<span class="history-status inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">` +
                `<span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>Perlu Restock</span>`;
        }
    }

    // Highlight baris yang baru diupdate
    row.classList.add("bg-primary/5");
    setTimeout(() => row.classList.remove("bg-primary/5"), 2000);
}

/* ------------------------------------------------------------------ */

function showLoading() {
    setState("loading");
    const btn = document.getElementById("btn-predict");
    btn.disabled = true;
    document.getElementById("btn-text").textContent = "Menghitung...";
}

function showResult(data) {
    document.getElementById("result-product-name").textContent = data.product_name;
    document.getElementById("result-qty").textContent          = data.rekomendasi_stok;
    document.getElementById("result-rop").textContent          = data.rop;
    document.getElementById("result-date").textContent         = "Diprediksi: " + data.tanggal;

    const currentStock = data.current_stock ?? 0;
    const rop          = data.rop;
    const isSafe       = currentStock >= rop;

    const safeEl   = document.getElementById("result-stock-safe");
    const dangerEl = document.getElementById("result-stock-danger");

    if (isSafe) {
        document.getElementById("result-current-stock-safe").textContent = currentStock;
        safeEl.classList.remove("hidden");
        dangerEl.classList.add("hidden");
    } else {
        document.getElementById("result-current-stock-danger").textContent = currentStock;
        dangerEl.classList.remove("hidden");
        safeEl.classList.add("hidden");
    }

    setState("data");
    resetButton();

    document
        .getElementById("result-data")
        .scrollIntoView({ behavior: "smooth", block: "start" });
}

function showError(message) {
    document.getElementById("result-error-msg").textContent = message;
    setState("error");
    resetButton();
}

function setState(state) {
    const states = ["empty", "loading", "data", "error"];
    states.forEach((s) => {
        const el = document.getElementById("result-" + s);
        if (el) el.classList.toggle("hidden", s !== state);
    });
}

function resetButton() {
    const btn = document.getElementById("btn-predict");
    btn.disabled = false;
    document.getElementById("btn-text").textContent = "Hitung Prediksi";
}