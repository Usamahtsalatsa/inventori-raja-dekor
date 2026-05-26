<?php
// Test koneksi database - hapus file ini setelah berhasil

// Fungsi bantu untuk mengambil env var dengan aman
function getEnvVar_test($key) {
    if (getenv($key)) return getenv($key);
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    return false;
}

header('Content-Type: application/json');

$db_url = getEnvVar_test('DATABASE_URL') ?: getEnvVar_test('MYSQL_URL');

$info = [
    'VERCEL_ENV' => getEnvVar_test('VERCEL') ? 'YES' : 'NO',
    'DATABASE_URL_EXISTS' => $db_url ? 'YES' : 'NO',
    'DATABASE_URL_LENGTH' => $db_url ? strlen($db_url) : 0,
    'PHP_VERSION' => phpversion(),
    'MYSQLI_AVAILABLE' => extension_loaded('mysqli') ? 'YES' : 'NO',
];

if ($db_url) {
    $parsed = parse_url($db_url);
    $info['DB_HOST'] = isset($parsed['host']) ? $parsed['host'] : 'NOT SET';
    $info['DB_PORT'] = isset($parsed['port']) ? $parsed['port'] : 'NOT SET';
    $info['DB_USER'] = isset($parsed['user']) ? $parsed['user'] : 'NOT SET';
    $info['DB_NAME'] = isset($parsed['path']) ? ltrim($parsed['path'], '/') : 'NOT SET';
    
    // Try to connect
    $host = $parsed['host'] ?? 'localhost';
    $user = $parsed['user'] ?? 'root';
    $password = $parsed['pass'] ?? '';
    $database = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '';
    $port = $parsed['port'] ?? 3306;
    
    $koneksi = @mysqli_connect($host, $user, $password, $database, $port);
    
    if ($koneksi) {
        $info['CONNECTION'] = 'SUCCESS';
        $info['SERVER_INFO'] = mysqli_get_server_info($koneksi);
        mysqli_close($koneksi);
    } else {
        $info['CONNECTION'] = 'FAILED';
        $info['ERROR'] = mysqli_connect_error();
    }
} else {
    $info['NOTE'] = 'DATABASE_URL not found in environment variables';
}

echo json_encode($info, JSON_PRETTY_PRINT);
?>