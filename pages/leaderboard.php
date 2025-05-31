<style>
  td {
    padding: 5px 10px !important;
  }
</style>
<?php
include 'quiz-process.php';

$awal_rekap = $today;
$sql = "SELECT *,
(
  SELECT COUNT(1) FROM tb_paket WHERE id_pengunjung=a.id 
  AND status is not null -- paket sudah dijawab
  ) punya_paket, 
(
  SELECT username FROM tb_user WHERE id_pengunjung=a.id 
  AND 1 -- role = 1 -- ortu masuk leaderboard
  ) username 
FROM tb_pengunjung a WHERE ";
$s = "$sql created_at >= '$awal_rekap'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$num_rows = mysqli_num_rows($q);
if ($num_rows < 5) {
  $last_week = date('Y-m-d', strtotime('-7 day', strtotime($today)));
  $awal_rekap = $last_week;
  $s = "$sql created_at >= '$awal_rekap'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $num_rows = mysqli_num_rows($q);
  if ($num_rows < 5) {
    $last_month = date('Y-m-d', strtotime('-30 day', strtotime($today)));
    $awal_rekap = $last_month;
    $s = "$sql created_at >= '$awal_rekap'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $num_rows = mysqli_num_rows($q);
  }
}

if (!$num_rows) stop('Tidak ada data pengunjung untuk leaderboard');

# ============================================================
# SUMMARY NILAI
# ============================================================
$rrank = [];
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  if (!$d['punya_paket']) continue; // 

  # ============================================================
  # JAWABAN UNARCHIVED (PENGUNJUNG ONLY)
  # ============================================================
  $s2 = "SELECT a.*,
  b.status, 
  b.waktu_load, 
  b.waktu_submit,
  c.lp 
  FROM tb_jawaban a 
  JOIN tb_paket b ON a.id_paket=b.id 
  JOIN tb_soal c ON a.id_soal=c.id 
  WHERE 1 -- a.id_paket=$id_paket -- milik current pengunjung 
  AND b.id_pengunjung = $d[id] 
  AND a.archived is null -- yang di archived ambil dari poin paket
  ";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $jumlah_soal_unarchived = mysqli_num_rows($q2);
  $nilai = 0;
  $benar = 0;
  $durasi_jawab = 0;
  if ($jumlah_soal_unarchived and !$d['username']) { // no username = pengunjung only
    $flat_reward = 0;
    while ($d2 = mysqli_fetch_assoc($q2)) {
      if ($d2['jawaban_benar'] == $d2['jawaban']) {
        $benar++;
        $flat_reward += $d2['lp'];
      }
      $durasi_jawab = $d2['waktu_submit'] ? strtotime($d2['waktu_submit']) - strtotime($d2['waktu_load']) : 0;
    }
    $nilai = $benar * 100 / $jumlah_soal_unarchived;
    $poin = $flat_reward * (((100 / $jumlah_soal_unarchived) * $benar) / 100);
    $average_icon = '';
    $user_icon = '🙂';
  } else { // khusus user, abaikan data user yang sedang main (unarchived)
    # ============================================================
    # AMBIL POIN DAN NILAI DARI DATA PAKET
    # ============================================================
    $s2 = "SELECT * FROM tb_paket WHERE id_pengunjung=$d[id]";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $sum_nilai = 0;
    $sum_poin = 0;
    $jumlah_paket = mysqli_num_rows($q2);

    while ($d2 = mysqli_fetch_assoc($q2)) {
      $sum_nilai += $d2['nilai'];
      $sum_poin += $d2['poin'];
    }
    $poin = $sum_poin;
    $nilai = round($sum_nilai / $jumlah_paket);

    if (!$d['username']) stop("invalid logic, harus punya username, id_pengunjung: $d[id]");

    $s2 = "SELECT * FROM tb_user WHERE username='$d[username]'";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $d2 = mysqli_fetch_assoc($q2);
    $nama = $d2['nama'];
    $d['nama'] = ucwords(strtolower($d2['nama']));


    $average_icon = '#️⃣';
    if ($d2['role'] == 1) { // pelajar
      $user_icon = $d2['gender'] == 'p' ? '👩' : '🧒';
    } elseif ($d2['role'] == 2) { // ortu
      $user_icon = $d2['gender'] == 'p' ? '👩‍🍼' : '👴';
    }
  }

  if ($i > 10) break;
  $rrank[$d['id']] = [
    'poin' => $poin,
    'nilai' => $nilai,
    'nama' => $d['nama'],
    'durasi_jawab' => $durasi_jawab,
    'average_icon' => $average_icon,
    'user_icon' => $user_icon,
  ];
}

