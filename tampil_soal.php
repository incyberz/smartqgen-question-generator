<?php
session_start();
$_SESSION['nama'] = 'Ahmad';
if (!isset($_SESSION['nama'])) {
  header("Location: welcome.php");
  exit;
}

$id_soal = $_GET['id'] ?? die("ID soal tidak ditemukan.");
$siswa_id = 123; // Contoh ID siswa

$conn = new mysqli("localhost", "root", "", "db_fisika");
$res = $conn->query("SELECT * FROM soal_fisika WHERE id = $id_soal");
$row = $res->fetch_assoc();

function generateRandomValue($range)
{
  list($min, $max) = explode("-", $range);
  return rand((int)$min, (int)$max);
}

function evaluateFormula($formula, $vars)
{
  extract($vars);

  $allowed_functions = ['sqrt', 'pow', 'sin', 'cos', 'tan', 'log', 'exp', 'abs', 'round', 'ceil', 'floor'];

  $formula_php = preg_replace_callback('/\b([a-zA-Z_][a-zA-Z0-9_]*)\b/', function ($match) use ($allowed_functions) {
    $word = $match[1];
    return in_array(strtolower($word), $allowed_functions) ? $word : '$' . $word;
  }, $formula);

  return eval("return $formula_php;");
}

// Randomisasi variabel
$variabel_json = json_decode($row['variabel_json'], true);
$randomVars = [];
foreach ($variabel_json as $var => $range) {
  $randomVars[$var] = generateRandomValue($range);
}

// Ganti placeholder di template soal
$soal_text = $row['soal_template'];
foreach ($randomVars as $key => $val) {
  $soal_text = str_replace("$$key", $val, $soal_text);
}

// Hitung jawaban benar
$jawaban_benar = evaluateFormula($row['formula'], $randomVars);
$jawaban_benar = round($jawaban_benar, 2); // dibulatkan 2 angka desimal

// Generate opsi PG
$opsi = [
  round(evaluateFormula($row['wrong_formula_1'], $randomVars), 2),
  round(evaluateFormula($row['wrong_formula_2'], $randomVars), 2),
  round(evaluateFormula($row['wrong_formula_3'], $randomVars), 2),
  $jawaban_benar
];
shuffle($opsi);

// Simpan data ke jawaban_siswa
$cek = $conn->query("SELECT id FROM jawaban_siswa WHERE id_siswa=$siswa_id AND id_soal=$id_soal");
if ($cek->num_rows == 0) {
  $stmt = $conn->prepare("INSERT INTO jawaban_siswa (id_siswa, id_soal, variabel_json, jawaban_benar) VALUES (?, ?, ?, ?)");
  $var_json = json_encode($randomVars);
  $stmt->bind_param("iisd", $siswa_id, $id_soal, $var_json, $jawaban_benar);
  $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Soal | SmartQGen</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container">
    <h2>Hai, <?= htmlspecialchars($_SESSION['nama']) ?>!</h2>
    <div class="question">
      <p><strong>Soal:</strong></p>
      <p><?= $soal_text ?></p>
    </div>

    <form method="POST" action="submit.php">
      <input type="hidden" name="id_soal" value="<?= $id_soal ?>">
      <input type="hidden" name="id_siswa" value="<?= $siswa_id ?>">
      <?php foreach ($opsi as $i => $pilihan): ?>
        <div class="option">
          <label>
            <input type="radio" name="jawaban" value="<?= $pilihan ?>" required>
            <?= chr(65 + $i) ?>. <?= $pilihan ?>
          </label>
        </div>
      <?php endforeach; ?>
      <button type="submit">Submit Jawaban</button>
    </form>
  </div>
</body>

</html>