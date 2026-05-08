<!-- Sidebar Component -->
<button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon"><i class="fas fa-crown"></i></div>
            <div class="logo-text">
                <h4>RAJA DEKOR</h4>
                <span>Inventory System</span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="nav-section-title">Menu Utama</span>
            <a href="api/dashboard.php" class="nav-link <?php echo ($active_page == 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i><span>Dashboard</span>
            </a>
            <a href="api/index.php" class="nav-link <?php echo ($active_page == 'data_barang') ? 'active' : ''; ?>">
                <i class="fas fa-box-open"></i><span>Data Barang</span>
            </a>
            <a href="api/tambah.php" class="nav-link <?php echo ($active_page == 'tambah_barang') ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i><span>Tambah Barang</span>
            </a>
            <a href="api/kategori.php" class="nav-link <?php echo ($active_page == 'kategori') ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i><span>Kategori</span>
            </a>
            <a href="api/log_aktivitas.php" class="nav-link <?php echo ($active_page == 'log') ? 'active' : ''; ?>">
                <i class="fas fa-history"></i><span>Log Aktivitas</span>
            </a>
        </div>

        <div class="nav-divider"></div>

        <div class="nav-section">
            <span class="nav-section-title">Laporan</span>
            <a href="api/export_excel.php" class="nav-link">
                <i class="fas fa-file-excel"></i><span>Export Excel</span>
            </a>
            <a href="api/cetak_pdf.php" class="nav-link" target="_blank">
                <i class="fas fa-file-pdf"></i><span>Export PDF</span>
            </a>
        </div>

        <div class="nav-divider"></div>

        <div class="nav-section">
            <a href="api/logout.php" class="nav-link" onclick="return confirm('Yakin ingin logout?')">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="user-avatar">
            <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 2)); ?>
        </div>
        <div class="user-info">
            <span class="user-name"><?php echo $_SESSION['nama_lengkap'] ?? 'User'; ?></span>
            <span class="user-role"><?php echo ucfirst($_SESSION['level'] ?? 'staff'); ?></span>
        </div>
    </div>
</aside>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}
</script>