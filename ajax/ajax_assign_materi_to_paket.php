<?php
session_start();
require_once '../conn.php';
require_once '../akses.php';
require_once '../includes/alert.php';

akses('assign_materi_to_paket');

$id_materi = $_GET['id_materi'] ?? kosong('id_materi');
$id_paket = $_GET['id_paket'] ?? kosong('id_paket');
$assign = $_GET['assign'] ?? kosong('assign');

if (!$id_materi) kosong('id_materi');
if (!$id_paket) kosong('id_paket');
if (!$assign) kosong('assign');

$id = "$id_paket--$id_materi";
if ($assign == 'true') {
  $s = "INSERT INTO tb_paket_soal_detail (
    id,
    id_paket,
    id_materi
  ) VALUES (
    '$id',
    $id_paket,
    $id_materi
  ) ON DUPLICATE KEY UPDATE
    assign_at = NOW()
  ";
} else {
  $s = "DELETE FROM tb_paket_soal_detail WHERE id = '$id'";
}
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
echo 'OK';
