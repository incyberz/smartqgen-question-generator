<?php
include '../conn.php';
include 'quiz-functions.php';

header('Content-Type: application/json');
$id_pengunjung = 1; // TODO: ambil dari session atau parameter aman

# ============================================================
# MAIN SELECT SOAL
# ============================================================
$s = "SELECT 
  a.*,
  b.nama_materi as materi,
  b.tingkat,
  c.nama_mapel as mapel
  FROM tb_soal a 
  JOIN tb_materi b ON a.id_materi = b.id 
  JOIN tb_mapel c ON b.id_mapel = c.id 
  WHERE a.status = 100
  ORDER BY rand()
  LIMIT 5";

$q = mysqli_query($cn, $s);
if (!$q) {
  http_response_code(500);
  echo json_encode(['error' => 'Gagal mengambil data soal.']);
  exit;
}

$jumlah_soal = mysqli_num_rows($q);
if (!$jumlah_soal) {
  http_response_code(404);
  echo json_encode(['error' => 'Tidak ada soal tersedia.']);
  exit;
}

$soalList = [];
while ($soal = mysqli_fetch_assoc($q)) {
  $id_soal = $soal['id'];
  include 'quiz-template_processing.php'; // menghasilkan $kalimat_soal dan $opsi

  $soalList[$id_soal] = [
    'id' => $soal['id'],
    'tingkat' => $soal['tingkat'],
    'mapel' => $soal['mapel'],
    'materi' => $soal['materi'],
    'gambar' => $soal['gambar'] ?: null,
    'kalimat_soal' => $kalimat_soal,
    'opsi' => array_values($opsi), // pastikan urut
  ];
}

echo json_encode($soalList, JSON_UNESCAPED_UNICODE);
