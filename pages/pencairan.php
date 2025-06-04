<?php
$kelas['saldo'] = 4000;

$ortu_show = ucwords(strtolower($kelas['nama_ortu'])) . " ($kelas[posisi_ortu])";
$saldo_show = 'Rp ' . number_format($kelas['saldo']);
$poin_show = number_format($user['poin']) . ' LP';

$s = "SELECT * FROM tb_level_pencairan";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$list_info_pencairan = '';
while ($d = mysqli_fetch_assoc($q)) {
  $emoji = '';
  if ($user['level'] >= $d['min_level'] and $kelas['saldo'] >= $d['pencairan']) {
    $user['max_pencairan'] = $d['pencairan'];
    $emoji = '✅';
  }
  $nominal = number_format($d['pencairan']);
  $list_info_pencairan .= "<li class=ml2>Rp $nominal - min level: $d[min_level] $emoji</li>";
}


echo "
  <div class='w-500'>
    <h2>Pencairan Poin</h2>
    
    <table class='table mt3'>
      <tr>
        <td>Kelas</td><td>$kelas[nama_kelas]</td>
      </tr>
      <tr>
        <td>Ortu</td><td>$ortu_show</td>
      </tr>
      <tr>
        <td>Saldo</td><td>$saldo_show</td>
      </tr>
      <tr>
        <td>Level</td><td>$user[level]</td>
      </tr>
      <tr>
        <td>Poin</td><td>$poin_show</td>
      </tr>
    </table>
    <h3 class='mb2 border-top mt3 pt3'>Pencairan Rp:</h3>
    <input class='tengah f30' type=number step=1000 min=2000 max=$user[max_pencairan] value=2000>
    <button class='btn btn-primary w-100 mt2' name=btn_cairkan>Cairkan</button>

    <div class='hover mt1 f12 btn-aksi' id=list_info_pencairan--toggle>Max Pencairan: Rp $user[max_pencairan], lihat info 👉</div>
    <ul class='hideit wadah pl4 left f14 mt3 gradasi-kuning' id=list_info_pencairan>
      $list_info_pencairan
    </ul>
  </div>
";
