
<?php
require_once "auth.php";

if (!userLogin()) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISKERMA UNIBI</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- LOADING OVERLAY -->
<div id="loading" class="loading-overlay hidden">
    <div class="spinner"></div>
    <p>Memproses...</p>
</div>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <img src="assets/logo-unibi.png" alt="Logo UNIBI">
            <div>
                <h2>SISKERMA</h2>
                <small>UNIBI</small>
            </div>
        </div>

        <p class="menu-label">MENU UTAMA</p>

        <nav>
            <a href="#" class="menu active" data-page="dashboard">
                ▦ Dashboard
            </a>

            <a href="#" class="menu" data-page="kerjasama">
                🤝 Data Kerja Sama
            </a>

            <a href="#" class="menu" data-page="mitra">
                ▦ Data Mitra
            </a>

            <a href="#" class="menu" data-page="dokumen">
                ▤ Dokumen Kerja Sama
            </a>

            <a href="#" class="menu" data-page="monitoring">
                ▥ Monitoring & Evaluasi
            </a>

            <a href="#" class="menu" data-page="laporan">
                ▣ Laporan
            </a>
        </nav>

        <div class="sidebar-footer">
            UNIVERSITAS INFORMATIKA DAN BISNIS INDONESIA
        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="topbar">

            <div>
                <h2 id="page-title">Dashboard</h2>
                <p>Sistem Informasi Kerja Sama UNIBI</p>
            </div>

            <div class="user-info">

                <div class="avatar">
                    <?= strtoupper(substr($_SESSION['nama'] ?? 'P', 0, 1)) ?>
                </div>

                <div class="user-name">
                    <span>
                        <?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <small>Pengguna SISKERMA</small>
                </div>

                <a href="logout.php" class="logout-btn">
                    Logout
                </a>

            </div>

        </header>


        <!-- DASHBOARD -->
        <section id="dashboard" class="page">

            <!-- HERO -->
            <div class="hero">

                <div>
                    <small>SISTEM INFORMASI KERJA SAMA UNIBI</small>

                    <h1>
                        Kelola Kerja Sama Lebih Mudah & Terintegrasi
                    </h1>

                    <p>
                        Pantau kemitraan, dokumen serta pelaksanaan kerja sama
                        UNIBI dalam satu sistem yang teratur.
                    </p>
                </div>

                <div class="hero-icon">🤝</div>

            </div>


            <!-- RINGKASAN DATA -->
            <div class="dashboard-section">

                <div class="section-heading">
                    <h3>Ringkasan Data</h3>
                    <p class="muted">
                        Statistik keseluruhan kerja sama
                    </p>
                </div>

                <div class="stats">

                    <div class="stat">
                        <span>🤝</span>
                        <p>Total Kerja Sama</p>
                        <strong id="total-kerjasama">0</strong>
                    </div>

                    <div class="stat">
                        <span>🏢</span>
                        <p>Total Mitra</p>
                        <strong id="total-mitra">0</strong>
                    </div>

                    <div class="stat">
                        <span>📄</span>
                        <p>Dokumen</p>
                        <strong id="total-dokumen">0</strong>
                    </div>

                    <div class="stat">
                        <span>⌛</span>
                        <p>Perlu Evaluasi</p>
                        <strong id="total-evaluasi">0</strong>
                    </div>

                </div>

            </div>


            <!-- AKTIVITAS DAN MONITORING -->
            <div class="dashboard-grid">

                <div class="panel">
                    <h3>Aktivitas Terbaru</h3>
                    <p class="muted">
                        Data kerja sama yang baru dimasukkan
                    </p>

                    <div id="dashboard-recent" class="empty">
                        Belum ada data kerja sama.
                    </div>
                </div>


                <div class="panel">
                    <h3>Monitoring Cepat</h3>
                    <p class="muted">
                        Status pelaksanaan kerja sama
                    </p>

                    <div id="monitoring-list-dash" class="empty">
                        Belum ada data untuk dimonitoring.
                    </div>
                </div>

            </div>

        </section>


        <!-- DATA KERJA SAMA -->
        <section id="kerjasama" class="page hidden">

            <div class="page-heading">

                <div>
                    <h2>Data Kerja Sama</h2>
                    <p class="muted">
                        Kelola seluruh informasi kerja sama UNIBI.
                    </p>
                </div>

                <button class="btn" id="open-form">
                    + Tambah Kerja Sama
                </button>

            </div>


            <div class="panel">

                <div class="toolbar">

                    <input
                        id="search"
                        type="search"
                        placeholder="Cari nama mitra, jenis dokumen, atau tahun..."
                    >

                    <button class="btn light" id="export-csv">
                        Ekspor CSV
                    </button>

                </div>


                <div class="table-wrap">

                    <table>

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Mitra</th>
                                <th>Jenis Dokumen</th>
                                <th>Tingkat</th>
                                <th>Tahun</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="data-table">

                            <tr>
                                <td colspan="7" class="empty">
                                    Belum ada data. Klik Tambah Kerja Sama.
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>


        <!-- DATA MITRA -->
        <section id="mitra" class="page hidden">

            <div class="panel">

                <h2>Data Mitra</h2>

                <p class="muted">
                    Daftar mitra akan terbentuk dari data kerja sama yang dimasukkan.
                </p>

                <div id="mitra-list" class="empty">
                    Belum ada data mitra.
                </div>

            </div>

        </section>


        <!-- DOKUMEN KERJA SAMA -->
        <section id="dokumen" class="page hidden">

            <div class="panel">

                <h2>Dokumen Kerja Sama</h2>

                <p class="muted">
                    Informasi dokumen kerja sama yang telah dicatat.
                </p>

                <div id="dokumen-list" class="empty">
                    Belum ada dokumen.
                </div>

            </div>

        </section>


        <!-- MONITORING -->
        <section id="monitoring" class="page hidden">

            <div class="panel">

                <h2>Monitoring & Evaluasi</h2>

                <p class="muted">
                    Pantau status pelaksanaan kerja sama.
                </p>

                <div id="monitoring-list" class="empty">
                    Belum ada data untuk dimonitoring.
                </div>

            </div>

        </section>


        <!-- LAPORAN -->
        <section id="laporan" class="page hidden">

            <div class="panel">

                <h2>Laporan Kerja Sama</h2>

                <p class="muted">
                    Unduh rekap data kerja sama dalam format CSV
                    melalui menu Data Kerja Sama.
                </p>

                <button class="btn" id="export-csv-2">
                    Unduh Rekap CSV
                </button>

            </div>

        </section>

    </main>

