<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("Location: /api/login.php");
    exit();
}
include 'koneksi.php';

$id = (int)$_GET['id'];

// Ambil nama kategori untuk log
$query_nama = "SELECT nama_kategori FROM kategori WHERE id_kategori = $id";
$result_nama = mysqli_query($koneksi, $query_nama);
$data = mysqli_fetch_assoc($result_nama);

if ($data) {
    catatLog($koneksi, 'Hapus Kategori', "Menghapus kategori: " . $data['nama_kategori'] . " (ID: $id)");
    
    // Update barang yang menggunakan kategori ini menjadi NULL
    mysqli_query($koneksi, "UPDATE barang SET id_kategori = NULL WHERE id_kategori = $id");
    
    // Hapus kategori
    $query = "DELETE FROM kategori WHERE id_kategori = $id";
    mysqli_query($koneksi, $query);
}

header("Location: kategori.php?sukses=hapus");
exit();
?>