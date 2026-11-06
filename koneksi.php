<?php

$host = "localhost";
$db   = "siskerma_unibi";
$user = "root";
$pass = "";

try {
    $koneksi = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

} catch (PDOException $e) {
    die("Koneksi database gagal. Periksa konfigurasi database.");
}

?>