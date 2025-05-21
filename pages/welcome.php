<link rel="stylesheet" href="./assets/css/btn-outlined.css">
<style>
  .form-welcome {
    position: relative;
    height: 100%;
  }

  .blok-bawah {
    position: absolute;
    bottom: 0;
    width: 100%;
  }

  .blok-opsi {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .label-mapel,
  .label-tingkat,
  .label-jumlah {
    display: block;
  }

  label input[type=radio] {
    display: none;
  }

  .label-mapel-active,
  .label-tingkat-active,
  .label-jumlah-active {
    background-color: #0d6efd;
    color: white;
  }
</style>
<?php
# ============================================================
# PROCESS
# ============================================================
if (isset($_POST['btn_start_quiz'])) {
  $nama = ucwords(preg_replace('/[^a-z `]/', '', strtolower($_POST['nama'])));

  if (!$id_pengunjung) {
    $s = "INSERT INTO tb_pengunjung (nama) VALUES ('$nama')";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  $s = "SELECT * FROM tb_pengunjung WHERE nama = '$nama' ORDER BY created_at DESC LIMIT 1";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $pengunjung = mysqli_fetch_assoc($q);
  $id_pengunjung = $pengunjung['id'];
  $_SESSION['qgen_id_pengunjung'] = $id_pengunjung;
  jsurl();
}



if ($id_pengunjung) {
  include 'quiz-started.php';
} else {

  $tingkat = [
    'SD' => [
      'active' => 'label-tingkat-active',
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

  $opsi_tingkat = '';
  foreach ($tingkat as $k => $v) {
    $opsi_tingkat .= "
      <label class='label-tingkat $v[active] btn-outlined btn-outlined-$v[bg] '>
        <input type='radio' name='tingkat' id='tingkat--$k' value='$k' > $k
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

























?>
  <form method="POST" class=form-welcome>
    <h1>Selamat Datang di <span style="color:#00bcd4;">SmartQGen</span></h1>
    <p class="mb4">Soal Dinamis, Evaluasi Otomatis!</p>

    <div class="tengah blok-logo">
      <img src="assets/img/logo.png" alt="logo" class="logo">
    </div>

    <div class="blok-opsies hideit">
      <div class="blok-opsi blok-opsi-tingkat mb2 pb4 border-bottom border-top mt4 pt4">
        <?= $opsi_tingkat ?>
      </div>
      <div class="blok-opsi blok-opsi-mapel pt4">
        <?= $opsi_mapel ?>
      </div>
      <div class="blok-opsi blok-opsi-jumlah-soal pt4">
        <?= $opsi_jumlah ?>
      </div>
      <div class="blok-login pt4">
        <span class="btn btn-sm btn-secondary w-100">Login</span>
      </div>
    </div>

    <div class="blok-bawah">
      <input class="mt3 f22 tengah proper" type="text" name="nama" id="nama" required placeholder="Nama kamu..." autocomplete="off" minlength="3" maxlength="20">
      <span class="btn btn-secondary" id=span-opsi>
        ⚙️ Opsi
      </span>
      <button type="submit" name=btn_start_quiz><span class="f18">🚀 Start Quiz</span></button>
    </div>
  </form>





























  <script>
    $(function() {
      $("#nama").keyup(function() {
        $(this).val(
          $(this).val()
          .replace(/'/g, "`") // Ubah tanda petik menjadi backtick
          .replace(/[^a-zA-Z` ]/g, "") // Hanya huruf, spasi, dan tanda backtick
          .replace("  ", " ") // Dilarang double spasi
          .toLowerCase()
        ); // Ubah ke uppercase
      });
      $("#span-opsi").click(function() {
        $(".blok-opsies").slideToggle();
        $(".blok-logo").slideToggle();
      });
      $(".label-tingkat").click(function() {
        $(".label-tingkat").removeClass('label-tingkat-active');
        $(this).addClass('label-tingkat-active');
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
<?php }
