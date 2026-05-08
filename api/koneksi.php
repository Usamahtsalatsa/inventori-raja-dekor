<?php
// Fungsi untuk mencatat log aktivitas (harus didefinisikan sebelum digunakan)
function catatLog($koneksi, $aksi, $detail = '') {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $detail = mysqli_real_escape_string($koneksi, $detail);
    
    $query = "INSERT INTO log_aktivitas (username, aksi, detail, ip_address) 
              VALUES ('$username', '$aksi', '$detail', '$ip_address')";
    mysqli_query($koneksi, $query);
}

// Cek apakah di lingkungan production (Vercel / Railway)
if (getenv('VERCEL') || getenv('RAILWAY_ENVIRONMENT') || getenv('DATABASE_URL')) {
    // Mode PRODUCTION (online)
    $db_url = getenv('DATABASE_URL');
    
    // Parse URL database dari Railway
    $parsed = parse_url($db_url);
    
    $host = $parsed['host'];
    $user = $parsed['user'];
    $password = $parsed['pass'];
    $database = ltrim($parsed['path'], '/');
    $port = $parsed['port'] ?? 3306;
} else {
    // Mode LOCAL (XAMPP)
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "db_inventori_pro";
    $port = 3306;
}

// Buat koneksi database
$koneksi = mysqli_connect($host, $user, $password, $database, $port);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($koneksi, "utf8");


?>