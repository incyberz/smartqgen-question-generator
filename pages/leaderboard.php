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
  SELECT COUNT(1) FROM tb_paket_jawaban WHERE id_pengunjung=a.id 
  AND status is not null -- paket sudah dijawab
  ) punya_paket, 
(SELECT username FROM tb_user WHERE id_pengunjung=a.id) username, 
(SELECT role FROM tb_user WHERE id_pengunjung=a.id) role 
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
  }
}
$s = "$sql 1"; // ZZZ DEBUG SHOW ALL
// echo '<pre>';
// print_r($s);
// echo '</pre>';
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$num_rows = mysqli_num_rows($q);

if (!$num_rows) stop('Tidak ada data pengunjung untuk leaderboard');

# ============================================================
# SUMMARY NILAI
# ============================================================
$rrank = [];
$jumlah_player = 0;
while ($d = mysqli_fetch_assoc($q)) {
  if ((!$user['role'] || $user['role'] == 1) // skip ortu untuk pengunjung atau pelajar
    and $d['username'] // untuk username ortu yang ini
    and $d['role'] == 2 // skip role ortu
  ) continue; // 

  $jumlah_player++;
  if ($d['username']) { // jika sudah jadi pelajar
    $s2 = "SELECT * FROM tb_user WHERE username='$d[username]'";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $d2 = mysqli_fetch_assoc($q2);
    $nama = $d2['nama']; // ambil nama pelajar
    $d['nama'] = ucwords(strtolower($d2['nama']));
    $gender_icon = gender_icon($d2['gender'], $d2['role']);

    # ============================================================
    # JIKA MILIK SAYA
    # ============================================================
    if ($d['username'] == $username) include 'update_tmp.php';

    # ============================================================
    # AMBIL DARI DATA TMP
    # ============================================================
    $s2 = "SELECT * FROM tb_tmp WHERE username='$d[username]'";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $d2 = mysqli_fetch_assoc($q2);
    $poin = $d2['poin'] ?? 0;
    $nilai = $d2['nilai'] ?? 0;
    $play_count = $d2['play_count'] ?? 0;
    $durasi_jawab = '';
  } else {
    # ============================================================
    # JAWABAN UNARCHIVED (PENGUNJUNG ONLY)
    # ============================================================
    $s2 = "SELECT a.*,
    b.status, 
    b.waktu_load, 
    b.waktu_submit,
    c.lp 
    FROM tb_jawaban a 
    JOIN tb_paket_jawaban b ON a.id_paket=b.id 
    JOIN tb_soal c ON a.id_soal=c.id 
    WHERE a.id_paket='$id_paket' -- milik current pengunjung 
    AND b.id_pengunjung = $d[id] 
    AND a.archived is null -- yang di archived ambil dari poin paket
    ";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $jumlah_jawab = mysqli_num_rows($q2);
    $nilai = 0;
    $benar = 0;
    $durasi_jawab = 0;
    if ($jumlah_jawab and !$d['username']) { // no username = pengunjung only
      $flat_reward = 0;
      while ($d2 = mysqli_fetch_assoc($q2)) {
        if ($d2['jawaban_benar'] == $d2['jawaban']) {
          $benar++;
          $flat_reward += $d2['lp'];
        }
        $durasi_jawab = $d2['waktu_submit'] ? strtotime($d2['waktu_submit']) - strtotime($d2['waktu_load']) : 0;
      }
      $nilai = round($benar * 100 / $jumlah_jawab);
      $poin = round($flat_reward * (((100 / $jumlah_jawab) * $benar) / 100));
      $gender_icon = '👤';
    } else { // khusus user, abaikan data user yang sedang main (unarchived)
      # ============================================================
      # AMBIL POIN DAN NILAI DARI DATA PAKET
      # ============================================================
      $s2 = "SELECT * FROM tb_paket_jawaban WHERE id_pengunjung=$d[id]";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      $sum_nilai = 0;
      $sum_poin = 0;
      $play_count = mysqli_num_rows($q2);

      while ($d2 = mysqli_fetch_assoc($q2)) {
        $sum_nilai += $d2['nilai'];
        $sum_poin += $d2['poin'];
      }
      $poin = $sum_poin;
      $nilai = round($sum_nilai / $play_count);
    }
  }


  if ($jumlah_player > 10) break;
  $rrank[$d['id']] = [
    'poin' => $poin,
    'nilai' => $nilai,
    'play_count' => $play_count,
    'nama' => $d['nama'],
    'durasi_jawab' => $durasi_jawab,
    'gender_icon' => $gender_icon,
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
    # ============================================================
    # AUTO UPDATE TMP POIN
    # ============================================================
    if ($username) {
      mysqli_query($cn, "UPDATE tb_tmp SET 
        poin=$d[poin],
        nilai=$d[nilai],
        play_count=$d[play_count],
        rank=$my_rank 
      WHERE username='$username'") or die(mysqli_error($cn));
    }
  }
  $medal = $medals[$i] ?? '';
  $tr .= "
    <tr class='$my_data'>
      <td><div class=right>$medal$i</div></td>
      <td>$d[nama] $d[gender_icon]</td>
      <td>$d[poin]</td>
      <td>$d[nilai]</td>
    </tr>
  ";
}

$span_save = $username ? "<a class='f26 hover toggle' id=a-save href=?dashboard>💾</a>" : "<span class='f26 hover toggle' id=span-save>💾</span>";


$leaderboard = "
  <div class='leaderboard w-500'>
    <h1 class='text-center f24'>🏆 Leaderboard 🏆</h1>

    <div class='score-box'>
      Kamu Rank: <strong>$my_rank</strong> dari $jumlah_player
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

abort_quiz();
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