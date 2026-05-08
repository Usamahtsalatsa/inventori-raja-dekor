<?php
// Fungsi catatLog (sementara tidak error)
function catatLog($koneksi, $aksi, $detail = '') {
    // Untuk sekarang, tidak perlu mencatat log dulu
    return true;
}

// Cek apakah di lingkungan Vercel / Production
$isProduction = getenv('VERCEL') || getenv('DATABASE_URL');

if ($isProduction && getenv('DATABASE_URL')) {
    // Mode PRODUCTION (Railway nanti)
    $db_url = getenv('DATABASE_URL');
    $parsed = parse_url($db_url);

    $host = $parsed['host'] ?? '';
    $user = $parsed['user'] ?? '';
    $password = $parsed['pass'] ?? '';
    $database = ltrim($parsed['path'] ?? '', '/');
    $port = $parsed['port'] ?? 3306;

    // Coba koneksi
    $koneksi = @mysqli_connect($host, $user, $password, $database, $port);
    if ($koneksi) {
        mysqli_set_charset($koneksi, 'utf8');
    } else {
        // Koneksi gagal, biarkan null dulu (nanti setelah Railway diisi)
        $koneksi = null;
    }
} else {
    // Mode LOCAL (XAMPP)
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'db_inventori_pro';
    $port = 3306;

    $koneksi = mysqli_connect($host, $user, $password, $database, $port);
    if ($koneksi) {
        mysqli_set_charset($koneksi, 'utf8');
    }
}
?><?php
// Fungsi catatLog (sementara tidak error)
function catatLog($koneksi, $aksi, $detail = '') {
    // Untuk sekarang, tidak perlu mencatat log dulu
    return true;
}

// Cek apakah di lingkungan Vercel / Production
$isProduction = getenv('VERCEL') || getenv('DATABASE_URL');

if ($isProduction && getenv('DATABASE_URL')) {
    // Mode PRODUCTION (Railway nanti)
    $db_url = getenv('DATABASE_URL');
    $parsed = parse_url($db_url);

    $host = $parsed['host'] ?? '';
    $user = $parsed['user'] ?? '';
    $password = $parsed['pass'] ?? '';
    $database = ltrim($parsed['path'] ?? '', '/');
    $port = $parsed['port'] ?? 3306;

    // Coba koneksi
    $koneksi = @mysqli_connect($host, $user, $password, $database, $port);
    if ($koneksi) {
        mysqli_set_charset($koneksi, 'utf8');
    } else {
        // Koneksi gagal, biarkan null dulu (nanti setelah Railway diisi)
        $koneksi = null;
    }
} else {
    // Mode LOCAL (XAMPP)
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'db_inventori_pro';
    $port = 3306;

    $koneksi = mysqli_connect($host, $user, $password, $database, $port);
    if ($koneksi) {
        mysqli_set_charset($koneksi, 'utf8');
    }
}
?>
