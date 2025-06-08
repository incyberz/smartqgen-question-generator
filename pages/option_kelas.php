<?php
if ($get_jenjang) {
  $option_kelas = '';
  $rjenjang_kelas = [
    'SD' => [
      'awal' => 1,
      'akhir' => 6,
    ],
    'SP' => [
      'awal' => 7,
      'akhir' => 9,
    ],
    'SA' => [
      'awal' => 10,
      'akhir' => 12,
    ],
  ];
  if (key_exists($get_jenjang, $rjenjang_kelas)) {
    for ($i = $rjenjang_kelas[$get_jenjang]['awal']; $i <= $rjenjang_kelas[$get_jenjang]['akhir']; $i++) {
      $option_kelas .= "<option value=$i>kelas $i</option>";
    }
  }
}
