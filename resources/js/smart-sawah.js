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

/* ===== T19: Kalender dan modal Jadwal & Aktivitas ===== */
document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-jadwal-page]');
    if (!page) return;

    const formDialog = document.getElementById('jadwal-form-dialog');
    const detailDialog = document.getElementById('jadwal-detail-dialog');

    const bukaDialog = dialog => {
        if (dialog && typeof dialog.showModal === 'function' && !dialog.open) {
            dialog.showModal();
            document.body.classList.add('modal-terbuka');
        }
    };

    const tutupDialog = dialog => {
        if (dialog && dialog.open) {
            dialog.close();
        }
    };

    page.querySelectorAll('[data-jadwal-modal-buka]').forEach(button => {
        button.addEventListener('click', () => {
            const dialog = document.getElementById(button.dataset.jadwalModalBuka);
            bukaDialog(dialog);
        });
    });

    page.querySelectorAll('[data-jadwal-dialog-tutup]').forEach(button => {
        button.addEventListener('click', () => tutupDialog(formDialog));
    });

    page.querySelectorAll('[data-jadwal-detail-tutup]').forEach(button => {
        button.addEventListener('click', () => tutupDialog(detailDialog));
    });

    [formDialog, detailDialog].forEach(dialog => {
        if (!dialog) return;

        dialog.addEventListener('click', event => {
            if (event.target === dialog) {
                tutupDialog(dialog);
            }
        });

        dialog.addEventListener('close', () => {
            if (![formDialog, detailDialog].some(item => item?.open)) {
                document.body.classList.remove('modal-terbuka');
            }
        });
    });

    const detailFields = {
        kegiatan: '[data-detail-kegiatan]',
        lahan: '[data-detail-lahan]',
        tanggal: '[data-detail-tanggal]',
        waktu: '[data-detail-waktu]',
        prioritas: '[data-detail-prioritas]',
        pengingat: '[data-detail-pengingat]',
        status: '[data-detail-status]',
        catatan: '[data-detail-catatan]',
    };

    page.querySelectorAll('[data-jadwal-detail]').forEach(button => {
        button.addEventListener('click', () => {
            Object.entries(detailFields).forEach(([key, selector]) => {
                const target = detailDialog?.querySelector(selector);
                if (target) target.textContent = button.dataset[key] || '-';
            });

            bukaDialog(detailDialog);
        });
    });

    // Jika validasi form gagal, tampilkan kembali dialog agar kesalahan mudah diperbaiki.
    if (page.dataset.jadwalBukaForm === '1') {
        bukaDialog(formDialog);
    }
});

/* ===== T20: Pratinjau dokumentasi foto Persiapan Lahan ===== */
document.addEventListener('DOMContentLoaded', () => {
    const halaman = document.querySelector('[data-persiapan-page]');
    if (!halaman) return;

    const inputFoto = halaman.querySelector('[data-persiapan-foto]');
    const wadahPreview = halaman.querySelector('[data-persiapan-preview]');

    if (!inputFoto || !wadahPreview) return;

    inputFoto.addEventListener('change', () => {
        wadahPreview.replaceChildren();

        const berkas = Array.from(inputFoto.files || []);

        berkas.slice(0, 5).forEach((file) => {
            if (!file.type.startsWith('image/')) return;

            const gambar = document.createElement('img');
            const url = URL.createObjectURL(file);

            gambar.src = url;
            gambar.alt = file.name;
            gambar.title = file.name;

            gambar.addEventListener('load', () => URL.revokeObjectURL(url), { once: true });
            wadahPreview.appendChild(gambar);
        });
    });
});
