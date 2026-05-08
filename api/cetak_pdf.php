<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$query = "SELECT b.*, k.nama_kategori 
          FROM barang b 
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
          ORDER BY b.id_barang";
$result = mysqli_query($koneksi, $query);

$user = $_SESSION['nama_lengkap'];
$tanggal = date('d-m-Y H:i:s');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventori - Raja Dekor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 30px;
            background: white;
            color: #1e2a3a;
        }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e2a3a; padding-bottom: 20px; }
        .header h1 { font-size: 28px; margin-bottom: 5px; }
        .header p { font-size: 12px; color: #666; }
        .info { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 12px; background: #f5f5f5; padding: 10px 15px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #1e2a3a; color: white; padding: 12px; text-align: left; font-size: 13px; }
        td { border-bottom: 1px solid #ddd; padding: 10px 12px; font-size: 12px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 20px; }
        .badge-stok { background: #28a745; color: white; padding: 2px 8px; border-radius: 20px; font-size: 11px; }
        .badge-danger { background: #dc3545; color: white; padding: 2px 8px; border-radius: 20px; font-size: 11px; }
        @media print {
            body { padding: 0; }
            .btn-print { display: none; }
        }
        .btn-print {
            background: #1e2a3a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>

    <div class="header">
        <h1>RAJA DEKOR</h1>
        <p>Laporan Inventori Barang</p>
    </div>

    <div class="info">
        <span>📅 Dicetak: <?php echo $tanggal; ?></span>
        <span>👤 Dicetak oleh: <?php echo $user; ?></span>
    </div>

    <table>
        <thead>
            <tr><th>ID</th><th>Nama Barang</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Tanggal Masuk</th></tr>
        </thead>
        <tbody>
            <?php 
            $total_nilai = 0;
            while($row = mysqli_fetch_assoc($result)): 
                $total_nilai += $row['harga'] * $row['stok'];
            ?>
                <tr>
                    <td><?php echo $row['id_barang']; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                    <td><?php echo $row['nama_kategori'] ?? '-'; ?></td>
                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td><?php echo $row['stok'] <= 5 ? '<span class="badge-danger">'.$row['stok'].'</span>' : '<span class="badge-stok">'.$row['stok'].'</span>'; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['tanggal_masuk'])); ?></td>
                </tr>
            <?php endwhile; ?>
            <tr style="background:#f0f0f0; font-weight:bold;">
                <td colspan="3" style="text-align:right;">Total Nilai Inventori:</td>
                <td colspan="3">Rp <?php echo number_format($total_nilai, 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Inventori Berbasis Web | Raja Dekor</p>
        <p>*Laporan ini dihasilkan secara otomatis oleh sistem</p>
    </div>
</body>
</html>