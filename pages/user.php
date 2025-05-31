<?php
if ($id_pengunjung) {
  if ($username) { // sedang login
    $s = "SELECT a.*,
    b.sebagai,
    c.id as id_pengunjung,
    c.created_at,
    (SELECT posisi_ortu FROM tb_ortu WHERE username=a.username) posisi_ortu, 
    (SELECT COUNT(1) FROM tb_kelas WHERE username=a.username) jumlah_kelas 
    FROM tb_user a 
    JOIN tb_role b ON a.role=b.id
    JOIN tb_pengunjung c ON a.id_pengunjung=c.id
    WHERE a.username = '$username'
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $user = mysqli_fetch_assoc($q);
    $pengunjung = $user;
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
  }
  $user['role'] = $user['role'] ?? null;
  $_SESSION['qgen_role'] = $user['role'];
  $user['nama'] = $user['nama'] ?? '';
  $user['nama'] = ucwords(strtolower($user['nama']));
}
