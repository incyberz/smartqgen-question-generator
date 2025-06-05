<?php
if (!$username) stop('user process hanya untuk logged user');
if (isset($_POST['btn_claim_poin'])) {
  $s = "UPDATE tb_jawaban SET archived=1 WHERE id_paket = $_POST[btn_claim_poin]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $s = "UPDATE tb_paket_jawaban SET status=100 WHERE id = $_POST[btn_claim_poin]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  include 'update_tmp.php';
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
} elseif (isset($_POST['btn_simpan_konfigurasi'])) {
  include 'config_default.php';

  $s = "DESCRIBE tb_config";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $Desc = [];
  while ($d = mysqli_fetch_assoc($q))  $Desc[$d['Field']] = $d;
  $pairs = '';
  $values = '';
  foreach ($Desc as $k => $v) {
    if ($k == 'ortu') continue;
    $koma = $pairs ? ',' : '';
    $value = $_POST[$k] == $config_default[$k]['value'] ? 'NULL' : intval($_POST[$k]);
    $pairs .= "$koma$k=$value";
  }

  $s = "UPDATE tb_config SET $pairs WHERE ortu = '$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
} elseif (isset($_POST['nominal-top-up'])) {
  $nominal = intval($_POST['nominal-top-up']);
  if ($nominal) {
    $s = "INSERT INTO tb_trx (
      username,
      jenis,
      nominal,
      ortu
    ) VALUES (
      '$username',
      'd',
      '$nominal',
      '$username'
    )";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

    $s = "UPDATE tb_tmp SET saldo = saldo + $nominal WHERE username = '$username'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  jsurl();
} elseif (isset($_POST['btn_batal_topup'])) {
  $id = intval($_POST['btn_batal_topup']);
  if ($id) {
    $s = "SELECT nominal FROM tb_trx WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $d = mysqli_fetch_assoc($q);
    mysqli_query($cn, "DELETE FROM tb_trx WHERE id=$id") or die(mysqli_error($cn));
    mysqli_query($cn, "UPDATE tb_tmp SET saldo = saldo - $d[nominal] WHERE username = '$username'") or die(mysqli_error($cn));
  }
  jsurl();
} elseif (isset($_POST['btn_approve_pencairan'])) {
  $id = intval($_POST['btn_approve_pencairan']);
  if ($id) {
    $s = "SELECT * FROM tb_trx WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $trx = mysqli_fetch_assoc($q);

    # ============================================================
    # RECHECK SALDO ORTU
    # ============================================================
    $real_saldo = 0;
    include 'update_saldo.php'; // update csaldo pada history

    if ($real_saldo < $trx['nominal']) {
      alert("Maaf, saldo tidak mencukupi.<hr class='mt3 mb3'>Saldo: $real_saldo, request Rp $trx[nominal]");
    } else {
      alert("Processing Pencairan...", 'info');
      mysqli_query($cn, "UPDATE tb_trx SET approv_date = NOW() WHERE id=$id") or die(mysqli_error($cn));
      mysqli_query($cn, "UPDATE tb_tmp SET saldo = saldo - $trx[nominal] WHERE username = '$username'") or die(mysqli_error($cn));
    }
  }
  jsurl('?', 3000);
} elseif (isset($_POST['btn_sudah_terima_uang'])) {
  $id = intval($_POST['btn_sudah_terima_uang']);
  if ($id) {
    $s = "UPDATE tb_trx SET take_date = NOW() WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  jsurl();
} elseif ($_POST) {

  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
  stop('Belum ada handler untuk data POST diatas. Hubungi Developer!');
  exit;
}
