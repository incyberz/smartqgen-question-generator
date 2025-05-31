<?php
session_start();
require_once '../conn.php';
require_once '../includes/akses.php';
require_once '../includes/alert.php';

if (!isset($_SESSION['qgen_username'])) {
  echo 'Anda belum login';
  exit;
}

akses('drop_peserta_kelas');

$id_kelas = $_GET['id_kelas'] ?? kosong('id_kelas');
$username = $_GET['username'] ?? kosong('username');

$s = "DELETE FROM tb_peserta WHERE id_kelas = $id_kelas AND username = '$username'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
?>
OK