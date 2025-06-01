<?php
include 'includes/key2kolom.php';

$target_username = $_GET['username'] ?? $username;
if (!$target_username) stop('Target username is null');

$s = "SELECT *
FROM tb_user a 
JOIN tb_tmp b ON a.username=b.username 
WHERE a.username = '$target_username'
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    foreach ($d as $key => $value) {
      if ($value === null || (
        $key == 'id'
        || $key == 'id_pengunjung'
        || $key == 'created_at'
        || $key == 'last_update'
        || $key == 'role'
        || $key == 'password'
      )) {
        continue;
      } elseif ($key == 'whatsapp' || $key == 'image') {
        $value = substr($value, 0, 3) . '...' . substr($value, -4);
      }

      $kolom = key2kolom($key);
      $tr .= "
        <tr>
          <td>$kolom</td>
          <td>$value</td>
        </tr>
      ";
    }
  }
}

echo "
  <div class='w-500'>
  <h2>Detail Peserta</h2>
  <a href=?>👈 Back Home</a>
  <table class='table mt3'>
    $tr
  </table>
  </div>
";
