<?php
$s = "SELECT *,
a.id as id_materi,
b.singkatan as singkatan_mapel 
FROM tb_materi a 
JOIN tb_mapel b ON a.id_mapel=b.id
JOIN tb_jenjang c ON b.jenjang=c.jenjang
WHERE a.id = $get_id_materi
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$materi = mysqli_fetch_assoc($q);
if (!$materi) kosong('materi @info_materi');
$tr = '';
foreach ($materi as $key => $value) {
  if (
    $key == 'id'
    || $key == 'date_created'
  ) continue;

  $kolom = key2kolom($key);
  $tr .= "
    <tr>
      <td>$kolom</td>
      <td>$value</td>
    </tr>
  ";
}

$info_materi = $tr ? "
  <table class=table>
    $tr
  </table>
" : alert("Data materi tidak ditemukan.", 'danger', null, false);
