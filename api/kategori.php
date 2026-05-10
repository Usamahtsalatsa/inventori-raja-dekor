<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: /api/login.php");
    exit();
}
include 'koneksi.php';

$query = "SELECT * FROM kategori ORDER BY id_kategori DESC";
$result = mysqli_query($koneksi, $query);
$active_page = 'kategori';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Barang | Sistem Inventori Raja Dekor</title>
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
                <h1 class="page-title">Manajemen Kategori</h1>
                <p class="page-subtitle">Kelola kategori produk inventori</p>
            </div>
            <a href="tambah_kategori.php" class="btn-premium btn-primary-glow"><i class="fas fa-plus"></i> Tambah Kategori</a>
        </div>

        <div class="glass-card animate-in">
            <div class="card-body-premium" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table-premium">
                        <thead>
                            <tr><th>ID</th><th>Nama Kategori</th><th>Tanggal Dibuat</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td style="color: var(--text-muted);">#<?php echo $row['id_kategori']; ?></td>
                                        <td style="color: var(--text-primary); font-weight: 500;">
                                            <i class="fas fa-tag" style="color: var(--accent-violet); margin-right: 6px; font-size: 12px;"></i>
                                            <?php echo htmlspecialchars($row['nama_kategori']); ?>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($row['createdAt'])); ?></td>
                                        <td>
                                            <a href="edit_kategori.php?id=<?php echo $row['id_kategori']; ?>" class="btn-premium btn-warning-glow btn-sm"><i class="fas fa-pen"></i></a>
                                            <a href="hapus_kategori.php?id=<?php echo $row['id_kategori']; ?>" class="btn-premium btn-danger-glow btn-sm" onclick="return confirm('Yakin hapus kategori ini?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" style="text-align:center; padding: 40px; color: var(--text-muted);"><i class="fas fa-folder-open" style="font-size:24px; display:block; margin-bottom:8px;"></i>Belum ada kategori</td></tr>
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