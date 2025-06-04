<style>
  .row-paket {
    display: grid;
    grid-template-columns: 20px auto;
    gap: 5px;
  }
</style>
<?php
# ============================================================
# FREE | PAID PAKET
# ============================================================
$s = "SELECT a.*,
(SELECT nama_mapel FROM tb_mapel WHERE id=a.id_mapel) mapel, 
(SELECT COUNT(1) FROM tb_soal WHERE id_paket=a.id) jumlah_soal,
(
  SELECT paid_until FROM tb_paid 
  WHERE id_paket=a.id 
  AND pembeli = '$username'
  ) paid_until
FROM tb_paket_soal a 
WHERE 1 -- (a.harga is null OR a.harga = 0)
ORDER BY paid_until DESC
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$tr_paid = '';
$tr_free = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;

  $jumlah_soal = $d['jumlah_soal'] ?? 0;
  $Random = ($d['mapel'] || !$jumlah_soal) ? '' : 'Random, ';

  $star = $d['paid_until'] ? '🌟' : '';
  $paid_until = $d['paid_until'] ? $d['paid_until'] : '<i>unlimitted</i>';
  $green = $d['paid_until'] ? 'bold green' : 'darkabu';
  $gradasi = $d['paid_until'] ? 'hijau' : 'abu';
  $tr_free .= "
    <tr>
      <td class='gradasi-$gradasi'>
        <div class=row-paket>
          <div>$i.</div>
          <div>
            <span class='$green'>$d[nama_paket] $star</span> 
            <div class='f12 abu'>$Random $jumlah_soal soal</div> 
            <div class='f12 abu'>Min level: $d[min_level]</div> 
            <div class='f12 abu'>Valid until: $paid_until</div> 
          </div>
        </div>
      </td>
    </tr>
  ";
}


$info_paket_soal = "
  <h2 class='tengah f20 pt4 mt4 border-top'>Paket Soal (LKS)</h2>
  <table class=table>
    <thead>
      <th>Paket Soal yang bisa dimainkan:</th>
    </thead>
    $tr_paid
    $tr_free
  </table>

  <p class='abu f12 mt1'>Anda bisa membeli Paket Soal lainnya pada Katalog LKS</p>

";
