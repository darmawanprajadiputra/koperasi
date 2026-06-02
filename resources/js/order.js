(function () {
    const DEFAULT_IMG = "/assets/pictures/produk.jpg";
    const PER_PAGE = 10;

    let allOrders = [];
    let currentPage = 1;
    let activeFilter = "all";

    const STATUS_LABEL = {
        pending: "DIPROSES",
        processing: "DIPROSES",
        completed: "SELESAI",
        cancelled: "DIBATALKAN",
    };
    const STATUS_CLASS = {
        pending: "bg-[#f5e6c8] text-[#7a4f00]",
        processing: "bg-[#f5e6c8] text-[#7a4f00]",
        completed: "bg-[#c8f5d5] text-[#005c20]",
        cancelled: "bg-red-100 text-red-700",
    };

    function statusLabel(s) {
        return STATUS_LABEL[s] ?? (s ? s.toUpperCase() : "-");
    }

    function statusClass(s) {
        return (
            STATUS_CLASS[s] ?? "bg-surface-container text-on-surface-variant"
        );
    }

    function formatRp(v) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(v);
    }

    function createOrderCard(order) {
        return `
            <div class="order-card bg-white rounded-2xl overflow-hidden flex items-stretch shadow-sm hover:shadow-md transition-shadow"
                 data-status="${order.payment_status ?? "pending"}">

                <div class="relative w-28 flex-shrink-0">
                    <img src="${DEFAULT_IMG}"
                         onerror="this.src='${DEFAULT_IMG}'"
                         alt="Pesanan"
                         class="w-full h-full object-cover" />
                    <div class="absolute top-3 left-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider ${statusClass(order.payment_status ?? "pending")}">
                            ${statusLabel(order.payment_status ?? "pending")}
                        </span>
                    </div>
                </div>

                <div class="flex-1 px-8 py-6 flex flex-col justify-between gap-4">

                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">No. Faktur</p>
                            <p class="font-manrope font-extrabold text-2xl text-on-surface">#${order.num_factur}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Total Pesanan</p>
                            <p class="font-manrope font-extrabold text-2xl text-primary">${formatRp(order.total_amount)}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex gap-8">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Tanggal</p>
                                <p class="text-sm font-semibold text-on-surface">${order.date}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Pemesan</p>
                                <p class="text-sm font-semibold text-on-surface">${order.name_customer}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Total Item</p>
                                <p class="text-sm font-semibold text-on-surface">${order.total_items} Produk</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Metode</p>
                                <p class="text-sm font-semibold text-on-surface">${order.payment_method ?? "-"}</p>
                            </div>
                        </div>
                        <button
                            class="detail-btn flex items-center gap-2 bg-on-surface text-surface px-6 py-3 rounded-full font-bold text-sm hover:opacity-80 active:scale-95 transition-all"
                            data-factur="${order.num_factur}">
                            Lihat Detail
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </button>
                    </div>

                </div>
            </div>
        `;
    }

    function render() {
        const container = document.getElementById("ordersContainer");
        const emptyState = document.getElementById("emptyState");
        const paginationWr = document.getElementById("paginationWrapper");
        const pagContainer = document.getElementById("paginationContainer");

        const filtered =
            activeFilter === "all"
                ? allOrders
                : allOrders.filter((o) => {
                      if (activeFilter === "process")
                          return ["pending", "processing"].includes(
                              o.payment_status,
                          );
                      if (activeFilter === "completed")
                          return o.payment_status === "completed";
                      if (activeFilter === "cancelled")
                          return o.payment_status === "cancelled";
                      return true;
                  });

        const totalPages = Math.ceil(filtered.length / PER_PAGE) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const slice = filtered.slice(
            (currentPage - 1) * PER_PAGE,
            currentPage * PER_PAGE,
        );

        if (slice.length === 0) {
            container.classList.add("hidden");
            paginationWr.classList.add("hidden");
            emptyState.classList.remove("hidden");
            return;
        }

        emptyState.classList.add("hidden");
        container.classList.remove("hidden");
        container.innerHTML = slice.map(createOrderCard).join("");

        container.querySelectorAll(".detail-btn").forEach((btn) => {
            btn.addEventListener("click", () => {
                window.location.href = "/order/" + btn.dataset.factur;
            });
        });

        if (totalPages > 1) {
            paginationWr.classList.remove("hidden");
            pagContainer.innerHTML = Array.from(
                { length: totalPages },
                (_, i) => `
                <button class="page-btn w-9 h-9 rounded-full text-sm font-bold transition-all
                    ${
                        i + 1 === currentPage
                            ? "bg-tertiary text-on-tertiary shadow"
                            : "bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest"
                    }"
                    data-page="${i + 1}">${i + 1}</button>
            `,
            ).join("");

            pagContainer.querySelectorAll(".page-btn").forEach((btn) => {
                btn.addEventListener("click", () => {
                    currentPage = parseInt(btn.dataset.page);
                    render();
                });
            });

            document.getElementById("prevBtn").disabled = currentPage <= 1;
            document.getElementById("nextBtn").disabled =
                currentPage >= totalPages;
        } else {
            paginationWr.classList.add("hidden");
        }
    }

    async function loadOrders() {
        const loading = document.getElementById("loadingState");
        try {
            const res = await fetch("/api/get_orders");
            const data = await res.json();

            loading.classList.add("hidden");

            if (data.success && data.orders.length > 0) {
                allOrders = data.orders;
                render();
            } else {
                document
                    .getElementById("emptyState")
                    .classList.remove("hidden");
            }
        } catch (err) {
            console.error("[ORDER]", err);
            loading.classList.add("hidden");
            document.getElementById("emptyState").classList.remove("hidden");
        }
    }

    function setupFilters() {
        document.querySelectorAll(".filter-btn").forEach((btn) => {
            btn.addEventListener("click", () => {
                document.querySelectorAll(".filter-btn").forEach((b) => {
                    b.classList.remove("active");
                });

                btn.classList.add("active");

                activeFilter = btn.dataset.filter;
                currentPage = 1;
                render();
            });
        });

        document.getElementById("prevBtn").addEventListener("click", () => {
            currentPage--;
            render();
        });

        document.getElementById("nextBtn").addEventListener("click", () => {
            currentPage++;
            render();
        });
    }

    const flash = document.getElementById("flashSuccess");
    if (flash) setTimeout(() => (flash.style.display = "none"), 5000);

    document.addEventListener("DOMContentLoaded", () => {
        setupFilters();
        loadOrders();
    });
})();
