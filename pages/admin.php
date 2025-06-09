<?php
$get_aksi = $_GET['aksi'] ?? '';
$get_role = $_GET['role'] ?? '';
if ($get_aksi == 'switch' and $get_role) {
  $_SESSION['qgen_role'] = $get_role;
  jsurl('?');
}

if ($user['role'] < 100 and $user['is_admin']) {
  # ============================================================
  # SWITCH SESSION TO ADMIN
  # ============================================================
  $_SESSION['qgen_role'] = 100;
  $user['role'] = 100;
  jsurl();
}

# ============================================================
# UI ROLE ADMIN
# ============================================================
echo "
  <div class=ortu>
  <h2>Admin</h2>
  <a class='f12 mb2 hover' href=?pengajar&aksi=switch&role=2><button>Switch to Role Orangtua 👨‍🏫</button></a>
";
?>
<div class='border-top mt4 pt4 mb2'>
  Fitur Admin:
</div>
<a class='btn btn-primary' href=?manage_jenjang>Manage Jenjang</a>
<a class='btn btn-primary' href=?manage_mapel>Manage Mapel</a>
<a class='btn btn-primary' href=?manage_materi>Manage Materi</a>
<a class='btn btn-primary' href=?manage_paket>Manage Paket</a>


<?php echo '</div>';
