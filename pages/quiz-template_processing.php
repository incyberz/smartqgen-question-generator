<?php
# ============================================================
# TEMPLATE SOAL PROCESSING
# ============================================================

// Randomisasi variabel
$variabel_json = json_decode($soal['variabel_json'], true);
$randomVars = [];
foreach ($variabel_json as $var => $range) {
  $randomVars[$var] = generateRandomValue($range);
}

// Ganti placeholder di template soal
$kalimat_soal = $soal['soal_template'];
foreach ($randomVars as $key => $val) {
  $kalimat_soal = str_replace("$$key", $val, $kalimat_soal);
}

// Hitung jawaban benar
$jawaban_benar = evaluateFormula($soal['formula'], $randomVars);
$jawaban_benar = round($jawaban_benar, 2); // dibulatkan 2 angka desimal

// Generate opsi PG
$opsi = [
  round(evaluateFormula($soal['wrong_formula_1'], $randomVars), 2),
  round(evaluateFormula($soal['wrong_formula_2'], $randomVars), 2),
  round(evaluateFormula($soal['wrong_formula_3'], $randomVars), 2),
  $jawaban_benar
];
shuffle($opsi);

# ============================================================
# AUTOSAVE DB JAWABAN 
# ============================================================
$variabel_json = json_encode($randomVars);
mysqli_query($cn, "INSERT INTO tb_jawaban (
  id_paket, 
  id_soal, 
  variabel_json, 
  jawaban_benar
) VALUES (
  $id_paket, 
  $id_soal, 
  '$variabel_json', 
  '$jawaban_benar'
) ON DUPLICATE KEY UPDATE 
  variabel_json = '$variabel_json', 
  jawaban_benar = '$jawaban_benar'
") or die(mysqli_error($cn));
