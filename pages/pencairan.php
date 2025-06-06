<?php
// $user['sisa_poin'] = 40000;
// $kelas['saldo'] = 100;

akses('pencairan');
include './includes/key2kolom.php';
include 'pencairan-process.php';
$form_konten = '';

$ortu_show = ucwords(strtolower($kelas['nama_ortu'])) . " ($kelas[posisi_ortu])";
$saldo_show = 'Rp ' . number_format($kelas['saldo']);
$sisa_poin_show = number_format($user['sisa_poin']) . ' LP';

# ============================================================
# MAX PENCAIRAN
# ============================================================
$user['max_pencairan'] = 0; // default 0 artinya saldo tidak mencukupi
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

# ============================================================
# CEK DB PENDING PENCAIRAN
# ============================================================
$s = "SELECT * FROM tb_trx 
WHERE username='$username' 
AND jenis='k' 
AND 1 -- approv_date is null
ORDER BY tanggal DESC
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$pending_pencairan = 0;
$pending_take = 0;
if (mysqli_num_rows($q)) {
  $tr = '';
  $i = 0;
  $sum_pencairan = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $sum_pencairan += $d['nominal'];
    $tgl = tanggal($d['tanggal']);
    if ($d['approv_date']) {
      $img = $img_check;
      $status = '<span class=green>Approved</span>';
      $batal = '-';
      if (!$d['take_date']) {
        $pending_take++;
        $batal = "<button class='btn btn-primary' name=btn_sudah_terima_uang onclick='return confirm(`Confirm?`)'  value=$d[id]>Uangnya sudah saya terima</button>";
      } else {
        $take_date = tanggal($d['take_date']);
        $batal = "
          $img_check
          <div class='f12 abu'>Diambil pada: <br/>$take_date</div>
        ";
      }
    } else {
      $pending_pencairan++;
      $batal = "<button class='btn btn-danger' name=btn_batal_pencairan value=$d[id] onclick='return confirm(`Batalkan?`)'>Batalkan Pencairan</button>";
      $img = '<img src="assets/img/loading.gif" width=25px height=25px>';
      $status = "<span class='red'>Belum diapprove sama $kelas[posisi_ortu]</span>";
    }

    $nominal = number_format($d['nominal']);

    $tr .= "
      <tr id=tr__$d[id]>
        <td>$i</td>
        <td>
          <div style='display:flex;gap:10px'>
            <div class=pt1>$img</div>
            <div>
              <div>Rp $nominal</div>
              $status
              <div class='f12 abu'>$tgl</div>
            </div>
          </div>
        </td>
        <td>$batal</td>
      </tr>
    ";
  } // end while

  # ============================================================
  # UPDATE SUM PENCAIRAN
  # ============================================================
  $s = "UPDATE tb_tmp SET sum_pencairan = $sum_pencairan WHERE username='$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));


  # ============================================================
  # HISTORY PENCAIRAN
  # ============================================================
  $form_konten = "
    <h3 class='mb2'>Pencairan Saya</h3>
    <table>
      $tr
    </table>
  ";
} // end if numrow

$rules = '';
foreach ($config as $k => $v) {
  if ($k == 'ortu' || !$v) continue;
  $kolom = key2kolom($k);
  $rules .= "<li><span class=f12>$kolom:</span> $v</li>";
}


if (!$pending_pencairan and !$pending_take) {
  $history = !$form_konten ? '' : "
    <div class='f14 hover btn-aksi border-top mt4 pt4' id=history_pencairan--toggle>Lihat History Pencairan</div>
    <div class=hideit id=history_pencairan>
      $form_konten
    </div>
  ";

  # ============================================================
  # OVERIDE FORM KONTEN
  # ============================================================
  $min_value = $config_default['nominal_pencairan_min']['value'];
  $min_value = $config['nominal_pencairan_min'] > $min_value ? $config['nominal_pencairan_min'] : $min_value;

  $max_value = $user['max_pencairan'] ?? kosong('max_pencairan');
  $max_value = $config['nominal_pencairan_max'] < $max_value ? $config['nominal_pencairan_max'] : $max_value;

  $disabled = '';
  if ($min_value > $user['sisa_poin'] || $min_value > $kelas['saldo']) {
    if ($min_value > $user['sisa_poin']) { // poin tidak mencukupi, Play Again
      $input_cairkan = "
        <div class='alert alert-danger w-100 mt2' >
          Maaf, Poin tidak Mencukupi
          <a href=?play_again class='btn btn-primary mt2 w-100'>Play Again</a>
        </div>
      ";
    } else { // saldo tidak mencukupi, hubungi Papa
      $input_cairkan = "
        <div class='alert alert-danger w-100 mt2' >
          Maaf, Saldo Kelas tidak Mencukupi
          <a href='?hubungi&ke=ortu&hal=Saldo Kelas tidak Mencukupi' class='btn btn-primary mt2 w-100'>Hubungi $Papa</a>
        </div>
      ";
    }
  } else { // saldo mencukupi, poin mencukupi
    $input_cairkan = "
      <h3 class='mb2'>Pencairan Rp:</h3>
      <input class='tengah f30' type=number step=1000 min=$min_value max=$max_value value=$min_value name=nominal required $disabled />
      <button class='btn btn-primary w-100 mt2' name=btn_cairkan>Cairkan</button>
    ";
  }

  $form_konten = "
    $input_cairkan

    <div class='hover mt1 f12 btn-aksi' id=list_info_pencairan--toggle>Max Pencairan: Rp $max_value, lihat info 👉</div>
    <div class='hideit wadah left f14 mt3 gradasi-kuning' id=list_info_pencairan style='position:absolute;z-index:2;bottom:0;right:15px;left:15px'>
      Rule by Points dan Saldo
      <ul class='pl4 mb3'>
        $list_info_pencairan
      </ul>
      Rule dari $Papa
      <ul class='pl4'>$rules</ul>
      <span class='btn btn-primary hover mt3 btn-aksi' id=list_info_pencairan--toggle--close>Close</span>
    </div>
    $history
  ";
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
        <td>Poin Sisa</td><td>$sisa_poin_show</td>
      </tr>
    </table>
    <form method=post class='border-top mt3 pt3'>
      $form_konten
    </form>
  </div>
";
