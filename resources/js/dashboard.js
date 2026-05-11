/**
 * Dashboard Module - Handles dashboard page interactions
 */

// Initialize dashboard when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    console.log("[v0] Dashboard module loaded");
    initializeDashboard();
});

/**
 * Initialize dashboard features
 */
function initializeDashboard() {
    // Check if we're on a dashboard page
    const dashboardContainer = document.querySelector(".dashboard-container");
    if (!dashboardContainer) return;

    console.log("[v0] Initializing dashboard...");
    initializeSidebarToggle();
    initializeSearch();
    initializeActivityAnimations();
    initializeChartAnimations();
    initializeLogout();
    initializeMobileMenu();
}

/**
 * Initialize sidebar toggle for mobile
 */
function initializeSidebarToggle() {
    const sidebar = document.querySelector(".dashboard-sidebar");
    const toggleBtn = document.querySelector('[data-toggle="sidebar"]');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("active");
        });
    }

    // Close sidebar when clicking outside
    document.addEventListener("click", function (event) {
        const isSidebar = event.target.closest(".dashboard-sidebar");
        const isToggle = event.target.closest('[data-toggle="sidebar"]');

        if (!isSidebar && !isToggle && sidebar && window.innerWidth <= 768) {
            sidebar.classList.remove("active");
        }
    });
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
    const searchInput = document.querySelector(".search-input");

    if (searchInput) {
        searchInput.addEventListener("keyup", function (event) {
            const query = event.target.value.toLowerCase();
            handleSearch(query);
        });
    }
}

/**
 * Handle search logic
 */
function handleSearch(query) {
    console.log("[v0] Searching for:", query);

    if (query.length === 0) {
        return;
    }

    const searchBox = document.querySelector(".search-box");
    if (searchBox) {
        searchBox.classList.add("searching");
    }

    setTimeout(() => {
        if (searchBox) {
            searchBox.classList.remove("searching");
        }
    }, 300);
}

/**
 * Initialize activity animations
 */
function initializeActivityAnimations() {
    const activityItems = document.querySelectorAll(".activity-item");

    if (activityItems.length === 0) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.animation =
                        "fadeInLeft 0.6s ease forwards";
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.1 },
    );

    activityItems.forEach((item, index) => {
        item.style.opacity = "0";
        item.style.animationDelay = `${index * 0.1}s`;
        observer.observe(item);
    });
}

/**
 * Initialize chart animations
 */
function initializeChartAnimations() {
    const bars = document.querySelectorAll(".bar");

    if (bars.length === 0) return;

    bars.forEach((bar, index) => {
        const height = bar.style.height;
        bar.style.height = "0";

        setTimeout(
            () => {
                bar.style.transition = "height 0.6s ease-out";
                bar.style.height = height;
            },
            50 + index * 50,
        );
    });

    bars.forEach((bar) => {
        const container = bar.parentElement;

        container.addEventListener("mouseenter", function () {
            bar.style.opacity = "0.8";
        });

        container.addEventListener("mouseleave", function () {
            bar.style.opacity = "1";
        });
    });
}

/**
 * Initialize logout functionality
 */
function initializeLogout() {
    const userAvatar = document.querySelector(".user-avatar");
    const logoutForm = document.getElementById("logoutForm");

    if (userAvatar && logoutForm) {
        userAvatar.addEventListener("click", function () {
            if (confirm("Apakah Anda yakin ingin logout?")) {
                logoutForm.submit();
            }
        });
    }
}

/**
 * Initialize mobile menu
 */
function initializeMobileMenu() {
    const navItems = document.querySelectorAll(".nav-item");
    const sidebar = document.querySelector(".dashboard-sidebar");

    navItems.forEach((item) => {
        item.addEventListener("click", function () {
            navItems.forEach((nav) => nav.classList.remove("active"));
            this.classList.add("active");

            if (window.innerWidth <= 768 && sidebar) {
                sidebar.classList.remove("active");
            }
        });
    });
}

/**
 * Utility: Format currency
 */
export function formatCurrency(value) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
}

/**
 * Utility: Format date
 */
export function formatDate(date) {
    return new Intl.DateTimeFormat("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    }).format(new Date(date));
}

/**
 * Utility: Get time ago
 */
export function getTimeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
    const intervals = {
        tahun: 31536000,
        bulan: 2592000,
        minggu: 604800,
        hari: 86400,
        jam: 3600,
        menit: 60,
        detik: 1,
    };

    for (const [name, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return `${interval} ${name} yang lalu`;
        }
    }

    return "baru saja";
}

/**
 * Inject animations CSS
 */
function injectAnimationsCSS() {
    const style = document.createElement("style");
    style.textContent = `
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .dashboard-sidebar.active {
            transform: translateX(0) !important;
        }

        .searching {
            animation: pulse 1s infinite;
        }
    `;
    document.head.appendChild(style);
}

injectAnimationsCSS();
