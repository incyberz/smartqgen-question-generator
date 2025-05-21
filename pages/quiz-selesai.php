<?php
if (!$id_pengunjung) {
  alert("Pengunjung tidak valid.");
  jsurl('?', 1000);
}

// Ambil semua jawaban pengunjung
$s = "SELECT * 
FROM tb_jawaban a 
JOIN tb_soal b ON a.id_soal=b.id
WHERE a.id_pengunjung = $id_pengunjung";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$total = 0;
$benar = 0;
$data = [];


while ($row = mysqli_fetch_assoc($q)) {
  $total++;
  $jawaban = round($row['jawaban'], 2);
  $kunci    = round($row['jawaban_benar'], 2);
  $status = abs($jawaban - $kunci) <= 0.1 ? "✅ Benar" : "❌ Salah";
  if ($status === "✅ Benar") $benar++;

  $data[] = [
    'soal_id' => $row['id_soal'],
    'mapel' => $row['mapel'],
    'materi' => $row['materi'],
    'jawaban' => $jawaban,
    'kunci' => $kunci,
    'status' => $status
  ];
}

$nilai = $total > 0 ? round(($benar / $total) * 100) : 0;

// simpan ke field tmp_nilai pada tb_pengunjung
$sql = "UPDATE tb_pengunjung SET tmp_nilai = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("di", $nilai, $id_pengunjung);
$stmt->execute();

?>

<style>
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    color: #fff;
  }

  th,
  td {
    border: 1px solid #555;
    padding: 10px;
    text-align: center;
  }

  th {
    background-color: #333;
  }

  .score-box {
    background-color: #222;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    font-size: 24px;
    color: #0f0;
  }

  .quiz-selesai {
    max-width: 500px;
  }
</style>

<div class="flex flex-center">
  <div class="quiz-selesai">
    <h1 class="text-center">🎉 Quiz Selesai</h1>

    <div class="score-box">
      Skor Kamu: <strong><?= $nilai ?></strong> / 100
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Mapel</th>
          <th>Materi</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $i => $item): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= $item['mapel'] ?></td>
            <td><?= $item['materi'] ?></td>
            <td><?= $item['status'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="tengah mt4">
      <a href="?quiz-coba_lagi" class="btn btn-primary">🔁 Coba Lagi</a>
    </div>
  </div>
</div>