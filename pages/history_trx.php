<?php
// cek saldo only (fast_mode) atau plus UI (normal_mode)
$fast_mode = (isset($update_saldo) and $update_saldo) ? 1 : 0;

# ============================================================
# TARGET USERNAME SELF JIKA ORTU
# ============================================================
$target_username = $user['role'] == 1 ? ($kelas['username_ortu'] ?? '') : $username;

$s = "SELECT a.*,
b.nama as nasabah 
FROM tb_trx a 
JOIN tb_user b ON a.username=b.username
WHERE ((a.jenis = 'k' AND a.approv_date is not null) || a.jenis = 'd') 
AND a.ortu = '$target_username'
ORDER BY a.tanggal ASC
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_trx = mysqli_num_rows($q);
$tr_history = '';

if ($jumlah_trx) {
  $rjenis = [
    'd' => [
      'title' => 'Top Up Saldo',
      'class' => 'debet',
    ],
    'k' => [
      'title' => 'Pencairan',
      'class' => 'kredit',
    ],
  ];

  $real_saldo = 0;
  $no = $jumlah_trx;
  $i = 0;


  if (!isset($tmp)) {
    $s2 = "SELECT * FROM tb_tmp WHERE username = '$username'";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $tmp = mysqli_fetch_assoc($q2);
  }

  while ($d = mysqli_fetch_assoc($q)) {
    # ============================================================
    # LOOP UPDATE SALDO
    # ============================================================
    if ($d['jenis'] == 'd') {
      $real_saldo += $d['nominal'];
    } else {
      $real_saldo -= $d['nominal'];
    }
    $format_csaldo = number_format($real_saldo);

    # ============================================================
    # UI HANYA DIJALANKAN SAAT NORMAL MODE
    # ============================================================
    if (!$fast_mode) {
      $i++;
      $class = $rjenis[$d['jenis']]['class'];
      $title = $rjenis[$d['jenis']]['title'];
      $format_nominal = number_format($d['nominal']);
      $nasabah = ucwords(strtolower($d['nasabah']));
      $tanggal = tanggal($d['tanggal']);




      # ============================================================
      # AVAILABLE DELETE | ABORT TOP UP
      # ============================================================
      $btn_batal_topup = "<span class='hover' onclick='alert(`Topup tidak bisa dibatalkan karena trx sudah lebih dari satu jam.`)'>$img_delete_disabled</span>";
      $selisih = strtotime('now') - strtotime($d['tanggal']);
      if (($selisih < 3600) and $tmp['saldo'] > $d['nominal'] and $no == 1 and $d['jenis'] == 'd') {
        $btn_batal_topup = "<button class=transparan value=$d[id] name=btn_batal_topup onclick='return confirm(`Batalkan Top Up?`)'>$img_delete</button>";
      } elseif ($d['jenis'] == 'k' || $selisih > 24 * 3600) {
        $btn_batal_topup = '';
      }

      # ============================================================
      # FINAL TR HISTORY
      # ============================================================
      $tr_history = "
            <tr class='$class'>
              <td>
                <div class=flexy>
                  <div>$no.</div>
                  <div>
                    $title Rp $format_nominal $btn_batal_topup
                    <div class='f12 abu'>$tanggal</div> 
                    <div class='f12 abu'>$nasabah</div> 
                  </div>
                </div>
              </td>
              <td class=nominal>
                $format_csaldo
              </td>
            </tr>
            $tr_history
          ";
      $no--;
    }
  } // end while

  # ============================================================
  # UPDATE TMP SALDO
  # ============================================================
  if ($tmp['saldo'] != $real_saldo || $fast_mode) {
    # ============================================================
    # SAVE SESSION REAL SALDO
    # ============================================================
    $_SESSION['qgen_saldo'] = $real_saldo;
    mysqli_query($cn, "UPDATE tb_tmp SET saldo=$real_saldo, last_update = NOW() WHERE username='$username'") or die(mysqli_error($cn));
  }
} // end if jumlah trx

if (!$fast_mode) {
  $history_trx = "
    <form method=post class='border-bottom pb4'>
      <h2 class='tengah f20 pt4 mt4'>History Transaksi</h2>
      <table class=table>
        <thead>
          <th>Debet / Kredit</th>
          <th>Saldo</th>
        </thead>
        $tr_history
      </table>
    </form>
  ";
}
