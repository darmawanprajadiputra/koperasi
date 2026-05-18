(function () {
    const DEFAULT_IMG = "/assets/pictures/produk.jpg";

    // ── Helpers ───────────────────────────────────────────────────────────────
    function formatRp(v) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(v);
    }

    function formatDatetime(iso) {
        if (!iso) return "—";
        const d = new Date(iso);
        return (
            d.toLocaleDateString("id-ID", {
                day: "2-digit",
                month: "long",
                year: "numeric",
            }) +
            " pukul " +
            d.toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
            }) +
            " WIB"
        );
    }

    const STATUS_LABEL = {
        pending: "DIPROSES",
        processing: "DIPROSES",
        completed: "SELESAI",
        cancelled: "DIBATALKAN",
    };
    const STATUS_BADGE_CLASS = {
        pending: "bg-[#f5e6c8] text-[#7a4f00]",
        processing: "bg-[#f5e6c8] text-[#7a4f00]",
        completed: "bg-[#c8f5d5] text-[#005c20]",
        cancelled: "bg-red-100 text-red-700",
    };

    // ── Ambil num_factur dari URL: /order/{num_factur} ────────────────────────
    const pathParts = window.location.pathname.split("/").filter(Boolean);
    const numFactur = pathParts[pathParts.length - 1];

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const loadingEl = document.getElementById("loadingState");
    const errorEl = document.getElementById("errorState");
    const detailEl = document.getElementById("detailContent");
    const completeSection = document.getElementById("completeOrderSection");
    const completeBtn = document.getElementById("completeOrderBtn");
    const confirmModal = document.getElementById("confirmModal");
    const cancelConfirmBtn = document.getElementById("cancelConfirm");
    const confirmCompleteBtn = document.getElementById("confirmComplete");
    const confirmSpinner = document.getElementById("confirmSpinner");

    // ── Render detail ─────────────────────────────────────────────────────────
    function renderDetail(data, transactions) {
        const status = data.payment_status ?? "pending";
        const isPending = ["pending", "processing"].includes(status);
        const isCompleted = status === "completed";

        // Header
        document.getElementById("headerInvoice").textContent =
            "#" + data.num_factur;
        document.getElementById("headerDate").textContent =
            "Diterima pada " + formatDatetime(data.created_at);

        const badge = document.getElementById("headerBadge");
        badge.textContent =
            STATUS_LABEL[status] ?? (status ? status.toUpperCase() : "—");
        badge.className =
            "px-3 py-1 rounded-full text-[11px] font-bold " +
            (STATUS_BADGE_CLASS[status] ??
                "bg-surface-container text-on-surface-variant");

        // Journey / progress
        document.getElementById("step1Date").textContent = data.date ?? "—";

        if (isCompleted) {
            document.getElementById("progressLine").style.width = "100%";

            const s2icon = document.getElementById("step2Icon");
            s2icon.className =
                "w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center z-10 shadow-md";
            s2icon.innerHTML =
                '<span class="material-symbols-outlined text-sm" style="font-variation-settings:\'FILL\' 1">check</span>';
            document.getElementById("step2Label").className =
                "mt-4 text-xs font-bold text-on-surface";
            document.getElementById("step2Sub").textContent = "—";

            const s3 = document.getElementById("step3");
            s3.classList.remove("opacity-40");
            const s3icon = document.getElementById("step3Icon");
            s3icon.className =
                "w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center z-10 shadow-md";
            s3icon.innerHTML =
                '<span class="material-symbols-outlined text-sm" style="font-variation-settings:\'FILL\' 1">check</span>';
            document.getElementById("step3Sub").textContent = data.date ?? "—";
        } else {
            document.getElementById("progressLine").style.width = "50%";
        }

        // Info pemesan
        document.getElementById("infoName").textContent =
            data.name_customer ?? "—";
        document.getElementById("infoPhone").textContent =
            data.no_telephone ?? "—";
        document.getElementById("infoRecipient").textContent =
            data.recipient ?? data.name_customer ?? "—";
        document.getElementById("infoAddress").textContent =
            data.address ?? "—";

        // Payment summary
        document.getElementById("payMethod").textContent =
            data.payment_method ?? "—";
        document.getElementById("payStatus").textContent =
            STATUS_LABEL[status] ?? "—";
        document.getElementById("payTotal").textContent = formatRp(
            data.total_amount,
        );
        document.getElementById("payItems").textContent =
            transactions.length + " Produk";

        if (data.notes) {
            document.getElementById("notesRow").classList.remove("hidden");
            document.getElementById("payNotes").textContent = data.notes;
        }

        // Product list
        document.getElementById("itemsCount").textContent =
            transactions.length + " Item";
        document.getElementById("productList").innerHTML = transactions
            .map(
                (t) => `
            <div class="p-8 flex items-center gap-6 hover:bg-slate-50/50 transition-colors">
                <div class="w-24 h-24 rounded-xl overflow-hidden bg-surface-container flex-shrink-0">
                    <img src="${t.product ? t.product.image : DEFAULT_IMG}"
                         onerror="this.src='${DEFAULT_IMG}'"
                         alt="${t.product ? t.product.name_product : "Produk"}"
                         class="w-full h-full object-cover" />
                </div>
                <div class="flex-1">
                    <h4 class="font-manrope text-lg font-bold text-on-surface">
                        ${t.product ? t.product.name_product : "—"}
                    </h4>
                    <p class="text-sm text-on-surface-variant mt-1">Qty: ${t.total_item}</p>
                </div>
                <div class="text-right min-w-[120px]">
                    <p class="text-sm text-on-surface-variant">
                        ${formatRp(t.total_amount / t.total_item)} × ${t.total_item}
                    </p>
                    <p class="font-manrope text-lg font-bold text-primary mt-1">
                        ${formatRp(t.total_amount)}
                    </p>
                </div>
            </div>
        `,
            )
            .join("");

        // Tampilkan tombol selesai hanya jika masih pending
        if (isPending) {
            completeSection.classList.remove("hidden");
        }

        loadingEl.classList.add("hidden");
        detailEl.classList.remove("hidden");
    }

    // ── Load dari API ─────────────────────────────────────────────────────────
    async function loadDetail() {
        try {
            const res = await fetch("/api/orders/" + numFactur);
            const data = await res.json();

            if (!data.success) throw new Error("not found");

            const first = data.transactions[0];
            const total = data.transactions.reduce(
                (sum, t) => sum + parseFloat(t.total_amount),
                0,
            );

            const summary = {
                num_factur: data.num_factur,
                name_customer: first.name_customer,
                no_telephone: first.no_telephone,
                address: first.address,
                recipient: first.recipient,
                payment_method: first.payment_method,
                payment_status: first.payment_status,
                notes: first.notes,
                total_amount: total,
                date: new Date(first.created_at).toLocaleDateString("id-ID", {
                    day: "2-digit",
                    month: "long",
                    year: "numeric",
                }),
                created_at: first.created_at,
            };

            renderDetail(summary, data.transactions);
        } catch (err) {
            console.error("[DETAIL]", err);
            loadingEl.classList.add("hidden");
            errorEl.classList.remove("hidden");
        }
    }

    // ── Tandai pesanan selesai ────────────────────────────────────────────────
    completeBtn.addEventListener("click", () => {
        confirmModal.classList.remove("hidden");
    });

    cancelConfirmBtn.addEventListener("click", () => {
        confirmModal.classList.add("hidden");
    });

    confirmModal.addEventListener("click", (e) => {
        if (e.target === confirmModal) confirmModal.classList.add("hidden");
    });

    confirmCompleteBtn.addEventListener("click", async () => {
        confirmCompleteBtn.disabled = true;
        confirmSpinner.classList.remove("hidden");

        try {
            const res = await fetch("/api/orders/" + numFactur + "/status", {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        (
                            document.querySelector('meta[name="csrf-token"]') ||
                            {}
                        ).content ?? "",
                    Accept: "application/json",
                },
                body: JSON.stringify({ status: "completed" }),
            });
            const data = await res.json();

            if (data.success) {
                confirmModal.classList.add("hidden");
                completeSection.classList.add("hidden");

                const badge = document.getElementById("headerBadge");
                badge.textContent = "SELESAI";
                badge.className =
                    "px-3 py-1 rounded-full text-[11px] font-bold bg-[#c8f5d5] text-[#005c20]";

                document.getElementById("payStatus").textContent = "SELESAI";
                document.getElementById("progressLine").style.width = "100%";

                const s2icon = document.getElementById("step2Icon");
                s2icon.className =
                    "w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center z-10 shadow-md";
                s2icon.innerHTML =
                    '<span class="material-symbols-outlined text-sm" style="font-variation-settings:\'FILL\' 1">check</span>';
                document.getElementById("step2Label").className =
                    "mt-4 text-xs font-bold text-on-surface";

                const s3 = document.getElementById("step3");
                s3.classList.remove("opacity-40");
                const s3icon = document.getElementById("step3Icon");
                s3icon.className =
                    "w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center z-10 shadow-md";
                s3icon.innerHTML =
                    '<span class="material-symbols-outlined text-sm" style="font-variation-settings:\'FILL\' 1">check</span>';
                document.getElementById("step3Sub").textContent =
                    new Date().toLocaleDateString("id-ID", {
                        day: "2-digit",
                        month: "long",
                        year: "numeric",
                    });
            } else {
                alert("Gagal memperbarui status pesanan. Silakan coba lagi.");
                confirmCompleteBtn.disabled = false;
                confirmSpinner.classList.add("hidden");
            }
        } catch (err) {
            console.error("[COMPLETE]", err);
            alert("Terjadi kesalahan jaringan. Silakan coba lagi.");
            confirmCompleteBtn.disabled = false;
            confirmSpinner.classList.add("hidden");
        }
    });

    // ── Init ──────────────────────────────────────────────────────────────────
    document.addEventListener("DOMContentLoaded", loadDetail);
})();
