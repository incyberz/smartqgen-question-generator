<?php
if ($user['role'] == 1) {
  $sql_status_jawaban = $username ? 'status=100' : '1';
  $s_tmp = "SELECT * FROM tb_paket_jawaban a 
  WHERE a.id_pengunjung = $id_pengunjung 
  AND $sql_status_jawaban -- status = 100 -- sudah di claim | pengunjung belum claim
  ORDER BY a.waktu_submit DESC
  ";
  $q_tmp = mysqli_query($cn, $s_tmp) or die(mysqli_error($cn));
  $total_poin = 0;
  $total_nilai = 0;
  $total_paket = 0;
  $waktu_submit = '';
  while ($d_tmp = mysqli_fetch_assoc($q_tmp)) {
    $total_poin += $d_tmp['poin'];
    $total_nilai += $d_tmp['nilai'];
    $total_paket++;
    $waktu_submit = $d_tmp['waktu_submit'];
  }
  $rnilai = round($total_nilai / $total_paket);
  $d_tmp = mysqli_fetch_assoc($q_tmp);

  # ============================================================
  # HITUNG LEVEL
  # ============================================================
  $s_tmp = "SELECT 
    a.level as next_level,  
    a.level - 1 as level,  
    a.min_poin as next_poin,
    (SELECT min_poin as awal_poin FROM tb_level_poin WHERE level=(a.level-1)) awal_poin
  FROM tb_level_poin a 
  WHERE a.min_poin >= $total_poin LIMIT 1
  ";
  $q_tmp = mysqli_query($cn, $s_tmp) or die(mysqli_error($cn));
  $d_tmp = mysqli_fetch_assoc($q_tmp);

  # ============================================================
  # FINAL UPDATE
  # ============================================================
  $s_tmp = "UPDATE tb_tmp SET 
    level = $d_tmp[level],
    awal_poin = $d_tmp[awal_poin],
    next_poin = $d_tmp[next_poin],
    poin = $total_poin, 
    nilai = $rnilai, 
    last_play = '$waktu_submit',
    last_update = NOW() 
  WHERE username='$username'";
  $q_tmp = mysqli_query($cn, $s_tmp) or die(mysqli_error($cn));
} else {
  stop('Update tmp hanya untuk pelajar.');
}
