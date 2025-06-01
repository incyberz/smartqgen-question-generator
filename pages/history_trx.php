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
  while ($d = mysqli_fetch_assoc($q)) {
    $class = $rjenis[$d['jenis']]['class'];
    $title = $rjenis[$d['jenis']]['title'];
    $format_nominal = number_format($d['nominal']);
    $nasabah = ucwords(strtolower($d['nasabah']));
    $tanggal = tanggal($d['tanggal']);
    // $csaldo += $d['jenis'] == 'd' ? $d['nominal'] : -$d['nominal'];

    if ($d['jenis'] == 'd') {
      $csaldo += $d['nominal'];
    } else {
      $csaldo -= $d['nominal'];
    }

    $format_csaldo = number_format($csaldo);
    $tr_history = "
      <tr class='$class'>
        <td>
          <div class=flexy>
            <div>$no.</div>
            <div>
              $title Rp $format_nominal
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
<div class='border-bottom pb4'>
  <h2 class='tengah f20 pt4 mt4'>History Transaksi</h2>
  <table class=table>
    <thead>
      <th>Debet / Kredit</th>
      <th>Saldo</th>
    </thead>
    $tr_history
  </table>
</div>
";
