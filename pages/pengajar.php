<?php
$get_aksi = $_GET['aksi'] ?? '';
$get_role = $_GET['role'] ?? '';
if ($get_aksi == 'switch' and $get_role) {
  $_SESSION['qgen_role'] = $get_role;
  jsurl('?');
}

if ($user['role'] == 2 and $user['is_pengajar']) {
  # ============================================================
  # SWITCH SESSION TO PENGAJAR
  # ============================================================
  $_SESSION['qgen_role'] = 3;
  $user['role'] = 3;
  jsurl();
}

# ============================================================
# UI ROLE PENGAJAR
# ============================================================
$switch_role = "<a class='f12 mb2 hover' href=?pengajar&aksi=switch&role=2><button>Switch to Role Orangtua 👨‍🏫</button></a>";
echo "
  <div class=ortu>
  <h2>Pengajar</h2>
  $switch_role
";
?>
<div class='border-top mt4 pt4 mb2'>
  Fitur Pengajar:
</div>
<a class='btn btn-primary' href=?manage_mapel>Manage Mapel</a>
<a class='btn btn-primary' href=?manage_materi>Manage Materi</a>


<?php echo '</div>';
