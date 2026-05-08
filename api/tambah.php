<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$pesan = "";
$kategori_list = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $harga = (int)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $tanggal_masuk = mysqli_real_escape_string($koneksi, $_POST['tanggal_masuk']);
    $id_kategori = !empty($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : 'NULL';
    $created_by = $_SESSION['username'];
    
    $gambar = 'default.jpg';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0 && $_FILES['gambar']['size'] > 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $gambar = time() . '_' . basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $gambar;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($imageFileType, $allowed_types)) {
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar = 'default.jpg';
                $pesan = "Gagal mengupload gambar.";
            }
        } else {
            $gambar = 'default.jpg';
            $pesan = "Format gambar tidak didukung.";
        }
    }
    
    $id_kategori_sql = ($id_kategori === 'NULL') ? 'NULL' : $id_kategori;
    $query = "INSERT INTO barang (nama_barang, harga, stok, tanggal_masuk, gambar, created_by, id_kategori) 
              VALUES ('$nama_barang', $harga, $stok, '$tanggal_masuk', '$gambar', '$created_by', $id_kategori_sql)";
    
    if (mysqli_query($koneksi, $query)) {
        catatLog($koneksi, 'Tambah Barang', "Menambah barang: $nama_barang");
        header("Location: index.php?sukses=tambah");
        exit();
    } else {
        $pesan = "Gagal menambah data: " . mysqli_error($koneksi);
    }
}

$active_page = 'tambah_barang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang | Sistem Inventori Raja Dekor</title>
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
                <h1 class="page-title">Tambah Barang</h1>
                <p class="page-subtitle">Tambahkan produk baru ke inventori</p>
            </div>
            <a href="index.php" class="btn-premium btn-ghost"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="form-card animate-in">
            <div class="form-card-header">
                <h3><i class="fas fa-plus-circle" style="color: var(--accent-violet); margin-right: 8px;"></i> Form Tambah Barang</h3>
                <p>Isi detail produk dengan lengkap</p>
            </div>
            <div class="form-card-body">
                <?php if ($pesan): ?>
                    <div class="alert-premium alert-danger-premium"><i class="fas fa-exclamation-circle"></i> <?php echo $pesan; ?></div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div style="margin-bottom: 18px;">
                        <label class="form-label-premium"><i class="fas fa-image" style="margin-right:4px;"></i> Gambar Produk</label>
                        <input type="file" name="gambar" class="form-glass" accept="image/*">
                        <div class="form-hint">Format: JPG, PNG, GIF, WEBP (Max 2MB)</div>
                    </div>
                    <div style="margin-bottom: 18px;">
                        <label class="form-label-premium">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-glass" placeholder="Masukkan nama barang" required>
                    </div>
                    <div style="margin-bottom: 18px;">
                        <label class="form-label-premium">Kategori</label>
                        <select name="id_kategori" class="form-glass">
                            <option value="">-- Pilih Kategori --</option>
                            <?php while($kat = mysqli_fetch_assoc($kategori_list)): ?>
                                <option value="<?php echo $kat['id_kategori']; ?>"><?php echo htmlspecialchars($kat['nama_kategori']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row g-3" style="margin-bottom: 18px;">
                        <div class="col-md-6">
                            <label class="form-label-premium">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-glass" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-premium">Stok</label>
                            <input type="number" name="stok" class="form-glass" placeholder="0" required>
                        </div>
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label class="form-label-premium">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-glass" required>
                    </div>
                    <div style="display:flex; gap: 12px;">
                        <button type="submit" class="btn-premium btn-success-glow"><i class="fas fa-check"></i> Simpan</button>
                        <a href="index.php" class="btn-premium btn-ghost">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>