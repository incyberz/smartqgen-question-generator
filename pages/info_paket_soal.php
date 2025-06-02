<style>
  .row-paket {
    display: grid;
    grid-template-columns: 20px auto;
    gap: 5px;
  }
</style>
<?php
# ============================================================
# FREE PAKET
# ============================================================
$s = "SELECT * FROM tb_paket_soal a WHERE (harga is null OR harga = 0)";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$tr_free = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;

  $d['jumlah_soal'] = rand(70, 150); // ZZZ
  $Random = $d['mapel'] ? '' : 'Random, ';

  $tr_free .= "
    <tr>
      <td>
        <div class=row-paket>
          <div>$i.</div>
          <div>
            <span class='bold green'>$d[nama_paket]</span> 
            <div class='f12 abu'>$Random$d[jumlah_soal] soal</div> 
            <div class='f12 abu'>Min level: $d[min_level]</div> 
            <div class='f12 abu'>Valid until: <i>unlimitted</i></div> 
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
