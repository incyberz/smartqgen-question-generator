<?php
if (isset($_POST['username_anak'])) {
  $pesan_error = null;
  $username_anak = preg_replace('/[^a-z0-9]/', '', strtolower($_POST['username_anak']));
  $tgl = $_POST['tanggal_lahir'];
  if (!strtotime($tgl)) die('Invalid tanggal lahir pada Proses Tambah Anak.');

  $s = "SELECT * FROM tb_user WHERE username = '$username_anak' 
  AND (tanggal_lahir = '$tgl' OR tanggal_lahir is null) 
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) {
    $pesan_error = "Tidak ada data anak dengan username [$username]";
  } else {
    # ============================================================
    # ASSIGN TO KELAS
    # ============================================================


  }

  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  exit;
}
