<?php
# ===============================
# Ambil Data Paket
# ===============================
$s = "SELECT 
a.* 
FROM tb_paket_jawaban a 
WHERE a.id_pengunjung = $id_pengunjung 
-- AND status < 100 -- belum di claim 
ORDER BY waktu_submit DESC
$limit
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

if (!mysqli_num_rows($q)) {
  // die('Tidak ada Paket Kuis untuk pengunjung ini.');
} else {

  $num_rows = mysqli_num_rows($q);
  $unclaim_no = 0;
  $last_kuis_no = 0;
  $tr_unclaim = '';
  $tr_last_kuis = '';

  while ($d = mysqli_fetch_assoc($q)) {
    # ============================================================
    # AUTO DELETE IF STATUS PAKET IS NULL
    # ============================================================
    if (!$d['status']) {
      $s2 = "DELETE FROM tb_paket_jawaban WHERE id=$d[id]";
      mysqli_query($cn, $s2) or die(mysqli_error($cn));
    }

    # ============================================================
    # LOOP BIASA | VALID ONLY
    # ============================================================
    $tgl = tanggal($d['waktu_submit']);
    $jam = date('H:i', strtotime($d['waktu_submit']));

    if ($d['status'] == 100) {
      $last_kuis_no++;
      $tr_last_kuis .= "
        <tr>
          <td>
            <div>$tgl <i class='f12 abu'>$jam</i></div>
            <div><b class='f14'>Nilai</b>: $d[nilai] ~ <i class='f12 abu'>$d[poin] LP</i></div>
          </td>
          <td >
            <div>✅<i class='f12'>Claimed</i></div>
          </td>
        </tr>
      ";
    } else {
      $unclaim_no++;
      $tr_unclaim .= "
        <tr>
          <td>
            <div>$tgl <i class='f12 abu'>$jam</i></div>
            <div><b class='f14'>Nilai</b>: $d[nilai] ~ <i class='f12 abu'>$d[poin] LP</i></div>
          </td>
          <td >
            <div>⚠️<i class='f12 yellow'>unclaim</i></div>
            <button name=btn_claim_poin value=$d[id] class='transparan f12 yellow hover' onclick='return confirm(`Claim $d[poin] LP?`)'>Claim XP</button> 
             | 
            <button name=btn_hapus_poin value=$d[id] class='transparan f12 yellow hover' onclick='return confirm(`Hapus poin?`)' >Hapus</button> 
          </td>
        </tr>
      ";
    }
  }
  if ($tr_unclaim) {
    $my_paket_kuis = "
      <h2 class='tengah f20 pt4 mt4 border-top'>🏅 Unclaim Points</h2>
      <p>Jika kamu claim maka Total Poin kamu bertambah dan data masuk ke History Kuis</p>
      <form method=post>
        <table id=tb-hasil-quiz>
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            $tr_unclaim
          </tbody>
        </table>
      </form>
    ";
  } else {
    $my_paket_kuis = "
      $claimed_title
      <form method=post>
        <table id=tb-hasil-quiz>
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            $tr_last_kuis
          </tbody>
        </table>
      </form>
    ";
  }
}
