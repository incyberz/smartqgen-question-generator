<?php
session_start();
include '../conn.php';
include 'quiz-functions.php';

header('Content-Type: application/json');
$id_pengunjung = $_SESSION['qgen_id_pengunjung'] ?? die('not login'); // TODO: ambil dari session atau parameter aman
$username = $_SESSION['qgen_username'] ?? null;



# ============================================================
# DELETE PAKET SOAL KOSONG DAN LJK NYA
# ============================================================
$s = "SELECT id FROM tb_paket_jawaban WHERE status is null AND id_pengunjung=$id_pengunjung";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  $s2 = "DELETE FROM tb_jawaban WHERE id_paket = $d[id] AND archived is null";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
}


# ============================================================
# BUAT PAKET BARU
# ============================================================
$s = "INSERT INTO tb_paket_jawaban (id_pengunjung) VALUES ($id_pengunjung)";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

# ============================================================
# DAPATKAN DAN START SESSION ID PAKET
# ============================================================
$s = "SELECT id FROM tb_paket_jawaban WHERE status is null AND id_pengunjung=$id_pengunjung";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die('Tidak dapat membuat Paket Baru.');
$d = mysqli_fetch_assoc($q);
$id_paket = $d['id'];
$_SESSION['qgen_id_paket'] = $id_paket;





# ============================================================
# MAIN SELECT SOAL
# ============================================================
$s = "SELECT 
  a.*,
  b.nama_materi as materi,
  d.nama_jenjang as jenjang,
  c.nama_mapel as mapel
  FROM tb_soal a 
  JOIN tb_materi b ON a.id_materi = b.id 
  JOIN tb_mapel c ON b.id_mapel = c.id 
  JOIN tb_jenjang d ON c.jenjang = d.jenjang 
  WHERE a.status >= 1 -- soal aktif
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
    'jenjang' => $soal['jenjang'],
    'mapel' => $soal['mapel'],
    'materi' => $soal['materi'],
    'gambar' => $soal['gambar'] ?: null,
    'kalimat_soal' => $kalimat_soal,
    'opsi' => array_values($opsi), // pastikan urut
  ];
}
$_SESSION['qgen_mulai_quiz'] = time();
echo json_encode($soalList, JSON_UNESCAPED_UNICODE);
