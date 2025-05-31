<?php
if (isset($_POST['btn_claim_poin'])) {
  $s = "UPDATE tb_jawaban SET archived=1 WHERE id_paket = $_POST[btn_claim_poin]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $s = "UPDATE tb_paket SET status=100 WHERE id = $_POST[btn_claim_poin]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
} elseif (isset($_POST['posisi_ortu'])) {
  $s = "UPDATE tb_ortu SET posisi_ortu=$_POST[posisi_ortu] WHERE username = '$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
} elseif (isset($_POST['nama_kelas'])) {
  $nama_kelas = preg_replace('/[^a-zA-Z0-9 ]/', '', $_POST['nama_kelas']);
  // insert tb_kelas
  $s = "INSERT INTO tb_kelas (nama_kelas, username) VALUES ('$nama_kelas', '$username')";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
} elseif (isset($_POST['btn_tambah_peserta'])) {
  $pesan_error = '';
  $id_kelas = intval($_POST['id_kelas']);
  if (!$id_kelas) stop('invalid id_kelas pada dashboard process');
  $username_peserta = preg_replace('/[^a-z0-9]/', '',  strtolower($_POST['username_anak']));

  if (!strtotime($_POST['tanggal_lahir'])) {
    $pesan_error = 'invalid tanggal lahir anak';
  } else {
    // cek validitas tanggal lahir anak pada DB
    $s = "SELECT 1 FROM tb_user WHERE username = '$username_peserta' 
    AND (tanggal_lahir = '$_POST[tanggal_lahir]' OR tanggal_lahir is null)
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q)) {
      $pesan_error = "Data username atau tanggal lahir anak Anda tidak tepat";
    } else {
      // insert peserta
      $s = "INSERT INTO tb_peserta (
        id,
        id_kelas, 
        username
      ) VALUES (
        '$id_kelas-$username_peserta', 
        $id_kelas, 
        '$username_peserta'
      ) ON DUPLICATE KEY UPDATE 
        assign_at = NOW()
      ";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      jsurl();
    }
  }
} elseif ($_POST) {

  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
  stop('Belum ada handler untuk data POST diatas. Hubungi Developer!');
  exit;
}
