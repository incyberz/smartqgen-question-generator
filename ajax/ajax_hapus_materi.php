<?php
session_start();
require_once '../conn.php';
require_once '../akses.php';
require_once '../includes/alert.php';

akses('hapus_materi');

$id_materi = $_GET['id_materi'] ?? kosong('id_materi');
if (!$id_materi) kosong('id_materi');

# ============================================================
# GET ID MAPEL FOR AUTO HAPUS 
# ============================================================
$s = "SELECT id_mapel FROM tb_materi WHERE id=$id_materi";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$id_mapel = $d['id_mapel'];

# ============================================================
# AUTO HAPUS MAPEL JIKA MATERI TERAKHIR DIHAPUS
# ============================================================
$s = "SELECT 1 FROM tb_materi WHERE id_mapel=$id_mapel";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$auto_hapus_mapel = mysqli_num_rows($q) == 1 ? 1 : 0;

$s = "DELETE FROM tb_materi WHERE id=$id_materi";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

if ($auto_hapus_mapel) {
  $s = "DELETE FROM tb_mapel WHERE id=$id_mapel";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
}

echo 'OK';
