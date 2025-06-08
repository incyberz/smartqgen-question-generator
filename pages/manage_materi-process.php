<?php
if (isset($_POST['btn_add_mapel'])) {
  $jenjang = $_POST['btn_add_mapel'];
  $nama_mapel = $_POST['nama_mapel'];
  $singkatan = $_POST['singkatan'];

  $nama_mapel = ucwords(preg_replace('/[^a-z0-9 ]/', '', strtolower($nama_mapel)));
  $singkatan = preg_replace('/[^a-z0-9]/', '', strtolower($singkatan));

  $s = "SELECT MAX(urutan)+1 as urutan FROM tb_mapel WHERE jenjang='$jenjang'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);
  $urutan = $d['urutan'] ?? 1;

  $s = "INSERT INTO tb_mapel (
    jenjang,
    nama_mapel,
    singkatan,
    urutan,
    created_by
  ) VALUES (
    '$jenjang',
    '$nama_mapel',
    '$singkatan',
    '$urutan',
    '$username'
  )";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
} elseif (isset($_POST) and $_POST) {
  echo '<pre>';
  print_r($_POST);
  echo '<b style=color:red>Belum ada handler untuk Manage Mapel POST diatas.</b></pre>';
  exit;
}
