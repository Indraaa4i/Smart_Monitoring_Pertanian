// Buka/tutup menu navigasi pada tablet & mobile.
document.querySelectorAll("[data-sidebar-toggle]").forEach((btn) => {
    btn.addEventListener("click", () => {
        const sidebar = btn.closest("[data-sidebar]");
        const open = sidebar.classList.toggle("is-open");
        btn.setAttribute("aria-expanded", open ? "true" : "false");
    });
});
