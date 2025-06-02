<?php
$s = "SELECT a.*,
b.nama as nasabah 
FROM tb_trx a 
JOIN tb_user b ON a.username=b.username
WHERE ((a.jenis = 'k' AND a.approv_date is not null) || a.jenis = 'd') 
AND a.ortu = '$username'
ORDER BY a.tanggal ASC
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_trx = mysqli_num_rows($q);
$tr_history = '';

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

if ($jumlah_trx) {
  $csaldo = 0;
  $no = $jumlah_trx;
  $i = 0;

  $s2 = "SELECT * FROM tb_tmp WHERE username = '$username'";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $tmp = mysqli_fetch_assoc($q2);

  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $class = $rjenis[$d['jenis']]['class'];
    $title = $rjenis[$d['jenis']]['title'];
    $format_nominal = number_format($d['nominal']);
    $nasabah = ucwords(strtolower($d['nasabah']));
    $tanggal = tanggal($d['tanggal']);

    if ($d['jenis'] == 'd') {
      $csaldo += $d['nominal'];
    } else {
      $csaldo -= $d['nominal'];
    }
    $format_csaldo = number_format($csaldo);



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
  # ============================================================
  # SAVE SESSION REAL SALDO
  # ============================================================
  $_SESSION['qgen_saldo'] = $csaldo;

  # ============================================================
  # UPDATE TMP SALDO
  # ============================================================
  mysqli_query($cn, "UPDATE tb_tmp SET saldo=$csaldo, last_update = NOW() WHERE username='$username'") or die(mysqli_error($cn));
}




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
