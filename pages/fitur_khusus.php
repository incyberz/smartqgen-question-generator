<?php
$fitur = "<div class='wadah gradasi-merah red'>Undefined Fitur untuk $user[sebagai]</div>";

$f = "pages/fitur_khusus--$user[role].php";
if (file_exists($f)) include $f;

$fitur_khusus = "
  <div class='wadah'>
    <h3>Fitur Khusus $user[sebagai]</h3>
    $fitur
  </div>
";
