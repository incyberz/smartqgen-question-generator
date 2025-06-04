<?php
if (isset($_POST['btn_login'])) {
  $username = preg_replace('/^a-z0-9/', '', $_POST['username'] ?? '');
  $password = strip_tags($_POST['password'] ?? '');

  if ($username and $password) {
    $OR_pass_null = $username == $password ? 'OR password is null' : '';
    $s = "SELECT * FROM tb_user WHERE username='$username' 
  AND (password=md5('$password') $OR_pass_null)
  ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q)) {
      $pesan_login = 'Username atau password tidak tepat.';
    } else {
      $d = mysqli_fetch_assoc($q);
      $_SESSION['qgen_id_pengunjung'] = $d['id_pengunjung'];
      $_SESSION['qgen_username'] = $d['username'];
      jsurl('?dashboard');
    }
  } else {
    $pesan_login = "Username atau password invalid.";
  }
}
