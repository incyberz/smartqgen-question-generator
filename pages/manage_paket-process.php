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
} elseif (isset($_POST['btn_add_paket_mapel'])) {
  $t = explode('--', $_POST['btn_add_paket_mapel']);
  $id_mapel = $t[0] ?? kosong('id_mapel at btn_add_paket_mapel');
  $jenjang = $t[1] ?? kosong('jenjang at btn_add_paket_mapel');

  $nama_paket = preg_replace('/[^a-zA-Z0-9 .-]/', '', $_POST['nama_paket']);
  $singkatan = preg_replace('/[^a-zA-Z0-9-]/', '', $_POST['singkatan']);

  if ($id_mapel and $jenjang and $nama_paket and $singkatan) {
    $s = "INSERT INTO tb_paket_soal (
      nama_paket,
      singkatan,
      created_by,
      id_mapel,
      jenjang
    ) VALUES (
      '$nama_paket',
      '$singkatan',
      '$username',
      '$id_mapel',
      '$jenjang'
    )";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    jsurl();
  } else {
    stop("Insert gagal.<hr>
      id_mapel: $id_mapel<br>
      jenjang: $jenjang<br>
      nama_paket: $nama_paket<br>
      singkatan: $singkatan<br>
    ");
  }
} elseif (isset($_POST) and $_POST) {
  echo '<pre>';
  print_r($_POST);
  echo '<b style=color:red>Belum ada handler untuk Manage Paket dari POST diatas.</b></pre>';
  exit;
}