</div>


<!-- FORM TAMBAH / EDIT -->
<div class="modal hidden" id="modal">

    <div class="modal-box">

        <div class="modal-head">

            <div>
                <h2 id="form-title">Tambah Kerja Sama</h2>
                <p class="muted">
                    Isi informasi kerja sama berikut.
                </p>
            </div>

            <button class="close" id="close-form">×</button>

        </div>


        <form id="cooperation-form">

            <input type="hidden" id="edit-id">

            <div class="form-grid">

                <label>
                    Nama Mitra / Instansi *
                    <input
                        id="nama"
                        required
                        placeholder="Contoh: Universiti Malaysia Kelantan"
                    >
                </label>


                <label>
                    Jenis Dokumen *
                    <input
                        id="jenis"
                        required
                        placeholder="Contoh: MoU, MoA, IA"
                    >
                </label>


                <label>
                    Tingkat Kerja Sama *
                    <input
                        id="tingkat"
                        required
                        placeholder="Lokal / Nasional / Internasional"
                    >
                </label>


                <label>
                    Tahun Kerja Sama *
                    <input
                        id="tahun"
                        required
                        type="number"
                        min="2000"
                        max="2100"
                        placeholder="2026"
                    >
                </label>


                <label>
                    Tanggal Mulai
                    <input id="mulai" type="date">
                </label>


                <label>
                    Tanggal Berakhir
                    <input id="akhir" type="date">
                </label>


                <label>
                    Status Kerja Sama
                    <input
                        id="status"
                        placeholder="Aktif / Proses / Berakhir"
                    >
                </label>


                <label>
                    Bidang Kerja Sama
                    <input
                        id="bidang"
                        placeholder="Pendidikan / Penelitian / PkM"
                    >
                </label>


                <label class="full">
                    Ruang Lingkup / Uraian Kerja Sama
                    <textarea
                        id="ruang"
                        rows="3"
                        placeholder="Tuliskan ruang lingkup kerja sama..."
                    ></textarea>
                </label>


                <label class="full">
                    Keterangan Tambahan
                    <textarea
                        id="keterangan"
                        rows="2"
                        placeholder="Catatan tambahan (opsional)..."
                    ></textarea>
                </label>

            </div>


            <div class="form-actions">

                <button
                    type="button"
                    class="btn light"
                    id="cancel-form"
                >
                    Batal
                </button>

                <button type="submit" class="btn">
                    Simpan Data
                </button>

            </div>

        </form>

    </div>

</div>


<script src="script.js"></script>

</body>
</html>
