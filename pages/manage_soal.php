<?php
akses('manage_soal');
include 'includes/key2kolom.php';

$get_id_materi = $_GET['id_materi'] ?? kosong('id_materi');


# ============================================================
# INFO MATERI
# ============================================================
$materi = [];
include 'info_materi.php';


# ============================================================
# MAIN SELECT SOAL
# ============================================================
$s = "SELECT 
a.id as id_soal,
b.jenis_soal,
(SELECT COUNT(1) FROM tb_jawaban WHERE id_soal=a.id AND archived=1) count_jwb,
a.soal_template,
a.formula as formula_benar,
a.wrong_formula_1,
a.wrong_formula_2,
a.wrong_formula_3,
a.gambar,
a.variabel_json,
b.* 
FROM tb_soal a 
JOIN tb_jenis_soal b ON a.jenis_soal=b.jenis 
WHERE a.id_materi=$get_id_materi";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  $th = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $td = '';
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'date_created'
        || $key == 'jenis'
        || $key == 'basic_lp'
        || $key == 'var_lp'
        || $key == 'gambar'
      ) {
        continue;
      } elseif ($key == 'soal_template' || $key == 'formula_benar') {
        $value = "<div class='blue mb1'>$value</div";
        if ($key == 'soal_template') {
          $value .= "
            <div>
              <div class='flexy gap1'>
                <div class='red hover'>❌</div>
                <div class='hover'>🔄</div>
                <div class='hover'>🖼️</div>
              </div>
            </div>
          ";
        }
      } elseif (substr($key, 0, 5) == 'wrong') {
        $value = "<span class='red'>$value</span";
      }
      if ($i == 1) {
        $kolom = key2kolom($key);
        $th .= "<th>$kolom</th>";
      }
      $td .= "<td>$value</td>";
    }
    $tr .= "
      <tr>
        $td
      </tr>
    ";
  }
}

$materi['kelas'] = $materi['kelas'] ?? '❓';

$title = "Manage Soal $materi[singkatan_mapel] $materi[nama_jenjang]  kelas $materi[kelas]";
set_title($title);
echo "
  <h2>$title</h2>
  <div>
  <a href=?manage_materi&jenjang=$materi[jenjang]>⬅️</a> 
  <b>Materi</b>: <span class='f40 blue'>$materi[nama_materi]</span> - id. $materi[id_materi]
  </div>
  <table class=table>
    <thead>$th</thead>
    $tr
  </table>
";
