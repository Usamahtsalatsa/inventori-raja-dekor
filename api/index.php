<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$keyword = "";
if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $query = "SELECT b.*, k.nama_kategori 
              FROM barang b 
              LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
              WHERE b.nama_barang LIKE '%$keyword%' 
              ORDER BY b.id_barang DESC";
} elseif (isset($_GET['stok_menipis']) && $_GET['stok_menipis'] == 1) {
    $query = "SELECT b.*, k.nama_kategori 
              FROM barang b 
              LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
              WHERE b.stok <= 5 
              ORDER BY b.stok ASC";
} else {
    $query = "SELECT b.*, k.nama_kategori 
              FROM barang b 
              LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
              ORDER BY b.id_barang DESC";
}

$result = mysqli_query($koneksi, $query);
$active_page = 'data_barang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang | Sistem Inventori Raja Dekor</title>
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
                <h1 class="page-title">Data Barang</h1>
                <p class="page-subtitle">Kelola semua produk inventori Raja Dekor</p>
            </div>
            <div class="header-actions">
                <span class="badge-premium badge-ghost"><i class="fas fa-user" style="margin-right:4px;"></i> <?php echo $_SESSION['username']; ?></span>
                <a href="tambah.php" class="btn-premium btn-primary-glow"><i class="fas fa-plus"></i> Tambah Barang</a>
            </div>
        </div>

        <?php if(isset($_GET['stok_menipis']) && $_GET['stok_menipis'] == 1): ?>
            <div class="alert-premium alert-warning-premium">
                <i class="fas fa-filter"></i> Menampilkan barang dengan stok menipis. <a href="index.php">Lihat semua barang →</a>
            </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <form method="GET" class="search-bar animate-in">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="cari" class="form-glass" placeholder="Cari nama barang..." value="<?php echo htmlspecialchars($keyword); ?>">
            </div>
            <button type="submit" class="btn-premium btn-primary-glow"><i class="fas fa-search"></i> Cari</button>
            <a href="index.php?stok_menipis=1" class="btn-premium btn-danger-glow btn-sm"><i class="fas fa-triangle-exclamation"></i> Stok Menipis</a>
        </form>

        <!-- Table -->
        <div class="glass-card animate-in" style="animation-delay: 0.1s;">
            <div class="card-body-premium" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>ID</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr class="<?php echo ($row['stok'] <= 5) ? 'row-warning' : ''; ?>">
                                        <td><img src="uploads/<?php echo !empty($row['gambar']) ? $row['gambar'] : 'default.jpg'; ?>" class="product-thumb" onerror="this.src='uploads/default.jpg'"></td>
                                        <td style="color: var(--text-muted);">#<?php echo $row['id_barang']; ?></td>
                                        <td style="color: var(--text-primary); font-weight: 500;"><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                        <td><span class="badge-premium badge-purple"><?php echo $row['nama_kategori'] ?? '—'; ?></span></td>
                                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if($row['stok'] <= 5): ?>
                                                <span class="badge-premium badge-danger"><i class="fas fa-exclamation-circle" style="margin-right:3px;"></i><?php echo $row['stok']; ?></span>
                                            <?php else: ?>
                                                <span class="badge-premium badge-success"><?php echo $row['stok']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($row['tanggal_masuk'])); ?></td>
                                        <td>
                                            <a href="edit.php?id=<?php echo $row['id_barang']; ?>" class="btn-premium btn-warning-glow btn-sm"><i class="fas fa-pen"></i></a>
                                            <a href="hapus.php?id=<?php echo $row['id_barang']; ?>" class="btn-premium btn-danger-glow btn-sm" onclick="return confirm('Yakin hapus barang ini?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" style="text-align:center; padding: 40px; color: var(--text-muted);"><i class="fas fa-inbox" style="font-size:24px; display:block; margin-bottom:8px;"></i>Belum ada data barang</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>