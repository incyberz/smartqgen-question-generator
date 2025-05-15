<?php
# ============================================================
# PROCESS
# ============================================================
if (isset($_POST['btn_start_quiz'])) {
  $nama = ucwords(strtolower($_POST['nama']));

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
  echo '<pre>';
  print_r($pengunjung);
  echo '<b style=color:red>Developer SEDANG DEBUGING: exit(true)</b></pre>';
  exit;
}



if ($id_pengunjung) {
  include 'quiz-started.php';
} else {
?>

  <div id="welcome">
    <h1>Selamat Datang di <span style="color:#00bcd4;">SmartQGen</span></h1>
    <p class="mb4">Soal Dinamis, Evaluasi Otomatis!</p>

    <div class="tengah">
      <img src="assets/img/logo.png" alt="logo" class="logo">
    </div>

    <form method="POST">
      <input class="mt3 f22 tengah proper" type="text" name="nama" id="nama" required placeholder="Nama kamu..." autocomplete="off" minlength="3" maxlength="20">
      <button type="submit" name=btn_start_quiz><span class="f18">Start Quiz</span></button>
    </form>
  </div>
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
    })
  </script>
<?php }
