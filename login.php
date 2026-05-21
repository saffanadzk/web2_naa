<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['level'] = $user['diver_level'];
            $_SESSION['is_premium'] = $user['is_premium'];
            
            header("Location: index.php");
            exit;
        }
    }
    $error = "Email atau Password salah!";
}

$title = "MarineLog - Login";

ob_start();
?>

<div class="auth-container">
    <div class="auth-box">
        <h2>⚓ MarineLog</h2>
        <p style="color: #ccc; font-size: 14px;">Selamat datang kembali, Penyelam!</p>
        <hr class="divider">

        <?php if($error): ?> 
            <div class="alert alert-danger"><?= $error; ?></div> 
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Masuk ke Dashboard</button>
        </form>
        <p style="margin-top:20px; font-size:13px;">Belum bergabung? <a href="register.php" style="color:#00d2c4; text-decoration:none; font-weight:bold;">Daftar disini</a></p>
    </div>
</div>

<?php
$content = ob_get_clean();

include 'layout.php';
?>