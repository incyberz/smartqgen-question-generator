<?php
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

// Ambil soal template
$id_soal = $_GET['id'] ?? die('GET id untuk soal undefined.');
$siswa_id = 123;

$conn = new mysqli("localhost", "root", "", "db_fisika");
$res = $conn->query("SELECT * FROM soal_fisika WHERE id = $id_soal");
$row = $res->fetch_assoc();

$soal_template = $row['soal_template'];
$formula = $row['formula'];
$wrong1 = $row['wrong_formula_1'];
$wrong2 = $row['wrong_formula_2'];
$wrong3 = $row['wrong_formula_3'];
$variabel_json = json_decode($row['variabel_json'], true);

$randomVars = [];
foreach ($variabel_json as $var => $range) {
  $randomVars[$var] = generateRandomValue($range);
}

$soal_final = $soal_template;
foreach ($randomVars as $var => $val) {
  $soal_final = str_replace("$$var", $val, $soal_final);
}

// Hitung jawaban dan opsi lainnya
$jawaban_benar = evaluateFormula($formula, $randomVars);
$opsi = [
  round($jawaban_benar, 2),
  round(evaluateFormula($wrong1, $randomVars), 2),
  round(evaluateFormula($wrong2, $randomVars), 2),
  round(evaluateFormula($wrong3, $randomVars), 2)
];

// Acak urutan opsi
shuffle($opsi);

// Simpan jawaban benar dan variabel ke DB
$variabel_json_encoded = json_encode($randomVars);
$stmt = $conn->prepare("INSERT INTO jawaban_siswa (id_siswa, id_soal, variabel_json, jawaban_benar) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iisd", $siswa_id, $id_soal, $variabel_json_encoded, $jawaban_benar);
$stmt->execute();

// Tampilkan soal dan opsi
echo "<h3>Soal:</h3>";
echo "<p>$soal_final</p>";
echo "<form method='POST' action='submit.php'>";
echo "<input type='hidden' name='id_soal' value='$id_soal'>";
echo "<input type='hidden' name='id_siswa' value='$siswa_id'>";

foreach ($opsi as $index => $val) {
  $label = chr(65 + $index); // A, B, C, D
  echo "<label><input type='radio' name='jawaban' value='$val' required> $label. $val</label><br>";
}

echo "<button type='submit'>Kirim</button>";
echo "</form>";
