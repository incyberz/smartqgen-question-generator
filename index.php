<?php
session_start();
// session_destroy();
// exit;

# ============================================================
# GLOBAL VARS
# ============================================================
$today = date('Y-m-d');

# ============================================================
# SESSION
# ============================================================
$id_pengunjung = $_SESSION['qgen_id_pengunjung'] ?? null;
$id_paket = $_SESSION['qgen_id_paket'] ?? null;
$username = $_SESSION['qgen_username'] ?? null;

# ============================================================
# GET PARAM
# ============================================================
$param = null;
if ($_GET) {
  foreach ($_GET as $key => $value) {
    $param = $key;
    break;
  }
}
if ($username) {
  $param = $param ?? 'dashboard';
} else {
  $param = $param ?? 'welcome';
}





# ============================================================
# CONN & USER DATA
# ============================================================
$pengunjung = [];
$user = [];
include 'conn.php';
include 'pages/user.php';
$username = $pengunjung['username'] ?? null;
$nama_pengunjung = $pengunjung['nama'] ?? null;
$nama_user = $user['nama'] ?? null;




?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartQGen - Soal Dinamis, Evaluasi Otomatis</title>
  <link rel="stylesheet" href="assets/css/dark.css">
  <link rel="stylesheet" href="assets/css/btn.css">
  <link rel="stylesheet" href="assets/css/btn-outlined.css">

  <?php
  # ============================================================
  # INCLUDES
  # ============================================================
  include 'includes/alert.php';
  include 'includes/insho_styles.php';
  include 'includes/img_icon.php';
  include 'includes/jsurl.php';
  include 'includes/set_h2.php';
  include 'includes/only.php';
  include 'includes/akses.php';

  $img_logout = img_icon('logout');

  ?>
  <script src="assets/js/jquery.js"></script>
</head>

<body>
  <div class="container">
    <span id="id_pengunjung" class="hideit"><?= $id_pengunjung ?></span>
    <?php if ($username) include 'pages/btn_logout.php'; ?>
    <?php include "pages/$param.php"; ?>
  </div>
  <?php include 'includes/script_btn_aksi.php'; ?>
</body>

</html>