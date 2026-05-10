<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: /api/login.php");
    exit();
}
include 'koneksi.php';

// Cek apakah user admin (hanya admin boleh hapus)
$is_admin = ($_SESSION['level'] == 'admin');

// Proses hapus semua log
if ($is_admin && isset($_POST['delete_all'])) {
    $query = "DELETE FROM log_aktivitas";
    if (mysqli_query($koneksi, $query)) {
        catatLog($koneksi, 'Reset Log', "Semua log aktivitas dihapus oleh admin");
        $_SESSION['message'] = "Semua log berhasil dihapus.";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menghapus log.";
        $_SESSION['msg_type'] = "danger";
    }
    header("Location: /api/log_aktivitas.php");
    exit();
}

// Proses hapus log yang dipilih (checkbox)
if ($is_admin && isset($_POST['delete_selected']) && isset($_POST['selected_logs'])) {
    $selected = array_map('intval', $_POST['selected_logs']); // sanitasi
    $ids = implode(',', $selected);
    if (!empty($ids)) {
        $query = "DELETE FROM log_aktivitas WHERE id_log IN ($ids)";
        if (mysqli_query($koneksi, $query)) {
            catatLog($koneksi, 'Hapus Log', "Menghapus " . count($selected) . " log aktivitas");
            $_SESSION['message'] = "Berhasil menghapus " . count($selected) . " log.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menghapus log terpilih.";
            $_SESSION['msg_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Tidak ada log yang dipilih.";
        $_SESSION['msg_type'] = "warning";
    }
    header("Location: /api/log_aktivitas.php");
    exit();
}

// Ambil data log
$query = "SELECT * FROM log_aktivitas ORDER BY id_log DESC";
$result = mysqli_query($koneksi, $query);
$active_page = 'log';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas | Sistem Inventori Raja Dekor</title>
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
                <h1 class="page-title">Log Aktivitas</h1>
                <p class="page-subtitle">Riwayat semua aktivitas pengguna di sistem</p>
            </div>
            <?php if ($is_admin): ?>
            <div class="header-actions">
                <form method="POST" id="deleteAllForm" style="display: inline;">
                    <button type="submit" name="delete_all" class="btn-premium btn-danger-glow" onclick="return confirm('⚠️ PERINGATAN: Semua log akan dihapus permanen. Lanjutkan?')">
                        <i class="fas fa-trash-alt"></i> Hapus Semua
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert-premium alert-<?php echo $_SESSION['msg_type']; ?>-premium animate-in">
                <i class="fas fa-<?php echo $_SESSION['msg_type'] == 'success' ? 'check-circle' : ($_SESSION['msg_type'] == 'danger' ? 'exclamation-triangle' : 'info-circle'); ?>"></i>
                <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['msg_type']);
                ?>
            </div>
        <?php endif; ?>

        <div class="glass-card animate-in">
            <div class="card-header-premium">
                <h3><i class="fas fa-clock-rotate-left" style="color: var(--accent-violet); margin-right: 8px;"></i> Riwayat Aktivitas</h3>
                <div>
                    <?php if ($is_admin): ?>
                    <span class="badge-premium badge-purple" style="margin-right: 12px;">
                        <i class="fas fa-shield-alt"></i> Mode Admin
                    </span>
                    <?php endif; ?>
                    <span class="badge-premium badge-ghost"><?php echo mysqli_num_rows($result); ?> entri</span>
                </div>
            </div>
            <div class="card-body-premium" style="padding: 0;">
                <form method="POST" id="logForm">
                    <?php if ($is_admin && mysqli_num_rows($result) > 0): ?>
                    <div style="padding: 12px 20px; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div style="display: flex; gap: 12px; align-items: center;">
                            <button type="submit" name="delete_selected" class="btn-premium btn-sm btn-danger-glow" onclick="return confirmDeleteSelected()" style="padding: 6px 12px;">
                                <i class="fas fa-trash"></i> Hapus yang Dipilih
                            </button>
                            <label style="color: var(--text-secondary); font-size: 13px; cursor: pointer;">
                                <input type="checkbox" id="selectAllCheckbox"> Pilih Semua
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <?php if ($is_admin): ?>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="selectAllHeader" style="transform: scale(1.1); cursor: pointer;">
                                    </th>
                                    <?php endif; ?>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Aksi</th>
                                    <th>Detail</th>
                                    <th>IP Address</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($result) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <?php if ($is_admin): ?>
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="selected_logs[]" value="<?php echo $row['id_log']; ?>" class="log-checkbox" style="transform: scale(1.1); cursor: pointer;">
                                            </td>
                                            <?php endif; ?>
                                            <td style="color: var(--text-muted);">#<?php echo $row['id_log']; ?></td>
                                            <td style="color: var(--text-primary); font-weight: 500;">
                                                <i class="fas fa-user-circle" style="color: var(--accent-violet); margin-right: 4px;"></i>
                                                <?php echo htmlspecialchars($row['username']); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $aksi = $row['aksi'];
                                                $badge_class = 'badge-indigo';
                                                if (strpos($aksi, 'Tambah') !== false) $badge_class = 'badge-success';
                                                elseif (strpos($aksi, 'Edit') !== false) $badge_class = 'badge-warning';
                                                elseif (strpos($aksi, 'Hapus') !== false) $badge_class = 'badge-danger';
                                                elseif (strpos($aksi, 'Login') !== false) $badge_class = 'badge-purple';
                                                ?>
                                                <span class="badge-premium <?php echo $badge_class; ?>"><?php echo htmlspecialchars($aksi); ?></span>
                                            </td>
                                            <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($row['detail']); ?></td>
                                            <td><code style="background: rgba(255,255,255,0.06); padding: 2px 8px; border-radius: 4px; font-size: 11px; color: var(--text-muted);"><?php echo $row['ip_address']; ?></code></td>
                                            <td style="white-space: nowrap;"><?php echo date('d M Y · H:i', strtotime($row['createdAt'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="<?php echo $is_admin ? '7' : '6'; ?>" style="text-align:center; padding: 40px; color: var(--text-muted);"><i class="fas fa-history" style="font-size:24px; display:block; margin-bottom:8px;"></i>Belum ada aktivitas tercatat</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi konfirmasi hapus yang dipilih
        function confirmDeleteSelected() {
            let checkboxes = document.querySelectorAll('.log-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Tidak ada log yang dipilih.');
                return false;
            }
            return confirm('Hapus ' + checkboxes.length + ' log yang dipilih? Tindakan ini tidak dapat dibatalkan.');
        }

        // Fungsi Select All
        const selectAllHeader = document.getElementById('selectAllHeader');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const allCheckboxes = document.querySelectorAll('.log-checkbox');

        function updateSelectAllCheckbox() {
            if (selectAllHeader) {
                const allChecked = allCheckboxes.length > 0 && [...allCheckboxes].every(cb => cb.checked);
                selectAllHeader.checked = allChecked;
            }
            if (selectAllCheckbox) {
                const allChecked = allCheckboxes.length > 0 && [...allCheckboxes].every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            }
        }

        if (selectAllHeader) {
            selectAllHeader.addEventListener('change', function(e) {
                allCheckboxes.forEach(cb => cb.checked = e.target.checked);
            });
        }
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function(e) {
                allCheckboxes.forEach(cb => cb.checked = e.target.checked);
            });
        }
        allCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectAllCheckbox);
        });
    </script>
</body>
</html>