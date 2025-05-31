<?php
unset($_SESSION['qgen_id_pengunjung']);
unset($_SESSION['qgen_mulai_quiz']);
unset($_SESSION['qgen_username']);
unset($_SESSION['qgen_id_paket']);
unset($_SESSION['qgen_role']);
if ($_SESSION) {
  echo '<pre>';
  print_r($_SESSION);
  echo '<b style=color:red>DEBUGING: masih ada data SESSION yang belum clear</b></pre>';
  jsurl('?', 3000);
}
jsurl('?');
