<?php
session_start();
include 'koneksi.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5(mysqli_real_escape_string($koneksi, $_POST['password']));
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $query = "INSERT INTO users (username, password, nama_lengkap, email, level) 
                  VALUES ('$username', '$password', '$nama_lengkap', '$email', 'staff')";
        
        if (mysqli_query($koneksi, $query)) {
            $success = "Pendaftaran berhasil! Silakan login.";
        } else {
            $error = "Pendaftaran gagal: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi | Sistem Inventori Raja Dekor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card" style="max-width: 460px;">
            <div class="auth-header">
                <div class="auth-logo"><i class="fas fa-user-plus"></i></div>
                <h2>Daftar Akun</h2>
                <p>Sistem Inventori — Raja Dekor</p>
            </div>
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert-premium alert-danger-premium">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert-premium alert-success-premium">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" autocomplete="off">
                    <div style="margin-bottom: 16px;">
                        <label class="form-label-premium">Username</label>
                        <input type="text" name="username" class="form-glass" placeholder="Masukkan username" required>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label class="form-label-premium">Password</label>
                        <input type="password" name="password" class="form-glass" placeholder="Masukkan password" required>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label class="form-label-premium">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-glass" placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div style="margin-bottom: 22px;">
                        <label class="form-label-premium">Email</label>
                        <input type="email" name="email" class="form-glass" placeholder="contoh@email.com">
                    </div>
                    <button type="submit" class="btn-premium btn-primary-glow" style="width:100%; padding: 12px; font-size: 14px;">
                        <i class="fas fa-user-check"></i> Daftar
                    </button>
                </form>
                <div style="text-align:center; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-light);">
                    <a href="login.php" class="auth-link"><i class="fas fa-arrow-left" style="margin-right:4px;"></i> Kembali ke Login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>