<?php
session_start();
require_once '../conn.php';
require_once '../akses.php';
require_once '../includes/alert.php';

akses('lihat_kalimat_soal_per_materi');

$id_materi = $_GET['id_materi'] ?? kosong('id_materi');
if (!$id_materi) kosong('id_materi');
$s = "SELECT soal_template FROM tb_soal WHERE id_materi=$id_materi";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$div = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $div .= "
    <div class='mb1'>$i. $d[soal_template]</div>
  ";
}
echo $div ? "<div class='f12'>$div</div>" : 'soal tidak ditemukan (via AJAX).';
