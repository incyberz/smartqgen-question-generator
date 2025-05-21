<?php
unset($_SESSION['mulai_mengerjakan']);
unset($_SESSION['nomor_soal']);
unset($_SESSION['id_soals']);
unset($_SESSION['qgen_id_pengunjung']);
if ($_SESSION) {
  echo '<pre>';
  print_r($_SESSION);
  echo '<b style=color:red>DEBUGING: masih ada data SESSION yang belum clear</b></pre>';
  jsurl('?', 3000);
}
jsurl('?');
