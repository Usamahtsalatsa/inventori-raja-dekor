<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$id = (int)$_GET['id'];
$pesan = "";

$query = "SELECT * FROM barang WHERE id_barang = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: index.php");
    exit();
}

$kategori_list = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $harga = (int)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $tanggal_masuk = mysqli_real_escape_string($koneksi, $_POST['tanggal_masuk']);
    $id_kategori = !empty($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : 'NULL';
    
    $gambar = $data['gambar'] ?? 'default.jpg';
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0 && $_FILES['gambar']['size'] > 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $gambar_baru = time() . '_' . basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $gambar_baru;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($imageFileType, $allowed_types)) {
            if ($gambar != 'default.jpg' && file_exists($target_dir . $gambar)) {
                unlink($target_dir . $gambar);
            }
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar = $gambar_baru;
            } else {
                $pesan = "Gagal mengupload gambar.";
            }
        } else {
            $pesan = "Format gambar tidak didukung.";
        }
    }
    
    $id_kategori_sql = ($id_kategori === 'NULL') ? 'NULL' : $id_kategori;
    $query_update = "UPDATE barang SET 
                     nama_barang = '$nama_barang',
                     harga = $harga,
                     stok = $stok,
                     tanggal_masuk = '$tanggal_masuk',
                     gambar = '$gambar',
                     id_kategori = $id_kategori_sql
                     WHERE id_barang = $id";
    
    if (mysqli_query($koneksi, $query_update)) {
        catatLog($koneksi, 'Edit Barang', "Mengedit barang: $nama_barang (ID: $id)");
        header("Location: index.php?sukses=edit");
        exit();
    } else {
        $pesan = "Gagal update: " . mysqli_error($koneksi);
    }
}

$active_page = 'data_barang';
$gambar_path = 'uploads/default.jpg';
if (!empty($data['gambar']) && $data['gambar'] != 'default.jpg' && file_exists("uploads/" . $data['gambar'])) {
    $gambar_path = 'uploads/' . $data['gambar'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang | Sistem Inventori Raja Dekor</title>
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
                <h1 class="page-title">Edit Barang</h1>
                <p class="page-subtitle">Perbarui data produk #<?php echo $id; ?></p>
            </div>
            <a href="index.php" class="btn-premium btn-ghost"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="form-card animate-in">
            <div class="form-card-header">
                <h3><i class="fas fa-pen-to-square" style="color: var(--color-warning); margin-right: 8px;"></i> Edit: <?php echo htmlspecialchars($data['nama_barang']); ?></h3>
            </div>
            <div class="form-card-body">
                <?php if ($pesan): ?>
                    <div class="alert-premium alert-danger-premium"><i class="fas fa-exclamation-circle"></i> <?php echo $pesan; ?></div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div style="margin-bottom: 18px;">
                        <label class="form-label-premium">Gambar Saat Ini</label>
                        <div class="img-preview-box">
                            <img src="<?php echo $gambar_path; ?>" width="100" height="100" style="object-fit:cover;" onerror="this.src='uploads/default.jpg'">
                        </div>
                        <label class="form-label-premium"><i class="fas fa-image" style="margin-right:4px;"></i> Ganti Gambar</label>
                        <input type="file" name="gambar" class="form-glass" accept="image/*">
                        <div class="form-hint">Kosongkan jika tidak ingin mengubah gambar</div>
                    </div>
                    <div style="margin-bottom: 18px;">
                        <label class="form-label-premium">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-glass" value="<?php echo htmlspecialchars($data['nama_barang']); ?>" required>
                    </div>
                    <div style="margin-bottom: 18px;">
                        <label class="form-label-premium">Kategori</label>
                        <select name="id_kategori" class="form-glass">
                            <option value="">-- Pilih Kategori --</option>
                            <?php while($kat = mysqli_fetch_assoc($kategori_list)): ?>
                                <option value="<?php echo $kat['id_kategori']; ?>" <?php echo ($data['id_kategori'] == $kat['id_kategori']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row g-3" style="margin-bottom: 18px;">
                        <div class="col-md-6">
                            <label class="form-label-premium">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-glass" value="<?php echo $data['harga']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-premium">Stok</label>
                            <input type="number" name="stok" class="form-glass" value="<?php echo $data['stok']; ?>" required>
                        </div>
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label class="form-label-premium">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-glass" value="<?php echo $data['tanggal_masuk']; ?>" required>
                    </div>
                    <div style="display:flex; gap: 12px;">
                        <button type="submit" class="btn-premium btn-warning-glow"><i class="fas fa-save"></i> Update</button>
                        <a href="index.php" class="btn-premium btn-ghost">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>