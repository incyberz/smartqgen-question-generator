<link rel="stylesheet" href="./assets/css/hasil-quiz.css">
<style>
  .transparan:hover {
    background: none;
    color: yellow;
    font-weight: bold;
  }
</style>
<h1>Dashboard</h1>
<p>Selamat Datang <?= ucwords(strtolower($user['nama'])) ?> 👐</p>
<div class="f12">Anda login sebagai <?= $user['sebagai'] ?></div>
<?php
include 'dashboard-process.php';
include 'opsi_quiz.php';
include "$dotdot/includes/hari_tanggal.php";

# ============================================================
# HITUNG LP
# ============================================================
$lp = $user['basic_lp'];

# ===============================
# Ambil Data Paket
# ===============================
$s = "SELECT 
a.* 
FROM tb_paket a 
WHERE a.id_pengunjung = $id_pengunjung 
-- AND status < 100 -- belum di claim
";



$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

if (!mysqli_num_rows($q)) {
  die('Tidak ada Paket Kuis untuk pengunjung ini.');
} else {

  $num_rows = mysqli_num_rows($q);
  $unclaim_no = 0;
  $last_kuis_no = 0;
  $tr_unclaim = '';
  $tr_last_kuis = '';

  while ($d = mysqli_fetch_assoc($q)) {
    # ============================================================
    # AUTO DELETE IF STATUS PAKET IS NULL
    # ============================================================
    if (!$d['status']) {
      $s2 = "DELETE FROM tb_paket WHERE id=$d[id]";
      mysqli_query($cn, $s2) or die(mysqli_error($cn));
    }

    # ============================================================
    # LOOP BIASA | VALID ONLY
    # ============================================================
    $tgl = tanggal($d['waktu_submit']);
    $jam = date('H:i', strtotime($d['waktu_submit']));

    if ($d['status'] == 100) {
      $lp += $d['poin'];
      $last_kuis_no++;
      $tr_last_kuis .= "
        <tr>
          <td>
            <div>$tgl <i class='f12 abu'>$jam</i></div>
            <div><b class='f14'>Nilai</b>: $d[nilai] ~ <i class='f12 abu'>$d[poin] LP</i></div>
          </td>
          <td >
            <div>✅<i class='f12 putih'>Claimed</i></div>
          </td>
        </tr>
      ";
    } else {
      $unclaim_no++;
      $tr_unclaim .= "
        <tr>
          <td>
            <div>$tgl <i class='f12 abu'>$jam</i></div>
            <div><b class='f14'>Nilai</b>: $d[nilai] ~ <i class='f12 abu'>$d[poin] LP</i></div>
          </td>
          <td >
            <div>⚠️<i class='f12 yellow'>unclaim</i></div>
            <button name=btn_claim_poin value=$d[id] class='transparan f12 yellow hover' onclick='return confirm(`Claim $d[poin] LP?`)'>Claim XP</button> 
             | 
            <button name=btn_hapus_poin value=$d[id] class='transparan f12 yellow hover' onclick='return confirm(`Hapus poin?`)' >Hapus</button> 
          </td>
        </tr>
      ";
    }
  }

  if ($tr_unclaim) {
    $unclaim_points = "
      <h2 class='tengah f20 pt4 mt4 border-top'>🏅 Unclaim Points</h2>
      <p>Jika kamu claim maka Total Poin kamu bertambah dan data masuk ke History Kuis</p>
      <form method=post>
        <table id=tb-hasil-quiz>
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            $tr_unclaim
          </tbody>
        </table>
      </form>
    ";
  } else {
    $unclaim_points = "
      <h2 class='tengah f20 pt4 mt4 border-top'>✅ Claimed Last Kuis</h2>
      <p class=f14>Claimed Kuis tidak dapat dihapus karena poinnya sudah ditambahkan ke Learning Point</p>
      <form method=post>
        <table id=tb-hasil-quiz>
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            $tr_last_kuis
          </tbody>
        </table>
      </form>
    ";
  }
}

$lp_show = number_format($lp);

echo "
  <div id=main-dashboard>
    <h2 class='tengah f20 pt4 mt4 border-top'>Learning Points</h2>
    <div class='score-box'>
      💲<strong id=nilai>$lp_show</strong> LP
    </div>
    <div class='f14 mt2 hover btn-aksi' id=cairkan--toggle>Cairkan Points 👉</div>
    <div class=hideit id=cairkan>
      <div class='wadah kiri mt3' >
        <div class='mb1 tengah'>Hi Learners!! 😇</div>
        Learning Points dapat dicairkan menjadi Rupiah. Learning Points bisa kamu kumpulkan dari hasil menjawab Quiz. Prasyarat pencairan adalah:
        <ul class='pl4 f14'>
          <li>Saldo mencukupi ✅</li>
          <li>
            <span class='hover btn-aksi' id=span1--toggle>My Profile 100% 👉</span> 
            <span class=hideit id=span1>lengkapi foto, biodata, whatsapp tervalidasi Admin.</span>
          </li>
          <li>
            <span class='hover btn-aksi' id=span2--toggle>Approved by Akun Orangtua 👉 </span> 
            <span class='hideit' id=span2>Yakni Orangtua kamu, guru kamu, atau siapa saja yang menjadi Wali Murid untuk akun kamu di SmartQgen, ajak mereka untuk gabung ya! 😄 </span> 
          </li>
        </ul>
        So, lengkapi persyaratannya dahulu ya!!
        <button class='btn btn-secondary w-100 mt2 btn-aksi' id=cairkan--toggle--close>Close</button>
      </div>
    </div>

    $unclaim_points

  </div>

  
  <div class='pt2 tengah' style='display:grid; grid-template-columns: 50% 50%'>
    <a href=?history_kuis><h2 class='f18 hover'>⏲️ History Kuis</h2></a>
    <h2 class='f18 hover' id=play-again>▶️ Play Again</h2>
  </div>
  <div class=hideit id=blok-play-again>
    $opsi_quiz
    <div class='mt2'><a href='?quiz-started' class='btn btn-primary w-100'> Start Quiz</a></div>
  </div>
";
?>
<script>
  $(function() {
    $('#play-again').click(function() {
      $('#main-dashboard').slideToggle();
      $('#blok-play-again').slideToggle();
    })
  })
</script>