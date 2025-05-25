<?php
session_start();
$id_pengunjung = $_SESSION['qgen_id_pengunjung'] ?? null;
$username = $_SESSION['qgen_username'] ?? null;
if ($username) die("Sedang login dengan username [$username]");

$username = $_GET['username'] ?? die('undefined index [username]');
$password = $_GET['password'] ?? die('undefined index [password]');

$username = preg_replace('/^a-z/', '', $username);
if (!$username) die('empty or invalid [username]');

include '../conn.php';
$s = "SELECT * FROM tb_user WHERE username='$username' 
AND (password=md5('$password') OR password is null)
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  die('Username atau password tidak tepat.');
} else {
  $d = mysqli_fetch_assoc($q);

  $_SESSION['qgen_id_pengunjung'] = $d['id_pengunjung'];
  $_SESSION['qgen_username'] = $d['username'];
  die('OK');
}
