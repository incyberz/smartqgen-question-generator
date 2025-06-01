<style>
  .blok-opsi {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .label-mapel,
  .label-jenjang,
  .label-jumlah {
    display: block;
  }

  label input[type=radio] {
    display: none;
  }

  .label-mapel-active,
  .label-jenjang-active,
  .label-jumlah-active {
    background-color: #0d6efd;
    color: white;
  }
</style>
<?php
$jenjang = [
  'SD' => [
    'active' => 'label-jenjang-active',
    'bg' => 'success',
  ],
  'SMP' => [
    'active' => '',
    'bg' => 'primary',
  ],
  'SMA' => [
    'active' => '',
    'bg' => 'warning',
  ],
  'UNIV' => [
    'active' => '',
    'bg' => 'danger',
  ],
];

$opsi_jenjang = '';
foreach ($jenjang as $k => $v) {
  $opsi_jenjang .= "
    <label class='label-jenjang $v[active] btn-outlined btn-outlined-$v[bg] '>
      <input type='radio' name='jenjang' id='jenjang--$k' value='$k' > $k
    </label>
    ";
}

$s = "SELECT nama_mapel as mapel FROM tb_mapel ORDER BY mapel ASC";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opsi_mapel = "
  <label class='label-mapel label-mapel-active btn-outlined btn-outlined-primary '>
    <input type='radio' name='mapel' id='mapel--random' value='random' checked> Random
  </label>
";
while ($d = mysqli_fetch_assoc($q)) {
  $opsi_mapel .= "
    <label class='label-mapel btn-outlined btn-outlined-primary '>
      <input type='radio' name='mapel' id='mapel--$d[mapel]' value='$d[mapel]' > $d[mapel]
    </label>
  ";
}

$opsi_jumlah = '';
for ($i = 1; $i <= 5; $i++) {
  $active = $i == 1 ? 'label-jumlah-active' : '';
  $jumlah_soal = $i * 5;
  $opsi_jumlah .= "
    <label class='label-jumlah $active btn-outlined btn-outlined-primary '>
      <input type='radio' name='jumlah-soal' id='jumlah-soal--$jumlah_soal' value='$jumlah_soal' > $jumlah_soal
    </label>
  ";
}

$opsi_quiz = "
  <div class='blok-opsi blok-opsi-jenjang mb2 border-top mt4 pt4'>
    $opsi_jenjang
  </div>
  <div class='blok-opsi blok-opsi-mapel pt4 mb2'>
    $opsi_mapel
  </div>
  <div class='blok-opsi blok-opsi-jumlah-soal pt4'>
    $opsi_jumlah
  </div>
  <div class='red'>Sorry! Quiz Options in development.</div>
";
?>
<script>
  $(function() {
    $(".label-jenjang").click(function() {
      $(".label-jenjang").removeClass('label-jenjang-active');
      $(this).addClass('label-jenjang-active');
    });
    $(".label-mapel").click(function() {
      $(".label-mapel").removeClass('label-mapel-active');
      $(this).addClass('label-mapel-active');
    });
    $(".label-jumlah").click(function() {
      $(".label-jumlah").removeClass('label-jumlah-active');
      $(this).addClass('label-jumlah-active');
    });
  })
</script>