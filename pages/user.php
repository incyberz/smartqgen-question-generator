<?php
if ($id_pengunjung) {
  if ($username) { // sedang login
    $s = "SELECT a.*,
    b.sebagai,
    c.id as id_pengunjung,
    c.created_at,
    d.*,
    d.poin - d.sum_pencairan as sisa_poin,
    (SELECT 1 FROM tb_pengajar WHERE username=a.username) is_pengajar, 
    (SELECT 1 FROM tb_admin WHERE username=a.username) is_admin, 
    (SELECT posisi_ortu FROM tb_ortu WHERE username=a.username) posisi_ortu, 
    (SELECT COUNT(1) FROM tb_kelas WHERE username=a.username) jumlah_kelas 
    FROM tb_user a 
    JOIN tb_role b ON a.role=b.id
    JOIN tb_pengunjung c ON a.id_pengunjung=c.id
    JOIN tb_tmp d ON a.username=d.username
    WHERE a.username = '$username'
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $user = mysqli_fetch_assoc($q);
    $pengunjung = $user;

    if (!$user) {
      # ============================================================
      # AUTO-INSERT DATA TMP (FIRST LOGIN ONLY)
      # ============================================================
      mysqli_query($cn, "INSERT INTO tb_tmp (username) VALUES ('$username')") or die(mysqli_error($cn));
      alert('updating user data...', 'info');
      jsurl('', 3000);
    }

    # ============================================================
    # DATA KELAS DAN ORTU KHUSUS PELAJAR
    # ============================================================
    $kelas = [];
    if ($user['role'] == 1) {
      $s = "SELECT 
      a.id as id_peserta,
      b.id,
      b.nama_kelas,
      b.username as username_ortu, 
      c.nama as nama_ortu,
      d.saldo,
      f.posisi_ortu 
      FROM tb_peserta a 
      JOIN tb_kelas b ON a.id_kelas=b.id 
      JOIN tb_user c ON b.username=c.username 
      JOIN tb_tmp d ON c.username=d.username 
      JOIN tb_ortu e ON c.username=e.username 
      JOIN tb_posisi_ortu f ON e.posisi_ortu=f.id 
      WHERE a.username='$username'";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      $kelas = mysqli_fetch_assoc($q);
    } elseif ($user['role'] > 1) {
      $pengajar = [];
      if ($user['is_pengajar']) {
        $s = "SELECT * FROM tb_pengajar a WHERE a.username='$username'";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        $pengajar = mysqli_fetch_assoc($q);
      }
    }

    # ============================================================
    # CAPTION Papa, Mama, Kakek, dll
    # ============================================================
    $Papa = $kelas ? $kelas['posisi_ortu'] : 'Ortu';

    # ============================================================
    # GENDER ICON 
    # ============================================================
    $gender_icon = gender_icon($user['gender'], $user['role']);
  } else { // belum login | baru register
    $s = "SELECT *,
    (SELECT username FROM tb_user WHERE id_pengunjung=a.id) username 
    FROM tb_pengunjung a 
    WHERE id = $id_pengunjung
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $pengunjung = mysqli_fetch_assoc($q);
    $id_pengunjung = $pengunjung['id'];

    if ($pengunjung['username']) {
      $s = "SELECT * 
      FROM tb_user a 
      JOIN tb_role b ON a.role=b.id
      WHERE a.username = '$pengunjung[username]'
      ";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      $user = mysqli_fetch_assoc($q);
      $_SESSION['qgen_username'] = $user['username'];
    }
  } // end if !$username
  $user['nama'] = $user['nama'] ?? '';
  $user['nama'] = ucwords(strtolower($user['nama']));
} // end if $id_pengunjung

# ============================================================
# ROLES
# ============================================================
$user['role'] = $user['role'] ?? null;

# ============================================================
# ORTU BOLEH PINDAH ROLE KE PENGAJAR
# ============================================================
if (!isset($_SESSION['qgen_role'])) {
  $_SESSION['qgen_role'] = $user['role'];
} else { // set role from SESSION
  $user['role'] = $_SESSION['qgen_role'];
}
