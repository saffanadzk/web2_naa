<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $diver_level = $_POST['diver_level'];

    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password, diver_level) VALUES ('$username', '$email', '$password_hash', '$diver_level')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Registrasi berhasil! Silakan login.";
        } else {
            $error = "Terjadi kesalahan pendaftaran.";
        }
    }
}

$title = "MarineLog - Daftar Akun";

ob_start();
?>

<div class="auth-container">
    <div class="auth-box">
        <h2>⚓ MarineLog</h2>
        <p style="color: #ccc; font-size: 14px;">Daftar akun logbook menyelam digital</p>
        <hr class="divider">

        <?php if($error): ?> <div class="alert alert-danger"><?= $error; ?></div> <?php endif; ?>
        <?php if($success): ?> <div class="alert alert-success"><?= $success; ?></div> <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" minlength="6" required>
            </div>
            <div class="form-group">
                <label>Tingkat Sertifikasi Selam</label>
                <select name="diver_level" required>
                    <option value="Pemula">Belum Sertifikasi (Pemula)</option>
                    <option value="Open Water">Open Water Diver</option>
                    <option value="Advanced">Advanced Open Water</option>
                    <option value="Rescue">Rescue Diver</option>
                </select>
            </div>
            <button type="submit">Mulai Petualangan</button>
        </form>
        <p style="margin-top:20px; font-size:13px;">Sudah punya akun? <a href="login.php" style="color:#00d2c4; text-decoration:none; font-weight:bold;">Login disini</a></p>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>