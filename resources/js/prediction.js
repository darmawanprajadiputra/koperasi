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
    const leadTime = leadTimeInput.value;

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
    } catch (error) {
        showError("Gagal terhubung ke server: " + error.message);
    }
}

function showLoading() {
    setState("loading");
    const btn = document.getElementById("btn-predict");
    btn.disabled = true;
    document.getElementById("btn-text").textContent = "Menghitung...";
}

function showResult(data) {
    document.getElementById("result-product-name").textContent =
        data.product_name;
    document.getElementById("result-qty").textContent = data.rekomendasi_stok;
    document.getElementById("result-rop").textContent = data.rop;
    document.getElementById("result-date").textContent =
        "Diprediksi: " + data.tanggal;

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
