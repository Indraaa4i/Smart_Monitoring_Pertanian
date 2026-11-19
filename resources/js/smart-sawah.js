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


/* ===== Manajemen Siklus Tanam ===== */
document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-cycle-page]');

    if (!page) return;

    const search = page.querySelector('[data-cycle-search]');
    const statusFilter = page.querySelector('[data-cycle-status]');
    const seasonFilter = page.querySelector('[data-cycle-season]');
    const rows = [...page.querySelectorAll('[data-cycle-row]')];
    const emptyRow = page.querySelector('[data-cycle-empty]');

    function filterCycles() {
        const keyword = (search?.value || '').trim().toLowerCase();
        const status = statusFilter?.value || '';
        const season = seasonFilter?.value || '';
        let visible = 0;

        rows.forEach(row => {
            const matchSearch = row.dataset.search.includes(keyword);
            const matchStatus = !status || row.dataset.status === status;
            const matchSeason = !season || row.dataset.season === season;

            const show = matchSearch && matchStatus && matchSeason;

            row.hidden = !show;

            if (show) visible++;
        });

        if (emptyRow) {
            emptyRow.hidden = visible !== 0;
        }
    }

    search?.addEventListener('input', filterCycles);
    statusFilter?.addEventListener('change', filterCycles);
    seasonFilter?.addEventListener('change', filterCycles);

    // Modal detail
    const dialog = page.querySelector('[data-cycle-dialog]');

    if (!dialog) return;

    const get = selector => dialog.querySelector(selector);

    const formatDate = value => {
        if (!value) return '-';

        const [year, month, day] = value.split('-');
        const months = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        return `${Number(day)} ${months[Number(month) - 1]} ${year}`;
    };

    page.querySelectorAll('[data-cycle-detail]').forEach(button => {
        button.addEventListener('click', () => {
            get('[data-detail-title]').textContent =
                `${button.dataset.name} · ${button.dataset.block}`;

            get('[data-detail-variety]').textContent =
                button.dataset.variety;

            get('[data-detail-area]').textContent =
                `${button.dataset.area} ha`;

            get('[data-detail-period]').textContent =
                `${formatDate(button.dataset.start)} – ${formatDate(button.dataset.end)}`;

            get('[data-detail-stage]').textContent =
                button.dataset.stage;

            get('[data-detail-status]').textContent =
                button.dataset.status;

            get('[data-detail-season]').textContent =
                button.dataset.season;

            get('[data-detail-note]').textContent =
                button.dataset.note || '-';

            dialog.showModal();
        });
    });

    dialog.querySelectorAll('[data-cycle-close]').forEach(button => {
        button.addEventListener('click', () => dialog.close());
    });

    dialog.addEventListener('click', event => {
        if (event.target === dialog) {
            dialog.close();
        }
    });
});
