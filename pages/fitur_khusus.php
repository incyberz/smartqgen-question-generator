<?php
$sebagai = str_replace(' ', '_', strtolower($user['sebagai']));
$fitur = "<div class='wadah gradasi-merah red'>Undefined Fitur untuk [$sebagai]</div>";
$f = "pages/fitur_khusus--$sebagai.php";
if (file_exists($f)) include $f;

$fitur_khusus = "
  <div class=''>
    <h3 class='hideit'>Fitur Khusus $user[sebagai]</h3>
    $fitur
  </div>
";
