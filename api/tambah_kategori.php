<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$pesan = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    
    $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
    
    if (mysqli_query($koneksi, $query)) {
        catatLog($koneksi, 'Tambah Kategori', "Menambah kategori: $nama_kategori");
        header("Location: kategori.php?sukses=tambah");
        exit();
    } else {
        $pesan = "Gagal menambah kategori: " . mysqli_error($koneksi);
    }
}

$active_page = 'kategori';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori | Sistem Inventori Raja Dekor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-wrapper">
        <div class="page-header">
            <div>
                <h1 class="page-title">Tambah Kategori</h1>
                <p class="page-subtitle">Tambahkan kategori produk baru</p>
            </div>
            <a href="kategori.php" class="btn-premium btn-ghost"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="form-card animate-in" style="max-width: 480px;">
            <div class="form-card-header">
                <h3><i class="fas fa-tags" style="color: var(--accent-violet); margin-right: 8px;"></i> Kategori Baru</h3>
            </div>
            <div class="form-card-body">
                <?php if ($pesan): ?>
                    <div class="alert-premium alert-danger-premium"><i class="fas fa-exclamation-circle"></i> <?php echo $pesan; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div style="margin-bottom: 22px;">
                        <label class="form-label-premium">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-glass" placeholder="Masukkan nama kategori" required autofocus>
                    </div>
                    <div style="display:flex; gap: 12px;">
                        <button type="submit" class="btn-premium btn-success-glow"><i class="fas fa-check"></i> Simpan</button>
                        <a href="kategori.php" class="btn-premium btn-ghost">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>