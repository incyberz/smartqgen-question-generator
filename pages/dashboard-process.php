<?php
if (isset($_POST['btn_claim_poin'])) {
  $s = "UPDATE tb_jawaban SET archived=1 WHERE id_paket = $_POST[btn_claim_poin]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $s = "UPDATE tb_paket SET status=100 WHERE id = $_POST[btn_claim_poin]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
} elseif ($_POST) {

  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
  stop('Belum ada handler untuk data POST diatas. Hubungi Developer!');
  exit;
}
