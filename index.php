<?php
session_start();
// session_destroy();
// exit;

# ============================================================
# SESSION
# ============================================================
$username = $_SESSION['qgen_username'] ?? null;
$id_pengunjung = $_SESSION['qgen_id_pengunjung'] ?? null;
// $role = $_SESSION['qgen_role'] ?? null;


// ZZZ
// $_SESSION['qgen_username'] = 'admin';
// $_SESSION['qgen_role'] = 'admin';

# ============================================================
# PETUGAS DEFAULT
# ============================================================
// $petugas_default = [
//   'nama' => 'Dasep Solehuddin',
//   'whatsapp' => '6287729007318',
// ];

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
# CONFIGIRATION FILE
# ============================================================
// include 'config.php';
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
// $user = [];
// $nama_user = '';
// $is_adm = false;
// include 'pages/user.php';


$pengunjung = [];
$nama_pengunjung = null;
if ($id_pengunjung) {
  $s = "SELECT * FROM tb_pengunjung ORDER BY created_at DESC LIMIT 1";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $pengunjung = mysqli_fetch_assoc($q);
  $id_pengunjung = $pengunjung['id'];
  $nama_pengunjung = $pengunjung['nama'];
}

$debug_id_pengunjung = "<i class=red>id: $id_pengunjung</i>";
$debug_nama_pengunjung = "<i class=red>nama: $nama_pengunjung</i>";



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
    <?php if ($id_pengunjung) include 'btn_logout.php'; ?>
    <?php include "$param.php"; ?>
    <?php
    // echo '<pre>SESSION: ';
    // print_r($_SESSION);
    // echo '</pre>';
    // echo '<pre>PENGUNJUNG: ';
    // print_r($pengunjung);
    // echo '</pre>';

    ?>
  </div>
</body>

</html>
<?php
include 'includes/script_btn_aksi.php';