uasort($rrank, function ($a, $b) {
  // Bandingkan poin (desc)
  if ($a['poin'] != $b['poin']) {
    return $b['poin'] <=> $a['poin'];
  }
  // Jika poin sama, bandingkan durasi_jawab (asc)
  return $a['durasi_jawab'] <=> $b['durasi_jawab'];
});

$my_rank = $num_rows;
$i = 0;

$medals = [
  1 => '🥇',
  2 => '🥈',
  3 => '🥉',
];

$tr = '';
foreach ($rrank as $k => $d) {
  $i++;
  $my_data = '';
  if ($id_pengunjung == $k) {
    $my_rank = $i;
    $my_data = 'my-data';
  }
  $medal = $medals[$i] ?? '';
  $tr .= "
    <tr class='$my_data'>
      <td><div class=right>$medal$i</div></td>
      <td>$d[nama] $d[user_icon]</td>
      <td>$d[poin]</td>
      <td>$d[average_icon]$d[nilai]</td>
    </tr>
  ";
}

$span_save = $username ? "<a class='f26 hover toggle' id=a-save href=?dashboard>💾</a>" : "<span class='f26 hover toggle' id=span-save>💾</span>";


$leaderboard = "
  <div class='leaderboard w-500'>
    <h1 class='text-center f24'>🏆 Leaderboard 🏆</h1>

    <div class='score-box'>
      Kamu Rank: <strong>$my_rank</strong> dari $num_rows
    </div>

    <table id=tb-hasil-quiz class='table table-dark'>
      <thead>
        <tr>
          <th>🏅</th>
          <th>Player</th>
          <th>Poin</th>
          <th>Nilai</th>
        </tr>
      </thead>
      <tbody>
        $tr
      </tbody>
    </table>

    <form method='POST' class='border-top mt4 pt4 hideit sub-form' id='form-coba-lagi'>
      <div class='f14 mb3'>Nilai kuis kamu yang ini akan hilang, pastikan Nilai Kuis yang selanjutnya lebih bagus 😄. Ready?!</div>
      <button type='submit' name=btn_start_quiz_lagi>Start Quiz Lagi</button>
    </form> 

    <div class='border-top mt4 pt4 hideit sub-form' id='petunjuk-register'>
      <div class='f14 mb3'>Untuk menyimpan poin, nilai, dan history belajar, kamu harus Register</div>
      <a href=?register class='btn btn-primary'>Register</a>
    </div> 

    <div class='border-top mt4 pt4 hideit sub-form' id='confirm-logout'>
      <div class='f14 mb3'>Yakin mau logout?</div>
      <a class='btn btn-warning ' href='?logout' >Logout $img_logout</a>
    </div> 

    <div class='tengah mt4'>
      <span class='f26 hover toggle' id=span-coba-lagi>🔁</span> 
      $span_save 
      <span class='f26 hover toggle' id=span-logout>⏏️</span> 
    </div>   

  </div>
";

echo $leaderboard;
?>
<script>
  $(function() {
    $('.toggle').click(function() {
      let id = $(this).prop('id');
      $('.sub-form').slideUp();
      if (id == 'span-coba-lagi') {
        $('#form-coba-lagi').slideToggle();
      } else if (id == 'span-save') {
        $('#petunjuk-register').slideToggle();
      } else if (id == 'span-logout') {
        $('#confirm-logout').slideToggle();
      }
    })
  })
</script>