// Buka/tutup menu navigasi pada tablet & mobile.
document.querySelectorAll("[data-sidebar-toggle]").forEach((btn) => {
    btn.addEventListener("click", () => {
        const sidebar = btn.closest("[data-sidebar]");
        const open = sidebar.classList.toggle("is-open");
        btn.setAttribute("aria-expanded", open ? "true" : "false");
    });
});

// ----- Modal berbasis <dialog> -----
// Tombol [data-modal-buka="id-dialog"] membuka modal.
// Menutup: tombol Tutup (<form method="dialog">), tombol Esc, atau klik di latar gelap.
document.querySelectorAll('[data-modal-buka]').forEach((tombol) => {
    const dialog = document.getElementById(tombol.dataset.modalBuka);
    if (!dialog || typeof dialog.showModal !== 'function') {
        return;
    }

    tombol.addEventListener('click', () => {
        dialog.showModal();
        document.body.classList.add('modal-terbuka');
    });
});

document.querySelectorAll('dialog.modal-siklus').forEach((dialog) => {
    // Klik pada latar (::backdrop) menjadikan dialog sebagai target klik.
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });

    // Terpanggil untuk semua cara menutup; mengembalikan scroll halaman.
    dialog.addEventListener('close', () => {
        document.body.classList.remove('modal-terbuka');
    });
});