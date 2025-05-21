<?php
if ($id_pengunjung) {
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
    WHERE username = '$pengunjung[username]'
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $user = mysqli_fetch_assoc($q);
  }
}
