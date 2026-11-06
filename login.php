<?php
require_once "koneksi.php";
require_once "auth.php";
if (userLogin()) {
    header("Location: index.php");
    exit;
}
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $stmt = $koneksi->prepare(
        "SELECT id, nama, email, password FROM users WHERE email = ?"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["nama"] = $user["nama"];
        $_SESSION["email"] = $user["email"];
        header("Location: index.php");
        exit;
    } else {
        $error = "Email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SISKERMA UNIBI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
    <div class="auth-header">
        <h2>SISKERMA UNIBI</h2>
        <p>Silakan login ke akun Anda</p>
    </div>
    <?php if ($error): ?>
        <div class="auth-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <form method="POST" class="auth-form">
        <label>Email
            <input type="email" name="email" required>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="auth-btn">Login</button>
    </form>
    <div class="auth-footer">
        Belum punya akun? <a href="register.php">Daftar Akun</a>
    </div>
</div>
</body>
</html>