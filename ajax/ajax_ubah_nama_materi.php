<?php
session_start();
require_once '../conn.php';
require_once '../akses.php';
require_once '../includes/alert.php';

akses('ubah_nama_materi');

$id_materi = $_GET['id_materi'] ?? kosong('id_materi');
if (!$id_materi) kosong('id_materi');
$new_nama_materi = $_GET['new_nama_materi'] ?? kosong('new_nama_materi');
if (!$new_nama_materi) kosong('new_nama_materi');
$s = "UPDATE tb_materi SET nama_materi='$new_nama_materi' WHERE id=$id_materi";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
echo 'OK';
