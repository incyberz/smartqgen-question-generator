<link rel="stylesheet" href="./assets/css/progress.css">
<?php
$user_src = "assets/img/user/$user[image]";
$src = ($user['image'] and file_exists($user_src)) ? $user_src : $default_src;

include 'tmp_data.php';
$awal_poin = $tmp['awal_poin'];
$next_poin = $tmp['next_poin'];
$level = $tmp['level'];

if (!$level || !$awal_poin || !$next_poin) {
  include 'update_tmp.php';
  alert('updating levels...', 'info');
  jsurl('', 3000);
}

$persen = round(($tmp['poin'] - $awal_poin) * 100 / ($next_poin - $awal_poin), 2);
$unfill_poin = ($next_poin - $awal_poin) - ($tmp['poin'] - $awal_poin);


# ============================================================
# INFO PENCAIRAN DEFAULT
# ============================================================
$info_pencairan = "
  <div class=' pt2 mt2 mb4'>
    <div class='biru hover btn-aksi' id=info-pencairan--toggle>Untuk Pencairan Poin kamu harus punya Orangtua dan ikut Grup Kelas 👉</div>
    <div class='hideit mt3' id=info-pencairan>
      <div class='wadah gradasi-kuning left pb4'>
        <h3 class=''>Info Pencairan</h3>
        <ul class=pl3>
          <li>Hanya Akun Orangtua yang berhak membuat Grup Kelas</li>
          <li>Orangtua nanti memasukan kamu ke Grup Kelasnya</li>
          <li>Mungkin adik, kakak, atau teman kamu juga akan diikutkan 😃</li>
        </ul>
        <div class='biru hover btn-aksi mt4 tengah' id=info-pencairan--toggle--close>OK, saya faham</div>
      </div>
    </div>
  </div>
";

$info_kelas = "
  <div class='border-top pt4 mt4'>
    <div class='f14 miring'>Kamu belum mengikuti kelas apapun.</div>
    <div class='biru mb3'>Yuk ajak orangtuamu buat bikin Kelas</div>
    <a href=?ajak_ortu class='btn btn-primary '>Ajak Orangtua 👉</a>
  </div>
";

# ============================================================
# PUNYA KELAS DAN ORTU
# ============================================================
if ($kelas) {
  $info_pencairan = "<a href=?pencairan class='btn btn-primary mt3'>💰 Pencairan Poin 👉</a>";
  # ============================================================
  # CEK TRX PENCAIRAN
  # ============================================================
  $s = "SELECT * FROM tb_trx WHERE username = '$username' 
  AND (approv_date is null OR take_date is null)";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    # ============================================================
    # OVERIDE BUTTON INFO PENCAIRAN
    # ============================================================
    while ($d = mysqli_fetch_assoc($q)) {
      $nominal = number_format($d['nominal']);
      if ($d['approv_date'] and !$d['take_date']) {
        $tanggal = hari_tanggal($d['approv_date']);
        $info_pencairan = "
          <div class='border-top mt4 pt4 pelajar'>
            $Papa kamu sudah Approv Pencairan 
            <div class=f30>Rp $nominal,-</div> 
            <div class='f12 abu'>Hari $tanggal</div>
            <div class='mt2 mb2'>Apakah kamu sudah menerimanya?</div>
            <div class='grid2 gap1'>
              <form method=post>
                <button class='btn btn-primary w-100' onclick='return confirm(`Confirm ?`)' name=btn_sudah_terima_uang value=$d[id]>Sudah</button>
              </form>
              <div>
                <a href=?pencairan>
                  <button class='btn btn-secondary w-100'>Belum</button>
                </a>
              </div>
            </div>
          </div>
        ";
      }
    }
  }






  # ============================================================
  # DATA PESERTA KELAS
  # ============================================================
  $s = "SELECT a.*,
  b.nama as nama_peserta,
  c.poin,
  c.nilai 
  FROM tb_peserta a 
  JOIN tb_user b ON a.username=b.username 
  JOIN tb_tmp c ON b.username=c.username 
  WHERE a.id_kelas='$kelas[id]' 
  ORDER BY c.poin DESC
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $tr = '';
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $nama_peserta = ambilNamaDepan($d['nama_peserta']);
    $row_saya = $d['username'] == $username ? 'row-saya' : '';
    $tr .= "
      <tr class='$row_saya'>
        <td>$i</td>
        <td>$nama_peserta</td>
        <td>$d[poin]</td>
        <td>$d[nilai]</td>
      </tr>
    ";
  }

  $nama_ortu = ucwords(strtolower($kelas['nama_ortu']));

  $info_kelas = "
    <div class='border-top pt4 mt4 pb4'>
      <div class='f14 miring'>Kelas kamu:</div>
      <h3 class='m0'>$kelas[nama_kelas]</h3>
      <div class=''>By: $nama_ortu</div>

      <table class='table'>
        <thead>
          <th>Rank</th>
          <th>Peserta</th>
          <th>Σ Poin</th>
          <th>Nilai</th>
        </thead>
        $tr
      </table>
    </div>
  ";
}

$tmp_poin = number_format($tmp['poin']);
$sum_pencairan = $tmp['sum_pencairan'];
$sisa_poin = $tmp['poin'] - $sum_pencairan;
$sisa_poin_show = number_format($sisa_poin);
$sum_pencairan_show = number_format($sum_pencairan);

$fitur = "
  <div class=ortu>
    <div><img src=$src class=foto_profil></div>
    <div class=score-box>
      <div>Level: <span id=level class=f40>$level</span></div>
      <div class=progress>
        <div class='progress-bar progress-bar-animated' style=width:$persen%></div>
      </div>
      <span class=f12>$unfill_poin LP to Next Level</span>
      <h2 class='mb1 border-top pt4 mt4'>Learning Points</h2>
      <div class='f30 bold mb2'>$sisa_poin_show <i class='f14'>LP</i></div>
      <div class='f14 green border-top pt2'>$tmp_poin <span class='darkred f12'>- $sum_pencairan_show</span></div>
      $info_pencairan
    </div>
  </div>
  $info_kelas
";
