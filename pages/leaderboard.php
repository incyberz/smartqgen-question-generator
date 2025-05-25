<link rel="stylesheet" href="assets/css/hasil-quiz.css">
<?php
include 'quiz-process.php';

$awal_rekap = $today;
$sql = "SELECT *,
(SELECT COUNT(1) FROM tb_paket WHERE id_pengunjung=a.id AND status is not null) punya_paket 
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
while ($d = mysqli_fetch_assoc($q)) {
  // if (!$d['punya_paket']) continue;
  $s2 = "SELECT a.*,
  b.status, 
  b.waktu_load, 
  b.waktu_submit 
  FROM tb_jawaban a 
  JOIN tb_paket b ON a.id_paket=b.id 
  WHERE a.id_paket=$id_paket 
  AND b.id_pengunjung = $d[id] 
  AND a.archived is null
  ";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $jumlah_soal = mysqli_num_rows($q2);
  $nilai = 0;
  $benar = 0;
  $durasi_jawab = 0;
  if ($jumlah_soal) {
    while ($d2 = mysqli_fetch_assoc($q2)) {
      if ($d2['jawaban_benar'] == $d2['jawaban']) $benar++;
      $durasi_jawab = $d2['waktu_submit'] ? strtotime($d2['waktu_submit']) - strtotime($d2['waktu_load']) : 0;
    }
    $nilai = $benar * 100 / $jumlah_soal;
  }
  $rrank[$d['id']] = [
    'nilai' => $nilai,
    'nama' => $d['nama'],
    'durasi_jawab' => $durasi_jawab,
  ];
}

uasort($rrank, function ($a, $b) {
  // Bandingkan nilai (desc)
  if ($a['nilai'] != $b['nilai']) {
    return $b['nilai'] <=> $a['nilai'];
  }
  // Jika nilai sama, bandingkan durasi_jawab (asc)
  return $a['durasi_jawab'] <=> $b['durasi_jawab'];
});

$my_rank = $num_rows;
$i = 0;

$tr = '';
foreach ($rrank as $k => $d) {
  $i++;
  $my_data = '';
  if ($id_pengunjung == $k) {
    $my_rank = $i;
    $my_data = 'my-data';
  }
  $tr .= "
    <tr class='$my_data'>
      <td>$i</td>
      <td>$d[nama]</td>
      <td>$d[nilai]</td>
      <td>$d[durasi_jawab]</td>
    </tr>
  ";
}

$span_save = $username ? "<a class='f26 hover toggle' id=a-save href=?dashboard>💾</a>" : "<span class='f26 hover toggle' id=span-save>💾</span>";


$leaderboard = "
  <div class=leaderboard style=max-width:500px>
    <h1 class='text-center f24'>🏆 Leaderboard 🏆</h1>

    <div class='score-box'>
      Kamu Rank: <strong>$my_rank</strong> dari $num_rows
    </div>

    <table id=tb-hasil-quiz class='table table-dark'>
      <thead>
        <tr>
          <th>#</th>
          <th>Player</th>
          <th>Nilai</th>
          <th>Waktu</th>
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
      <div class='f14 mb3'>Untuk menyimpan nilai dan history belajar kamu harus Register</div>
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