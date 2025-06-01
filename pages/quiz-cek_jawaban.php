<?php
session_start();
header('Content-Type: application/json');
include '../conn.php';

function sendError($msg)
{
  echo json_encode(['status' => 'error', 'msg' => $msg]);
  exit;
}

function sendSuccess($html)
{
  echo json_encode(['status' => 'success', 'html' => $html]);
  exit;
}

$jawabans = $_GET['jawabans'] ?? null;
if (!$jawabans) sendError('Tidak ada data jawaban yang harus diproses.');

$id_pengunjung = $_SESSION['qgen_id_pengunjung'] ?? null;
if (!$id_pengunjung) sendError('Session login sudah habis, silahkan mulai dari awal.');

$id_paket = $_SESSION['qgen_id_paket'] ?? null;
if (!$id_paket) sendError('Session id paket login sudah habis, silahkan mulai dari awal.');

$rjawaban = json_decode($jawabans, true);
if (!$rjawaban) sendError('Data jawaban kosong atau tidak valid.');

# ===============================
# Ambil Data LJK dari Pengunjung
# ===============================
$s = "SELECT 
a.id,
a.id_soal,
a.jawaban_benar,
b.lp,
c.nama_materi as materi, 
d.nama_mapel as mapel 
FROM tb_jawaban a 
JOIN tb_soal b ON a.id_soal = b.id 
JOIN tb_materi c ON b.id_materi = c.id 
JOIN tb_mapel d ON c.id_mapel = d.id 
JOIN tb_paket_jawaban e ON a.id_paket = e.id 
WHERE e.id = $id_paket 
AND a.archived is null
";
$q = mysqli_query($cn, $s) or sendError(mysqli_error($cn));

if (!mysqli_num_rows($q)) sendError('Tidak ada LJK pada database untuk pengunjung ini.');

$flat_reward = 0;
$total = 0;
$benar = 0;
$tr = '';


while ($d = mysqli_fetch_assoc($q)) {
  $total++;
  $id_soal = $d['id_soal'];
  if (!isset($rjawaban[$id_soal])) {
    $jawaban = 0;
  } else {
    $jawaban = round($rjawaban[$id_soal], 2);
  }
  $kunci = round($d['jawaban_benar'], 2);
  $status = abs($jawaban - $kunci) <= 0.1 ? '✅ Benar' : '❌ Salah';

  if ($status === '✅ Benar') {
    $benar++;
    $flat_reward += $d['lp'];
  }

  $tr .= "
    <tr>
      <td>$total</td>
      <td>{$d['mapel']}</td>
      <td>{$d['materi']}</td>
      <td>$status</td>
    </tr>
  ";

  $s2 = "UPDATE tb_jawaban SET jawaban='$jawaban' WHERE id='$d[id]'";
  mysqli_query($cn, $s2) or sendError(mysqli_error($cn));
}

# ============================================================
# HITUNG NILAI DAN LP
# ============================================================
$nilai = $total > 0 ? round(($benar / $total) * 100) : 0;
$poin = $flat_reward * (((100 / $total) * $benar) / 100);

# ============================================================
# UPDATE PAKET
# ============================================================
$s = "UPDATE tb_paket_jawaban SET 
  status = 1, -- terjawab
  waktu_submit = CURRENT_TIMESTAMP,
  nilai = $nilai,
  poin = $poin  
WHERE id=$id_paket";
mysqli_query($cn, $s) or sendError(mysqli_error($cn));

$html = "
  <div style='display:flex; justify-content:center; align-items:center;height:100%'>
    <div>
      <h1 class='text-center'>🎉 Quiz Selesai</h1>
      <div class='score-box'>
        Skor Kamu: <strong id=nilai>$nilai</strong> / 100
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
      <div class='mt2'><a href='?leaderboard' class='btn btn-primary w-100'>🏆 Leaderboard</a></div>
    </div>
  </div>
";

sendSuccess($html);
