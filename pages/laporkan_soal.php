<?php
# ============================================================
# BTN LAPORKAN
# ============================================================
$s = "SELECT * FROM tb_alasan";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$ralasan = [];
while ($d = mysqli_fetch_assoc($q)) {
  $ralasan[$d['id']] = $d;
}


$btn_laporkan = '';
foreach ($ralasan as $k => $v) {
  $btn_laporkan .= "
    <button class='btn btn-danger w-100 mb1 mt1' name=btn_laporkan value='$k' onclick='return confirm(`Confirm Laporkan?`)'>$v[alasan]</button>
  ";
}
$laporkan_soal = "
  <span class='hover f14 btn-aksi' id=blok-laporkan-soal--toggle>Laporkan Soal</span>
  <div class='hideit mt3 bordered br5 p2 gradasi-merah' id=blok-laporkan-soal>
    $btn_laporkan
    <span class='btn btn-secondary w-100 mb1 mt1' id=btn_cancel_laporkan>Cancel</span>
  </div>
";
?>
<script>
  $(function() {
    $('#btn_cancel_laporkan').click(function() {
      $('#blok-laporkan-soal').slideUp();
    })
  })
</script>