<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: login.php");
    exit();
}

// Data statistik
$total_barang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang"))['total'];
$total_stok = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(stok),0) as total FROM barang"))['total'];
$total_nilai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(harga * stok),0) as total FROM barang"))['total'];
$barang_habis = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang WHERE stok <= 5"))['total'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kategori"))['total'];
$total_log = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM log_aktivitas"))['total'];

// Data untuk grafik stok
$query_chart = "SELECT b.nama_barang, b.stok, k.nama_kategori 
                FROM barang b 
                LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                ORDER BY b.id_barang";
$result_chart = mysqli_query($koneksi, $query_chart);
$nama_barang_arr = [];
$stok_arr = [];
while ($row = mysqli_fetch_assoc($result_chart)) {
    $nama_barang_arr[] = $row['nama_barang'];
    $stok_arr[] = $row['stok'];
}

// Data stok menipis untuk tooltip
$query_habis_detail = "SELECT nama_barang, stok FROM barang WHERE stok <= 5 ORDER BY stok ASC";
$result_habis_detail = mysqli_query($koneksi, $query_habis_detail);
$data_habis = [];
while($row = mysqli_fetch_assoc($result_habis_detail)) {
    $data_habis[] = $row;
}

$active_page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistem Inventori Raja Dekor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-wrapper">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Selamat datang kembali, <?php echo $_SESSION['nama_lengkap']; ?> · <?php echo date('l, d F Y'); ?></p>
            </div>
        </div>

        <!-- Alert Stok Menipis -->
        <?php if($barang_habis > 0): ?>
        <div class="alert-premium alert-warning-premium animate-in">
            <i class="fas fa-exclamation-triangle"></i>
            Terdapat <strong><?php echo $barang_habis; ?> barang</strong> dengan stok menipis. 
            <a href="index.php?stok_menipis=1">Lihat detail →</a>
        </div>
        <?php endif; ?>

        <!-- Row 1: Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="stat-card card-indigo animate-in animate-in-1">
                    <div class="stat-icon"><i class="fas fa-boxes-stacked"></i></div>
                    <div class="stat-label">Total Barang</div>
                    <div class="stat-value"><?php echo $total_barang; ?></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stat-card card-success animate-in animate-in-2">
                    <div class="stat-icon"><i class="fas fa-cubes"></i></div>
                    <div class="stat-label">Total Stok</div>
                    <div class="stat-value"><?php echo number_format($total_stok, 0, ',', '.'); ?></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stat-card card-purple animate-in animate-in-3">
                    <div class="stat-icon"><i class="fas fa-coins"></i></div>
                    <div class="stat-label">Nilai Inventori</div>
                    <div class="stat-value small">Rp <?php echo number_format($total_nilai,0,',','.'); ?></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stat-card card-danger animate-in animate-in-4 <?php echo ($barang_habis > 0) ? 'pulse-danger' : ''; ?>" 
                     onclick="window.location.href='index.php?stok_menipis=1'" 
                     onmouseenter="showStokTooltip(event)" onmouseleave="hideStokTooltip()">
                    <div class="stat-icon"><i class="fas fa-triangle-exclamation"></i></div>
                    <div class="stat-label">Stok Menipis</div>
                    <div class="stat-value"><?php echo $barang_habis; ?></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stat-card card-info animate-in animate-in-5" onclick="window.location.href='kategori.php'">
                    <div class="stat-icon"><i class="fas fa-tags"></i></div>
                    <div class="stat-label">Total Kategori</div>
                    <div class="stat-value"><?php echo $total_kategori; ?></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stat-card card-warning animate-in animate-in-6" onclick="window.location.href='log_aktivitas.php'">
                    <div class="stat-icon"><i class="fas fa-clock-rotate-left"></i></div>
                    <div class="stat-label">Log Aktivitas</div>
                    <div class="stat-value"><?php echo $total_log; ?></div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="glass-card mb-4 animate-in" style="animation-delay: 0.35s;">
            <div class="card-header-premium">
                <h3><i class="fas fa-chart-bar" style="color: var(--accent-violet); margin-right: 8px;"></i> Grafik Stok Barang</h3>
            </div>
            <div class="card-body-premium">
                <div class="chart-container">
                    <canvas id="stokChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Latest + Low Stock -->
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="glass-card animate-in" style="animation-delay: 0.4s;">
                    <div class="card-header-premium">
                        <h3><i class="fas fa-sparkles" style="color: #818cf8; margin-right: 8px;"></i> 5 Barang Terbaru</h3>
                    </div>
                    <div class="card-body-premium">
                        <div class="list-premium">
                            <?php $top = mysqli_query($koneksi, "SELECT nama_barang, stok FROM barang ORDER BY id_barang DESC LIMIT 5"); ?>
                            <?php while($row = mysqli_fetch_assoc($top)): ?>
                                <div class="list-item">
                                    <span style="color: var(--text-primary);"><?php echo htmlspecialchars($row['nama_barang']); ?></span>
                                    <span class="badge-premium badge-indigo">Stok: <?php echo $row['stok']; ?></span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="glass-card animate-in" style="animation-delay: 0.45s;">
                    <div class="card-header-premium">
                        <h3><i class="fas fa-triangle-exclamation" style="color: #fbbf24; margin-right: 8px;"></i> Stok Menipis (≤5)</h3>
                    </div>
                    <div class="card-body-premium">
                        <div class="list-premium">
                            <?php $habis = mysqli_query($koneksi, "SELECT nama_barang, stok FROM barang WHERE stok <= 5 ORDER BY stok ASC"); ?>
                            <?php if(mysqli_num_rows($habis) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($habis)): ?>
                                    <div class="list-item">
                                        <span style="color: var(--text-primary);"><?php echo htmlspecialchars($row['nama_barang']); ?></span>
                                        <span class="badge-premium badge-danger">Sisa: <?php echo $row['stok']; ?></span>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="list-item" style="justify-content:center; color: var(--color-success);">
                                    <i class="fas fa-check-circle" style="margin-right: 6px;"></i> Semua stok aman
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Tooltip Stok Menipis -->
    <div class="tooltip-premium" id="stokTooltip">
        <div class="tooltip-title"><i class="fas fa-triangle-exclamation"></i> Stok Menipis</div>
        <div id="tooltipContent">Loading...</div>
    </div>

    <script>
        const stokMenipisData = <?php echo json_encode($data_habis); ?>;

        function showStokTooltip(event) {
            const tooltip = document.getElementById('stokTooltip');
            const contentDiv = document.getElementById('tooltipContent');
            if (stokMenipisData.length > 0) {
                let html = '';
                stokMenipisData.forEach(item => {
                    const color = item.stok <= 2 ? 'badge-danger' : 'badge-warning';
                    html += `<div class="tooltip-item">
                        <span>${item.nama_barang}</span>
                        <span class="badge-premium ${color}">Sisa: ${item.stok}</span>
                    </div>`;
                });
                contentDiv.innerHTML = html;
            } else {
                contentDiv.innerHTML = '<span style="color: var(--color-success);">Semua stok aman</span>';
            }
            tooltip.style.display = 'block';
            tooltip.style.left = (event.pageX + 15) + 'px';
            tooltip.style.top = (event.pageY - 40) + 'px';
        }

        function hideStokTooltip() { document.getElementById('stokTooltip').style.display = 'none'; }

        document.addEventListener('mousemove', function(event) {
            const tooltip = document.getElementById('stokTooltip');
            if (tooltip.style.display === 'block') {
                tooltip.style.left = (event.pageX + 15) + 'px';
                tooltip.style.top = (event.pageY - 40) + 'px';
            }
        });

        // Chart
        const ctx = document.getElementById('stokChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.6)');
        gradient.addColorStop(1, 'rgba(139, 92, 246, 0.1)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($nama_barang_arr); ?>,
                datasets: [{
                    label: 'Jumlah Stok',
                    data: <?php echo json_encode($stok_arr); ?>,
                    backgroundColor: gradient,
                    borderColor: 'rgba(99, 102, 241, 0.8)',
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { labels: { color: 'rgba(240,240,248,0.65)', font: { family: 'Inter', size: 12 } } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.04)' },
                        ticks: { color: 'rgba(255,255,255,0.5)', font: { family: 'Inter' } },
                        title: { display: true, text: 'Jumlah Stok', color: 'rgba(255,255,255,0.5)', font: { family: 'Inter' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: 'rgba(255,255,255,0.5)', font: { family: 'Inter', size: 11 } },
                        title: { display: true, text: 'Nama Barang', color: 'rgba(255,255,255,0.5)', font: { family: 'Inter' } }
                    }
                }
            }
        });
    </script>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>