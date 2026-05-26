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

// Fungsi bantu untuk mengambil env var dengan aman
function getEnvVar($key) {
    if (getenv($key)) return getenv($key);
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    return false;
}

$db_url = getEnvVar('DATABASE_URL') ?: getEnvVar('MYSQL_URL');

// Cek apakah di lingkungan production (Vercel / Railway) atau ada db_url
if ($db_url || getEnvVar('VERCEL') || getEnvVar('RAILWAY_ENVIRONMENT')) {
    // Mode PRODUCTION (online)
    
    if ($db_url) {
        // Parse URL database dari Railway
        $parsed = parse_url($db_url);
        
        $host = isset($parsed['host']) ? $parsed['host'] : 'localhost';
        $user = isset($parsed['user']) ? $parsed['user'] : 'root';
        $password = isset($parsed['pass']) ? $parsed['pass'] : '';
        $database = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '';
        $port = isset($parsed['port']) ? $parsed['port'] : 3306;
    } else {
        // Fallback manual jika format URL tidak digunakan
        $host = getEnvVar('DB_HOST') ?: 'localhost';
        $user = getEnvVar('DB_USER') ?: 'root';
        $password = getEnvVar('DB_PASS') ?: '';
        $database = getEnvVar('DB_NAME') ?: '';
        $port = getEnvVar('DB_PORT') ?: 3306;
    }
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