<?php
if (isset($_POST['btn_cairkan'])) {
  $nominal = intval($_POST['nominal']);
  $ortu = $kelas['username_ortu'] ?? kosong('username_ortu pada kelas');
  if ($nominal and $ortu) {
    // tidak perlu recheck saldo di sisi pelajar
    // insert into tb_trx
    $s = "INSERT INTO tb_trx (
      username,
      jenis,
      nominal,
      ortu
    ) VALUES (
      '$username',
      'k',
      $nominal,
      '$ortu'
    )";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  jsurl();
} elseif (isset($_POST['btn_sudah_terima_uang'])) {
  $id = intval($_POST['btn_sudah_terima_uang']);
  if ($id) {
    $s = "UPDATE tb_trx SET take_date = NOW() WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  jsurl();
} elseif (isset($_POST['btn_batal_pencairan'])) {
  $id = intval($_POST['btn_batal_pencairan']);
  if ($id) {
    $s = "DELETE FROM tb_trx WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  jsurl();
} elseif ($_POST) {
  echo '<pre>';
  print_r($_POST);
  echo "<b style=color:red>Belum ada handler @pencairan untuk data POST diatas.</b></pre>";
  exit;
}
