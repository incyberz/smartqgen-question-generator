<?php
akses('approve_pencairan');
set_title('Approve Pencairan');
$id_trx = $_GET['id_trx'] ?? kosong('id_trx');


include 'user-process.php';

$s = "SELECT a.*,
b.nama as nasabah,
b.id_pengunjung as id_anak,
c.saldo,
d.poin as total_poin 
FROM tb_trx a 
JOIN tb_user b ON a.username=b.username 
JOIN tb_tmp c ON a.ortu=c.username 
JOIN tb_tmp d ON a.username=d.username 
WHERE a.id=$id_trx 
AND a.ortu='$username' -- milik sendiri
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  stop('Data Transaksi tidak ditemukan.');
} else {
  $trx = mysqli_fetch_assoc($q);
}

# ============================================================
# HISTORY BELAJAR
# ============================================================
$limit = 5;
$s = "SELECT 
a.* 
FROM tb_paket_jawaban a 
WHERE a.id_pengunjung = $trx[id_anak] 
-- AND status < 100 -- belum di claim 
ORDER BY waktu_submit DESC 
LIMIT $limit
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  stop('History Kuis tidak boleh null dalam pencairan LP');
} else {
  $tr_history = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $tgl = tanggal($d['waktu_submit']);
    $jam = date('H:i', strtotime($d['waktu_submit']));

    // $status = $d['status'] == 100 ? "✅<i class='f12'>Claimed $d[poin] LP</i>" : "⚠️<i class='f12 yellow'>unclaim</i>";

    $tr_history .= "
      <tr>
        <td>
          <div>$tgl <i class='f12 abu'>$jam</i></div>
        </td>
        <td >
          <div><b class='f14'>Nilai</b>: $d[nilai] ~ <i class='f12 abu'>$d[poin] LP</i></div>
        </td>
      </tr>
    ";
  }
}

$nasabah = ucwords(strtolower($trx['nasabah']));
$format_nominal = number_format($trx['nominal']);
$format_saldo = number_format($trx['saldo']);
$format_total_poin = number_format($trx['total_poin']);

echo "
  <form method=post class='w-500 ortu'>
    <h2 class='tengah f20 pt4 mt4'>💸 Approve Pencairan</h2>
    <div>
      $nasabah dengan total poin <span class='darkblue'>$format_total_poin LP</span> meminta pencairan sebesar: 
      <div class='border-top border-bottom pt2 pb2 kuning f30 mt2 mb2'>Rp $format_nominal</div>
      Saldo tersedia: <span class=kuning>Rp $format_saldo</span>
    </div>
    
    <h4 class='border-top pt2 mt3'>History Belajar</h4>
    <table class=''>
      $tr_history
    </table>

    <div class='mt3 border-top pt2'>
      <label>
        <input type=checkbox required> Saya telah menyediakan uangnya
      </label>
      <button class='btn btn-primary w-100 mt3' name=btn_approve_pencairan value=$id_trx>Approve Pencairan</button>
    </div>
  </form>
";
