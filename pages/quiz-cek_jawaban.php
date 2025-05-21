<?php
include '../conn.php';
$jawabans = $_GET['jawabans'] ?? die('undefined jawabans');
$id_pengunjung = $_GET['id_pengunjung'] ?? die('undefined id_pengunjung');

$rjawaban = json_decode($jawabans, true);
if (!$rjawaban) {
  die('tidak ada jawaban yang dikirim ke server.');
}

$s = "SELECT 
a.id_soal,
a.jawaban_benar,
c.nama_materi as materi, 
d.nama_mapel as mapel 
FROM tb_jawaban a 
JOIN tb_soal b ON a.id_soal=b.id 
JOIN tb_materi c ON b.id_materi=c.id 
JOIN tb_mapel d ON c.id_mapel=d.id
WHERE a.id_pengunjung = $id_pengunjung";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$total = 0;
$benar = 0;
$tr = '';
while ($d = mysqli_fetch_assoc($q)) {
  $total++;
  $jawaban = round($rjawaban[$d['id_soal']], 2);
  $kunci    = round($d['jawaban_benar'], 2);
  if (abs($jawaban - $kunci) <= 0.1) {
    $benar++;
    $status = '✅ Benar';
  } else {
    $status = '❌ Salah';
  }
  $tr .= "
    <tr>
      <td>$total</td>
      <td>$d[mapel]</td>
      <td>$d[materi]</td>
      <td>$status</td>
    </tr>
  ";
}
$nilai = $total > 0 ? round(($benar / $total) * 100) : 0;

echo "
  <h1 class='text-center'>🎉 Quiz Selesai</h1>

  <div class='score-box'>
    Skor Kamu: <strong>$nilai</strong> / 100
  </div>

  <table id=tb-hasil-quiz class='table table-dark'>
      <thead>
        <tr>
          <th>#</th>
          <th>Mapel</th>
          <th>Materi</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>$tr</tbody>
    </table>

  <div class='tengah mt4'>
    <a href='?quiz-coba_lagi' class='btn btn-primary'>🔁 Coba Lagi</a>
  </div>
";
