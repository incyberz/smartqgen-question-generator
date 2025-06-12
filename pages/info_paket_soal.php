<style>
  .row-paket {
    display: grid;
    grid-template-columns: 20px auto;
    gap: 5px;
  }
</style>
<?php
$rtr = [];
$rtr['paid'] = '';
# ============================================================
# FREE PAKET
# ============================================================
$jenis = 'free';
include 'info_paket_soal-tr.php';
$rtr['free'] = $tr;

$jenis = 'paid';
include 'info_paket_soal-tr.php';
$rtr['paid'] = $tr;



$info_paket_soal = "
  <h2 class='tengah f20 pt4 mt4 border-top'>Premium Paket Soal 🌟</h2>
  <table class=table>
    <thead>
      <th>Paket Soal yang bisa dimainkan:</th>
    </thead>
    $rtr[paid]
  </table>

  <h2 class='tengah f20 pt4 mt4 border-top'>Free Paket Soal</h2>
  <table class=table>
    $rtr[free]
  </table>

  <p class='abu f12 mt1'>Anda bisa membeli Paket Soal lainnya pada Katalog LKS</p>

";
