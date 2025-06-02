<?php
$s = "SELECT a.*,
(SELECT poin FROM tb_tmp WHERE username=a.username) tmp_poin,
(SELECT saldo FROM tb_tmp WHERE username=a.ortu) tmp_saldo,
b.nama as nasabah  
FROM tb_trx a 
JOIN tb_user b ON a.username=b.username
WHERE a.jenis = 'k'  
AND a.ortu = '$username' 
AND a.approv_date is null
ORDER BY a.tanggal ASC
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_request_pencairan = mysqli_num_rows($q);
$request_pencairan = '';
if ($param == 'manage_trx') {

  $tr_pencairan = '';

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

  if ($jumlah_request_pencairan) {
    $no = $jumlah_request_pencairan;
    $format_saldo = 0;
    while ($d = mysqli_fetch_assoc($q)) {
      $class = $rjenis[$d['jenis']]['class'];
      $title = $rjenis[$d['jenis']]['title'];
      $format_nominal = number_format($d['nominal']);
      $nasabah = ucwords(strtolower($d['nasabah']));
      $tanggal = tanggal($d['tanggal']);
      $format_poin = number_format($d['tmp_poin']);
      $format_saldo = number_format($d['tmp_saldo']);
      $tr_pencairan = "
        <tr class=request-pencairan>
          <td>
            <div class=flexy>
              <div>$no.</div>
              <div>
                Dari $nasabah
                <div class='f12'>$tanggal</div> 
                <div class='f12'><b>Poin</b>: $format_poin LP</div> 
              </div>
            </div>
          </td>
          <td>
            Rp $format_nominal
            <div class='mt1'><a href=?approve_pencairan&id_trx=$d[id] class='btn btn-primary btn-sm'>Approve</a></div>
          </td>
        </tr>
        $tr_pencairan
      ";
      $no--;
    }
    $request_pencairan = "
    <div class='border-bottom pb4'>
      <h2 class='tengah f20 pt4 mt4 darkred'>👆Request Pencairan</h2>
      <b>Saldo</b>: $format_saldo
      <table class=table>
        $tr_pencairan
      </table>
    </div>
    ";
  }
}
