const API_URL = "api_kerjasama.php";
let dataKerjaSama = [];
const $ = id => document.getElementById(id);

// Fungsi Badge Warna
function getBadgeClass(status = "") {
    const s = String(status).toLowerCase();
    if (/aktif/i.test(s)) return "status-aktif";
    if (/proses/i.test(s)) return "status-proses";
    if (/berakhir/i.test(s)) return "status-berakhir";
    return "status-berakhir";
}

// Fungsi Loading
function setLoading(show) {
    if ($("loading")) {
        $("loading").classList.toggle("hidden", !show);
    }
}

// KOMUNIKASI DENGAN DATABASE
async function requestAPI(method, data = null) {
    const options = {
        method: method,
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        }
    };
    if (data !== null) {
        options.body = JSON.stringify(data);
    }
    const response = await fetch(API_URL, options);
    const hasil = await response.json();
    if (!response.ok || !hasil.success) {
        throw new Error(hasil.message || "Gagal mengakses database.");
    }
    return hasil;
}

// MENGAMBIL DATA DARI MYSQL
async function muatData() {
    setLoading(true);
    try {
        const hasil = await requestAPI("GET");
        dataKerjaSama = hasil.data || [];
        renderSemua();
    } catch (error) {
        alert(error.message);
        console.error(error);
    } finally {
        setLoading(false);
    }
}

function pindahHalaman(nama, judul) {
    document.querySelectorAll(".page").forEach(p => {
        p.classList.add("hidden");
    });
    $(nama).classList.remove("hidden");
    document.querySelectorAll(".menu").forEach(m => {
        m.classList.remove("active");
    });
    document.querySelector(`[data-page="${nama}"]`)
        .classList.add("active");
    $("page-title").textContent = judul;
    renderSemua();
}

document.querySelectorAll(".menu").forEach(menu => {
    menu.addEventListener("click", function(e) {
        e.preventDefault();
        const nama = this.dataset.page;
        const judul = this.textContent.trim().replace(/^[^\w]+/, "");
        pindahHalaman(nama, judul);
    });
});

// FORM TAMBAH DAN EDIT
function bukaForm(item = null) {
    $("cooperation-form").reset();
    $("edit-id").value = item ? item.id : "";
    $("form-title").textContent = item
        ? "Edit Kerja Sama"
        : "Tambah Kerja Sama";
    if (item) {
        $("nama").value = item.nama;
        $("jenis").value = item.jenis;
        $("tingkat").value = item.tingkat;
        $("tahun").value = item.tahun;
        $("mulai").value = item.mulai || "";
        $("akhir").value = item.akhir || "";
        $("status").value = item.status || "";
        $("bidang").value = item.bidang || "";
        $("ruang").value = item.ruang || "";
        $("keterangan").value = item.keterangan || "";
    }
    $("modal").classList.remove("hidden");
}

function tutupForm() {
    $("modal").classList.add("hidden");
}

$("open-form").addEventListener("click", () => bukaForm());
$("close-form").addEventListener("click", tutupForm);
$("cancel-form").addEventListener("click", tutupForm);

// MENYIMPAN DAN MEMPERBARUI DATA
$("cooperation-form").addEventListener("submit", async function(e) {
    e.preventDefault();
    setLoading(true);
    const id = $("edit-id").value;
    const item = {
        nama: $("nama").value.trim(),
        jenis: $("jenis").value.trim(),
        tingkat: $("tingkat").value.trim(),
        tahun: $("tahun").value,
        mulai: $("mulai").value,
        akhir: $("akhir").value,
        status: $("status").value.trim() || "Aktif",
        bidang: $("bidang").value.trim(),
        ruang: $("ruang").value.trim(),
        keterangan: $("keterangan").value.trim()
    };
    try {
        if (id) {
            item.id = Number(id);
            await requestAPI("PUT", item);
            alert("Data berhasil diperbarui.");
        } else {
            await requestAPI("POST", item);
            alert("Data berhasil disimpan.");
        }
        tutupForm();
        await muatData();
        pindahHalaman("kerjasama", "Data Kerja Sama");
    } catch (error) {
        alert(error.message);
        console.error(error);
    } finally {
        setLoading(false);
    }
});

// MENAMPILKAN TABEL DENGAN BADGE
function renderTabel() {
    const keyword = $("search").value.toLowerCase();
    const hasil = dataKerjaSama.filter(d =>
        [
            d.nama,
            d.jenis,
            d.tingkat,
            d.tahun,
            d.bidang
        ].join(" ").toLowerCase().includes(keyword)
    );
    $("data-table").innerHTML = hasil.length
        ? hasil.map((d, i) => `
            <tr>
                <td>${i + 1}</td>
                <td><b>${aman(d.nama)}</b></td>
                <td>${aman(d.jenis)}</td>
                <td>${aman(d.tingkat)}</td>
                <td>${aman(d.tahun)}</td>
                <td><span class="status-badge ${getBadgeClass(d.status)}">${aman(d.status)}</span></td>
                <td>
                    <button class="action edit"
                        onclick="editData(${Number(d.id)})">
                        Edit
                    </button>
                    <button class="action delete"
                        onclick="hapusData(${Number(d.id)})">
                        Hapus
                    </button>
                </td>
            </tr>
        `).join("")
        : `<tr>
            <td colspan="7" class="empty">
                Data tidak ditemukan.
            </td>
        </tr>`;
}

