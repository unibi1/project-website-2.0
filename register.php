<?php
require_once "koneksi.php";
require_once "auth.php";
if (userLogin()) {
    header("Location: index.php");
    exit;
}
$error = "";
$berhasil = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $konfirmasi = $_POST["konfirmasi"] ?? "";
    if ($nama === "" || $email === "" || $password === "") {
        $error = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";
    } elseif ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        $cek = $koneksi->prepare(
            "SELECT id FROM users WHERE email = ?"
        );
        $cek->execute([$email]);
        if ($cek->fetch()) {
            $error = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $simpan = $koneksi->prepare(
                "INSERT INTO users (nama, email, password)
                 VALUES (?, ?, ?)"
            );
            $simpan->execute([$nama, $email, $hash]);
            $berhasil = "Registrasi berhasil! Silakan login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi SISKERMA UNIBI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <h2>Registrasi Akun SISKERMA</h2>
            <p>Buat akun untuk mengakses sistem</p>
        </div>
        <?php if ($error): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($berhasil): ?>
            <div style="padding:12px;background:#e8f8f2;color:#107048;border-radius:7px;margin-bottom:15px;">
                <?= htmlspecialchars($berhasil) ?>
                <a href="login.php">Login sekarang</a>
            </div>
        <?php endif; ?>
        <form method="POST" class="auth-form">
            <label>Nama Lengkap
                <input type="text" name="nama" required>
            </label>
            <label>Email
                <input type="email" name="email" required>
            </label>
            <label>Password (minimal 8 karakter)
                <input type="password" name="password" required>
            </label>
            <label>Konfirmasi Password
                <input type="password" name="konfirmasi" required>
            </label>
            <button type="submit" class="auth-btn">Daftar Akun</button>
        </form>
        <div class="auth-footer">
            Sudah punya akun? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>