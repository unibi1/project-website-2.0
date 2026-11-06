<?php
require_once "auth.php";
require_once "koneksi.php";

header("Content-Type: application/json; charset=utf-8");

function kirim($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// Pastikan pengguna sudah login
if (!isset($_SESSION["user_id"])) {
    kirim(["success" => false, "message" => "Silakan login kembali."], 401);
}

$user_id = (int) $_SESSION["user_id"];
$method = $_SERVER["REQUEST_METHOD"];

$input = json_decode(file_get_contents("php://input"), true) ?? [];

try {

    // MENGAMBIL DATA MILIK PENGGUNA
    if ($method === "GET") {
        $stmt = $koneksi->prepare(
            "SELECT id, nama, jenis, tingkat, tahun, mulai,
             akhir, status, bidang, ruang, keterangan
             FROM kerjasama
             WHERE user_id = ?
             ORDER BY id DESC"
        );

        $stmt->execute([$user_id]);

        kirim([
            "success" => true,
            "data" => $stmt->fetchAll()
        ]);
    }

    // MENAMBAH DATA
    if ($method === "POST") {
        $stmt = $koneksi->prepare(
            "INSERT INTO kerjasama
            (user_id, nama, jenis, tingkat, tahun, mulai,
             akhir, status, bidang, ruang, keterangan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $user_id,
            trim($input["nama"] ?? ""),
            trim($input["jenis"] ?? ""),
            trim($input["tingkat"] ?? ""),
            (int) ($input["tahun"] ?? 0),
            $input["mulai"] ?: null,
            $input["akhir"] ?: null,
            trim($input["status"] ?? "Aktif"),
            trim($input["bidang"] ?? ""),
            trim($input["ruang"] ?? ""),
            trim($input["keterangan"] ?? "")
        ]);

        kirim([
            "success" => true,
            "message" => "Data berhasil disimpan.",
            "id" => $koneksi->lastInsertId()
        ], 201);
    }

    // MENGUBAH DATA MILIK PENGGUNA
    if ($method === "PUT") {
        $id = (int) ($input["id"] ?? 0);

        $stmt = $koneksi->prepare(
            "UPDATE kerjasama SET
                nama = ?, jenis = ?, tingkat = ?, tahun = ?,
                mulai = ?, akhir = ?, status = ?, bidang = ?,
                ruang = ?, keterangan = ?
             WHERE id = ? AND user_id = ?"
        );

        $stmt->execute([
            trim($input["nama"] ?? ""),
            trim($input["jenis"] ?? ""),
            trim($input["tingkat"] ?? ""),
            (int) ($input["tahun"] ?? 0),
            $input["mulai"] ?: null,
            $input["akhir"] ?: null,
            trim($input["status"] ?? "Aktif"),
            trim($input["bidang"] ?? ""),
            trim($input["ruang"] ?? ""),
            trim($input["keterangan"] ?? ""),
            $id,
            $user_id
        ]);

        kirim([
            "success" => true,
            "message" => "Data berhasil diperbarui."
        ]);
    }

    // MENGHAPUS DATA MILIK PENGGUNA
    if ($method === "DELETE") {
        $id = (int) ($input["id"] ?? 0);

        $stmt = $koneksi->prepare(
            "DELETE FROM kerjasama WHERE id = ? AND user_id = ?"
        );

        $stmt->execute([$id, $user_id]);

        kirim([
            "success" => true,
            "message" => "Data berhasil dihapus."
        ]);
    }

    kirim(["success" => false, "message" => "Metode tidak didukung."], 405);

} catch (PDOException $e) {
    kirim([
        "success" => false,
        "message" => "Terjadi kesalahan database."
    ], 500);
}