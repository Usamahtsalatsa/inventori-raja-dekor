<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$id = (int)$_GET['id'];

$query_gambar = "SELECT nama_barang, gambar FROM barang WHERE id_barang = $id";
$result_gambar = mysqli_query($koneksi, $query_gambar);
$data = mysqli_fetch_assoc($result_gambar);

if ($data) {
    catatLog($koneksi, 'Hapus Barang', "Menghapus barang: " . $data['nama_barang'] . " (ID: $id)");
    if ($data['gambar'] != 'default.jpg' && file_exists("uploads/" . $data['gambar'])) {
        unlink("uploads/" . $data['gambar']);
    }
}

$query = "DELETE FROM barang WHERE id_barang = $id";

if (mysqli_query($koneksi, $query)) {
    header("Location: index.php?sukses=hapus");
} else {
    header("Location: index.php?error=" . mysqli_error($koneksi));
}
exit();
?>