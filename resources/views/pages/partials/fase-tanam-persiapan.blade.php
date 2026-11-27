<div class="persiapan-page">
    {{-- Pemilihan lahan mengikuti query global Fase Tanam. --}}
    <div class="persiapan-toolbar">
        <form method="GET" action="{{ route('fase-tanam') }}" class="persiapan-lahan-form">
            <input type="hidden" name="tab" value="persiapan">
            <label class="sr-only" for="persiapan-lahan">Pilih lahan</label>
            <select id="persiapan-lahan" name="lahan" onchange="this.form.submit()">
                @foreach ($dataPersiapan['daftarLahan'] as $opsiLahan)
                    <option value="{{ $opsiLahan->id }}"
                        @selected($dataPersiapan['lahan']?->id === $opsiLahan->id)>
                        {{ $opsiLahan->nama_lahan }}
                        @if ($opsiLahan->luas_lahan)
                            · {{ number_format($opsiLahan->luas_lahan, 1, ',', '') }} ha
                        @endif
                    </option>
                @endforeach
            </select>
        </form>

        <span class="persiapan-status">
            {{ $dataPersiapan['status_persiapan'] }}
        </span>
    </div>

    @if (session('success'))
        <div class="persiapan-alert persiapan-alert--success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="persiapan-alert persiapan-alert--error" role="alert">
            <strong>Periksa kembali data yang dimasukkan.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Kondisi lahan terkini --}}
    <section class="persiapan-card">
        <div class="persiapan-card__heading">
            <div>
                <h2>Kondisi lahan terkini</h2>
                <p>
                    {{ $dataPersiapan['lahan']?->nama_lahan ?? 'Belum ada lahan dipilih' }}
                    ·
                    {{ $dataPersiapan['sensor']['label_waktu'] }}
                </p>
            </div>
            <span class="persiapan-source-badge {{ $dataPersiapan['sensor']['tersedia'] ? 'is-online' : 'is-offline' }}">
                {{ $dataPersiapan['sensor']['tersedia'] ? 'Sensor aktif' : 'Sensor belum tersedia' }}
            </span>
        </div>

        <div class="persiapan-sensor-grid">
            @foreach ($dataPersiapan['sensor']['parameter'] as $parameter)
                <article class="persiapan-sensor-card">
                    <span class="persiapan-sensor-card__label">{{ $parameter['judul'] }}</span>
                    <strong class="persiapan-sensor-card__value">
                        {{ $parameter['nilai'] }}
                        @if ($parameter['satuan'] !== '')
                            <small>{{ $parameter['satuan'] }}</small>
                        @endif
                    </strong>
                    <span class="persiapan-sensor-card__foot">
                        {{ $parameter['status'] }}
                    </span>
                </article>
            @endforeach
        </div>

        <p class="persiapan-help">{{ $dataPersiapan['sensor']['pesan'] }}</p>
    </section>

    {{-- Form pemeriksaan --}}
    <section class="persiapan-card" data-persiapan-page>
        <div class="persiapan-card__heading">
            <div>
                <h2>Catatan persiapan lahan</h2>
                <p>Catat hasil pemeriksaan dan pengolahan sebelum penanaman.</p>
            </div>
        </div>

        <div class="persiapan-source-switch" aria-label="Sumber data pemeriksaan">
            <button type="button" class="is-disabled" disabled
                title="Integrasi tabel data_sensor belum aktif">
                Data IoT
            </button>
            <button type="button" class="is-active" aria-pressed="true">
                Input manual
            </button>
        </div>

        @if (!$dataPersiapan['sensor']['tersedia'])
            <div class="persiapan-alert persiapan-alert--info">
                <strong>Data IoT belum dapat digunakan.</strong>
                <span>
                    Tabel atau pembacaan data_sensor belum tersedia untuk lahan ini.
                    Masukkan hasil pemeriksaan melalui input manual.
                </span>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('fase-tanam.persiapan.store', ['tab' => 'persiapan']) }}"
            enctype="multipart/form-data"
            class="persiapan-form"
        >
            @csrf

            <input type="hidden" name="sumber" value="manual">

            <div class="persiapan-field">
                <label for="persiapan-lahan-form">Lahan <span>*</span></label>
                <select id="persiapan-lahan-form" name="lahan_id" required>
                    @foreach ($dataPersiapan['daftarLahan'] as $opsiLahan)
                        <option value="{{ $opsiLahan->id }}"
                            @selected((string) old('lahan_id', $dataPersiapan['lahan']?->id) === (string) $opsiLahan->id)>
                            {{ $opsiLahan->nama_lahan }}
                            @if ($opsiLahan->luas_lahan)
                                · {{ number_format($opsiLahan->luas_lahan, 1, ',', '') }} ha
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('lahan_id') <small class="persiapan-error">{{ $message }}</small> @enderror
            </div>

            <div class="persiapan-field">
                <label for="persiapan-tanggal">Tanggal pemeriksaan <span>*</span></label>
                <input
                    id="persiapan-tanggal"
                    type="date"
                    name="tanggal_pemeriksaan"
                    value="{{ old('tanggal_pemeriksaan', $dataPersiapan['tanggal_default']) }}"
                    max="{{ $dataPersiapan['tanggal_default'] }}"
                    required
                >
                @error('tanggal_pemeriksaan') <small class="persiapan-error">{{ $message }}</small> @enderror
            </div>

            <div class="persiapan-field">
                <label for="persiapan-kondisi">Kondisi tanah <span>*</span></label>
                <select id="persiapan-kondisi" name="kondisi_tanah" required>
                    @foreach ($dataPersiapan['opsi']['kondisi'] as $opsi)
                        <option value="{{ $opsi }}" @selected(old('kondisi_tanah', 'Lembap, gembur') === $opsi)>
                            {{ $opsi }}
                        </option>
                    @endforeach
                </select>
                @error('kondisi_tanah') <small class="persiapan-error">{{ $message }}</small> @enderror
            </div>

            <div class="persiapan-field">
                <label for="persiapan-metode">Metode pengolahan <span>*</span></label>
                <select id="persiapan-metode" name="metode_pengolahan" required>
                    @foreach ($dataPersiapan['opsi']['metode'] as $opsi)
                        <option value="{{ $opsi }}" @selected(old('metode_pengolahan', 'Bajak traktor + pelumpuran') === $opsi)>
                            {{ $opsi }}
                        </option>
                    @endforeach
                </select>
                @error('metode_pengolahan') <small class="persiapan-error">{{ $message }}</small> @enderror
            </div>

            <div class="persiapan-field">
                <label for="persiapan-ph">pH tanah <span>*</span></label>
                <input
                    id="persiapan-ph"
                    type="number"
                    name="ph"
                    min="0"
                    max="14"
                    step="0.1"
                    value="{{ old('ph') }}"
                    placeholder="Contoh: 6,4"
                    required
                >
                <small>Rentang nilai 0–14</small>
                @error('ph') <small class="persiapan-error">{{ $message }}</small> @enderror
            </div>

            <div class="persiapan-field">
                <label for="persiapan-kelembapan">Kelembapan tanah (%) <span>*</span></label>
                <input
                    id="persiapan-kelembapan"
                    type="number"
                    name="kelembapan"
                    min="0"
                    max="100"
                    step="0.1"
                    value="{{ old('kelembapan') }}"
                    placeholder="Contoh: 68"
                    required
                >
                <small>Rentang nilai 0–100%</small>
                @error('kelembapan') <small class="persiapan-error">{{ $message }}</small> @enderror
            </div>

            <div class="persiapan-field">
                <label for="persiapan-air">Ketinggian air (cm) <span>*</span></label>
                <input
                    id="persiapan-air"
                    type="number"
                    name="ketinggian_air"
                    min="0"
                    max="1000"
                    step="0.1"
                    value="{{ old('ketinggian_air') }}"
                    placeholder="Contoh: 3"
                    required
                >
                <small>Diukur dari permukaan tanah</small>
                @error('ketinggian_air') <small class="persiapan-error">{{ $message }}</small> @enderror
            </div>

            <div class="persiapan-field">
                <label for="persiapan-petugas">Penanggung jawab</label>
                <input
                    id="persiapan-petugas"
                    type="text"
                    name="penanggung_jawab"
                    maxlength="100"
                    value="{{ old('penanggung_jawab') }}"
                    placeholder="Nama penanggung jawab"
                >
                @error('penanggung_jawab') <small class="persiapan-error">{{ $message }}</small> @enderror
            </div>

            <div class="persiapan-field persiapan-field--full">
                <label for="persiapan-catatan">Catatan</label>
                <textarea
                    id="persiapan-catatan"
                    name="catatan"
                    rows="3"
                    maxlength="1000"
                    placeholder="Tambahkan catatan pemeriksaan..."
                >{{ old('catatan') }}</textarea>
                @error('catatan') <small class="persiapan-error">{{ $message }}</small> @enderror
            </div>

            <div class="persiapan-photo persiapan-field--full">
                <div>
                    <strong>Dokumentasi foto</strong>
                    <p>JPG / PNG · Maks. 5 MB per foto · Maks. 5 foto</p>
                </div>

                <label class="persiapan-photo__button" for="persiapan-foto">
                    + Pilih foto
                </label>
                <input
                    id="persiapan-foto"
                    type="file"
                    name="foto[]"
                    accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                    multiple
                    data-persiapan-foto
                >
                <div class="persiapan-photo__preview" data-persiapan-preview></div>
                @error('foto') <small class="persiapan-error">{{ $message }}</small> @enderror
                @error('foto.*') <small class="persiapan-error">{{ $message }}</small> @enderror
                <small class="persiapan-photo__note">Foto bersifat opsional.</small>
            </div>

            <div class="persiapan-form__actions persiapan-field--full">
                <button type="submit" class="btn-fase btn-fase--utama">Simpan catatan</button>
                <a href="{{ route('fase-tanam', ['tab' => 'persiapan', 'lahan' => $dataPersiapan['lahan']?->id]) }}"
                    class="btn-fase">Batal</a>
            </div>
        </form>
    </section>

    {{-- Riwayat pemeriksaan --}}
    <section class="persiapan-card">
        <div class="persiapan-card__heading">
            <div>
                <h2>Riwayat persiapan lahan</h2>
                <p>Catatan pemeriksaan yang tersimpan untuk lahan ini.</p>
            </div>
        </div>

        <div class="persiapan-table-wrap">
            <table class="persiapan-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Sumber</th>
                        <th>Hasil</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataPersiapan['riwayat'] as $catatan)
                        <tr>
                            <td><strong>{{ $catatan['tanggal_label'] }}</strong></td>
                            <td>
                                {{ $catatan['kegiatan'] }}
                                @if (!empty($catatan['catatan']))
                                    <small class="persiapan-table__note">{{ $catatan['catatan'] }}</small>
                                @endif
                                @if (!empty($catatan['foto_url']))
                                    <div class="persiapan-photo-links">
                                        @foreach ($catatan['foto_url'] as $fotoUrl)
                                            <a href="{{ $fotoUrl }}" target="_blank" rel="noopener">Lihat foto</a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>{{ $catatan['sumber'] }}</td>
                            <td>{{ $catatan['hasil'] }}</td>
                            <td>{{ $catatan['petugas'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="persiapan-table__empty">
                                Belum ada catatan persiapan lahan untuk lahan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($dataPersiapan['jumlah_catatan'] > 0)
            <p class="persiapan-table__footnote">
                {{ $dataPersiapan['jumlah_catatan'] }} catatan ditampilkan.
                Data contoh ditandai pada halaman.
            </p>
        @endif
    </section>
</div>
