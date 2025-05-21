<?php
session_start();
// session_destroy();
// exit;

# ============================================================
# SESSION
# ============================================================
$id_pengunjung = $_SESSION['qgen_id_pengunjung'] ?? null;

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
$param = $param ?? 'welcome';


# ============================================================
# CONN & CONFIGIRATION FILE
# ============================================================
include 'conn.php';
// include 'global_vars.php';

$dotdot = $is_live ? '.' : '..';
# ============================================================
# INCLUDES
# ============================================================
include 'includes/alert.php';
include 'includes/insho_styles.php';
include 'includes/img_icon.php';
include 'includes/jsurl.php';
include 'includes/set_h2.php';


# ============================================================
# USER DATA
# ============================================================
$pengunjung = [];
$user = [];
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
  <?php include 'includes/insho_styles.php'; ?>
  <script src="assets/js/jquery.js"></script>
</head>

<body>
  <div class="container">
    <?php include "pages/$param.php"; ?>
  </div>
</body>

</html>
<?php
include 'includes/script_btn_aksi.php';
