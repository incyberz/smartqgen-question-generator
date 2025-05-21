<?php
if (isset($_POST['btn_laporkan'])) {
  $t = explode('--', $_POST['btn_laporkan']);
  $id_alasan = $t[0] ?? kosong('id_alasan');
  $id_soal = $t[1] ?? kosong('id_soal');
  $no = $t[2] ?? kosong('no');
  $id = "$id_soal--$id_alasan--$id_pengunjung";
  $s = "INSERT INTO tb_laporkan (
    id, 
    id_soal, 
    id_alasan,
    id_pengunjung 
  ) VALUES (
    '$id', 
    '$id_soal',
    '$id_alasan', 
    '$id_pengunjung'
  ) ON DUPLICATE KEY UPDATE 
    tanggal = CURRENT_TIMESTAMP
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $next_no = $no + 1;
  jsurl("?quiz-started&no=$next_no");
} elseif (isset($_POST['btn_submit_jawaban'])) {
  $jawaban = $_POST['jawaban'] === '' ? '(no-answer)' :  $_POST['jawaban'];
  $s = "UPDATE tb_jawaban SET jawaban = '$jawaban' WHERE id_pengunjung = $id_pengunjung AND id_soal = $_POST[btn_submit_jawaban]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  // mundurkan 
  $_SESSION['mulai_mengerjakan'] = time() - 3600;

  jsurl();
} elseif (isset($_POST['btn_start_quiz_lagi'])) {
  $s = "DELETE FROM tb_jawaban WHERE id_pengunjung = $id_pengunjung";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl('?quiz-started');
} elseif ($_POST) {

  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
  stop('Belum ada handler untuk data POST diatas. Hubungi Developer!');
  exit;
}
