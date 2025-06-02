<link rel="stylesheet" href="./assets/css/progress.css">
<?php
$user_src = "assets/img/user/$user[image]";
$src = ($user['image'] and file_exists($user_src)) ? $user_src : $default_src;

include 'tmp_data.php';

$s = "SELECT 
  level as next_level,  
  min_poin as next_poin
FROM tb_level 
WHERE min_poin >= $tmp[poin] LIMIT 1
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$next_level = $d['next_level'];
$next_poin = $d['next_poin'];
$level = $next_level - 1;

$s = "SELECT min_poin as awal_poin FROM tb_level WHERE level=$level";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$awal_poin = $d['awal_poin'];

$persen = round(($tmp['poin'] - $awal_poin) * 100 / ($next_poin - $awal_poin), 2);
$unfill_poin = ($next_poin - $awal_poin) - ($tmp['poin'] - $awal_poin);





$fitur = "
  <div class=ortu>
    <div><img src=$src class=foto_profil></div>
    <div>Level: <span id=level class=f30>$level</span></div>
    <div class=progress>
      <div class=progress-bar style=width:$persen%></div>
    </div>
    <div class='mt1 f14'>$unfill_poin LP to Next Level</div>
  </div>
";
