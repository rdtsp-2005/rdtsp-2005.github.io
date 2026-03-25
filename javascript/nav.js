document.addEventListener("DOMContentLoaded", () => {

    // ── Active nav link highlight ──────────────────────────────────────────
    const bodyId = document.body.id;
    document.querySelectorAll(".links a[data-active]").forEach(link => {
        if (link.dataset.active === bodyId) link.classList.add("active");
    });

    // ── Elements ───────────────────────────────────────────────────────────
    const burgerBtn = document.getElementById("burger-btn");
    const mobileMenu = document.getElementById("mobile-menu");
    const overlay = document.getElementById("menu-overlay");
    const closeBtn = document.getElementById("mobile-close-btn");
    const catBtn = document.querySelector(".category-dropdown-btn");
    const catContainer = document.querySelector(".category-dropdown-container");

    // ── Open mobile menu ───────────────────────────────────────────────────
    function openMenu() {
        mobileMenu.classList.add("open");
        overlay.classList.add("visible");
        burgerBtn.classList.add("is-open");
        burgerBtn.setAttribute("aria-expanded", "true");
        mobileMenu.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden"; // prevent background scroll
    }

    // ── Close mobile menu ──────────────────────────────────────────────────
    function closeMenu() {
        mobileMenu.classList.remove("open");
        overlay.classList.remove("visible");
        burgerBtn.classList.remove("is-open");
        burgerBtn.setAttribute("aria-expanded", "false");
        mobileMenu.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    }

    // ── Burger button toggle ───────────────────────────────────────────────
    if (burgerBtn) {
        burgerBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            mobileMenu && mobileMenu.classList.contains("open") ? closeMenu() : openMenu();
        });
    }

    // ── Close button inside menu ───────────────────────────────────────────
    if (closeBtn) closeBtn.addEventListener("click", closeMenu);

    // ── Overlay click to close ─────────────────────────────────────────────
    if (overlay) overlay.addEventListener("click", closeMenu);

    // ── ESC key to close ──────────────────────────────────────────────────
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeMenu();
    });

    // ── Desktop: Categories dropdown ───────────────────────────────────────
    if (catBtn) {
        catBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            const open = catContainer.classList.toggle("open");
            catBtn.setAttribute("aria-expanded", open);
            // rotate caret icon
            catBtn.querySelector(".caret-icon").style.transform = open ? "rotate(180deg)" : "rotate(0deg)";
        });
    }

    // ── Close categories dropdown clicking outside ─────────────────────────
    document.addEventListener("click", (e) => {
        if (catContainer && !catContainer.contains(e.target)) {
            catContainer.classList.remove("open");
            if (catBtn) {
                catBtn.setAttribute("aria-expanded", "false");
                const caret = catBtn.querySelector(".caret-icon");
                if (caret) caret.style.transform = "rotate(0deg)";
            }
        }
    });

    // ── Desktop: User menu dropdown ────────────────────────────────────────
    const userMenuBtn = document.querySelector(".user-menu-btn");
    const userMenuContainer = document.querySelector(".user-menu-container");

    if (userMenuBtn && userMenuContainer) {
        userMenuBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            const open = userMenuContainer.classList.toggle("open");
            userMenuBtn.setAttribute("aria-expanded", open);
        });

        // Close user dropdown when clicking outside
        document.addEventListener("click", (e) => {
            if (!userMenuContainer.contains(e.target)) {
                userMenuContainer.classList.remove("open");
                userMenuBtn.setAttribute("aria-expanded", "false");
            }
        });
    }

});
