<?php
function akses($fitur)
{
  if (!$fitur) return false;
  # ============================================================
  # ROLES
  # ============================================================
  # 	1 	Pelajar
  # 	2 	Orangtua
  # 	3 	Staf Pengajar
  # 	4 	Tenaga Pendidikan
  # 	5 	Akademik
  # 	6 	Supervisor
  # 	100 	Admin System
  $fiturs = [
    'dashboard' => [1, 2],
    'pencairan' => [1],
    'manage_kelas' => [2, 3, 4, 5, 100],
    'manage_trx' => [2, 3],
    'top_up_saldo' => [2, 3, 4, 5, 100],
    'drop_peserta_kelas' => [2, 3],
    'approve_pencairan' => [2, 3],
    'konfigurasi_pencairan' => [2, 3],
    'manage_mapel' => [3, 100],
    'add_materi' => [3, 100],
    'manage_materi' => [3, 100],
    'manage_soal' => [3, 100],
    'update_kelas_materi' => [3, 100],
  ];
  if (key_exists($fitur, $fiturs)) {
    only($fiturs[$fitur]);
  } else {
    die("Fitur [$fitur] belum didefinisikan pada Fungsi Akses.");
  }
}

require_once 'includes/only.php';
