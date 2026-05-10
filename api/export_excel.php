<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: /api/login.php");
    exit();
}
include 'koneksi.php';

$query = "SELECT b.id_barang, b.nama_barang, b.harga, b.stok, b.tanggal_masuk, k.nama_kategori 
          FROM barang b 
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
          ORDER BY b.id_barang";
$result = mysqli_query($koneksi, $query);

$total_nilai = 0;
$data_barang = [];
while ($row = mysqli_fetch_assoc($result)) {
    $total_nilai += $row['harga'] * $row['stok'];
    $data_barang[] = $row;
}

$filename = "Laporan_Inventori_Raja_Dekor_" . date('Y-m-d_His') . ".xls";

// Bersihkan semua buffer yang mungkin mengganggu
while (ob_get_level()) ob_end_clean();

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");
header("Expires: 0");
header("Pragma: public");

// Mulai output HTML dengan format agar Excel mengenali sebagai spreadsheet
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Laporan Inventori</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header-title { font-size: 20pt; font-weight: bold; color: #1e2a3a; text-align: center; margin-bottom: 4px; }
        .header-sub { font-size: 12pt; color: #555; text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 15px; padding: 8px; background: #f5f5f5; font-size: 10pt; }
        .info-left { float: left; }
        .info-right { float: right; }
        .clear { clear: both; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th { 
            background: #1e2a3a; 
            color: white; 
            font-weight: bold; 
            font-size: 11pt; 
            padding: 8px; 
            border: 1px solid #aaa;
            text-align: center;
        }
        td { 
            border: 1px solid #ccc; 
            padding: 6px 8px; 
            font-size: 10pt; 
            vertical-align: middle;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row td { 
            background: #e8e8e8; 
            font-weight: bold; 
            border-top: 2px solid #aaa;
            font-size: 11pt;
        }
        .footer { text-align: center; font-size: 9pt; color: #777; margin-top: 20px; border-top: 1px solid #ccc; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header-title">RAJA DEKOR</div>
    <div class="header-sub">Laporan Inventori Barang</div>
    
    <div class="info">
        <div class="info-left">📅 Dicetak: ' . date('d-m-Y H:i:s') . '</div>
        <div class="info-right">👤 Dicetak oleh: ' . htmlspecialchars($_SESSION['nama_lengkap']) . '</div>
        <div class="clear"></div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Harga (Rp)</th>
                <th>Stok</th>
                <th>Subtotal (Rp)</th>
                <th>Tanggal Masuk</th>
            </tr>
        </thead>
        <tbody>';

$no = 1;
foreach ($data_barang as $item) {
    $subtotal = $item['harga'] * $item['stok'];
    $tanggal_masuk = date('Y-m-d', strtotime($item['tanggal_masuk']));
    echo '<tr>
            <td class="text-center">' . $no++ . '</td>
            <td>' . htmlspecialchars($item['nama_barang']) . '</td>
            <td class="text-center">' . htmlspecialchars($item['nama_kategori'] ?? '-') . '</td>
            <td class="text-right">Rp ' . number_format($item['harga'], 0, ',', '.') . '</td>
            <td class="text-center">' . $item['stok'] . '</td>
            <td class="text-right">Rp ' . number_format($subtotal, 0, ',', '.') . '</td>
            <td class="text-center">' . $tanggal_masuk . '</td>
        </tr>';
}
echo '<tr class="total-row">
        <td colspan="5" class="text-right"><strong>TOTAL NILAI INVENTORI</strong></td>
        <td colspan="2"><strong>Rp ' . number_format($total_nilai, 0, ',', '.') . '</strong></td>
     </tr>
        </tbody>
    </table>
    
    <div class="footer">
        Sistem Inventori Berbasis Web | Raja Dekor — Laporan dihasilkan otomatis
    </div>
</body>
</html>';
exit;
?>