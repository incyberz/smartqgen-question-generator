<?php
$config = [];
if ($user['role'] >= 2) {
  $s = "SELECT * FROM tb_config WHERE ortu='$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $config = mysqli_fetch_assoc($q);
  if (!mysqli_num_rows($q)) {
    # ============================================================
    # AUTO CREATE CONFIG
    # ============================================================
    mysqli_query($cn, "INSERT INTO tb_config (ortu) VALUES ('$username')") or die(mysqli_error($cn));
  }
} elseif ($user['role'] == 1) {
  if (isset($kelas['username_ortu']) and $kelas['username_ortu']) {
    $s = "SELECT * FROM tb_config WHERE ortu='$kelas[username_ortu]'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $config = mysqli_fetch_assoc($q);
  }
}
