<?php
akses('pencairan');
include 'pencairan-process.php';
$form_konten = '';

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
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
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
  }

  $form_konten = "
    <h3 class='mb2'>Pencairan Saya</h3>
    <table>
      $tr
    </table>
  ";
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
  $form_konten = "
    <h3 class='mb2'>Pencairan Rp:</h3>
    <input class='tengah f30' type=number step=1000 min=2000 max=$user[max_pencairan] value=2000 name=nominal required>
    <button class='btn btn-primary w-100 mt2' name=btn_cairkan>Cairkan</button>
    <div class='hover mt1 f12 btn-aksi' id=list_info_pencairan--toggle>Max Pencairan: Rp $user[max_pencairan], lihat info 👉</div>
    <ul class='hideit wadah pl4 left f14 mt3 gradasi-kuning' id=list_info_pencairan>
      $list_info_pencairan
    </ul>
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
        <td>Poin</td><td>$poin_show</td>
      </tr>
    </table>
    <form method=post class='border-top mt3 pt3'>
      $form_konten
    </form>
  </div>
";