// PENGAMAN TAMPILAN TEKS
function aman(teks) {
    return String(teks ?? "").replace(/[&<>"']/g, karakter => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;"
    })[karakter]);
}

// EDIT DATA
function editData(id) {
    const item = dataKerjaSama.find(d => Number(d.id) === Number(id));
    if (item) {
        bukaForm(item);
    }
}

// HAPUS DATA
async function hapusData(id) {
    const item = dataKerjaSama.find(d => Number(d.id) === Number(id));
    if (!item) return;
    if (confirm(`Yakin ingin menghapus data kerja sama dengan ${item.nama}?`)) {
        setLoading(true);
        try {
            await requestAPI("DELETE", {
                id: Number(id)
            });
            alert("Data berhasil dihapus.");
            await muatData();
        } catch (error) {
            alert(error.message);
            console.error(error);
        } finally {
            setLoading(false);
        }
    }
}

// MEMPERBARUI SELURUH RINGKASAN DASHBOARD
function renderSemua() {
    $("total-kerjasama").textContent = dataKerjaSama.length;
    $("total-mitra").textContent =
        new Set(dataKerjaSama.map(d =>
            d.nama.toLowerCase()
        )).size;
    $("total-dokumen").textContent = dataKerjaSama.length;
    $("total-evaluasi").textContent =
        dataKerjaSama.filter(d =>
            /berakhir|evaluasi|proses/i.test(d.status)
        ).length;

    renderTabel();

    const terbaru = [...dataKerjaSama].slice(-5).reverse();
    $("dashboard-recent").innerHTML = terbaru.length
        ? terbaru.map(d =>
            `<p class="recent-row">
                <b>${aman(d.nama)}</b><br>
                ${aman(d.jenis)} • ${aman(d.tahun)}
                <span class="status-badge ${getBadgeClass(d.status)}">${aman(d.status || "-")}</span>
            </p>`
        ).join("")
        : `<p class="empty">Belum ada data kerja sama.</p>`;

    const dashMonitor = [...dataKerjaSama].slice(-5);
    $("monitoring-list-dash").innerHTML = dashMonitor.length
        ? dashMonitor.map(d =>
            `<p class="recent-row">
                <b>${aman(d.nama)}</b><br>
                Berakhir: ${aman(d.akhir || "Belum diisi")}
                <span class="status-badge ${getBadgeClass(d.status)}">${aman(d.status || "-")}</span>
            </p>`
        ).join("")
        : `<p class="empty">Belum ada data untuk dimonitoring.</p>`;

    const mitraUnik = [
        ...new Map(
            dataKerjaSama.map(d => [
                d.nama.toLowerCase(),
                d
            ])
        ).values()
    ];
    $("mitra-list").innerHTML = mitraUnik.length
        ? mitraUnik.map(d =>
            `<p class="recent-row">
                ${aman(d.nama)} — ${aman(d.tingkat)}
            </p>`
        ).join("")
        : `<p class="empty">Belum ada data mitra.</p>`;

    $("dokumen-list").innerHTML = dataKerjaSama.length
        ? dataKerjaSama.map(d =>
            `<p class="recent-row">
                ${aman(d.nama)} — ${aman(d.jenis)} — ${aman(d.tahun)}
            </p>`
        ).join("")
        : `<p class="empty">Belum ada dokumen.</p>`;

    $("monitoring-list").innerHTML = dataKerjaSama.length
        ? dataKerjaSama.map(d =>
            `<p class="recent-row">
                ${aman(d.nama)} —
                Status: <span class="status-badge ${getBadgeClass(d.status)}">${aman(d.status)}</span>
                — Berakhir: ${aman(d.akhir || "Belum diisi")}
            </p>`
        ).join("")
        : `<p class="empty">Belum ada data untuk dimonitoring.</p>`;
}

$("search").addEventListener("input", renderTabel);

// EKSPOR CSV
function eksporCSV() {
    if (!dataKerjaSama.length) {
        alert("Belum ada data untuk diekspor.");
        return;
    }
    const kolom = [
        "Nama Mitra",
        "Jenis Dokumen",
        "Tingkat",
        "Tahun",
        "Tanggal Mulai",
        "Tanggal Berakhir",
        "Status",
        "Bidang",
        "Ruang Lingkup",
        "Keterangan"
    ];
    const baris = dataKerjaSama.map(d => [
        d.nama,
        d.jenis,
        d.tingkat,
        d.tahun,
        d.mulai,
        d.akhir,
        d.status,
        d.bidang,
        d.ruang,
        d.keterangan
    ]);
    const csv = [kolom, ...baris].map(row =>
        row.map(value =>
            `"${String(value ?? "").replace(/"/g, '""')}"`
        ).join(";")
    ).join("\n");
    const blob = new Blob(
        ["\uFEFF" + csv],
        { type: "text/csv;charset=utf-8;" }
    );
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "Rekap_Data_Kerja_Sama_UNIBI.csv";
    link.click();
    URL.revokeObjectURL(link.href);
}

$("export-csv").addEventListener("click", eksporCSV);
$("export-csv-2").addEventListener("click", eksporCSV);

// PERTAMA KALI MEMBUKA DASHBOARD
muatData();