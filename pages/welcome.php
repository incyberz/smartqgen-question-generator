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

  include 'opsi_quiz.php';

























?>
  <script>
    localStorage.removeItem('jawaban_kuis_encrypted');
    localStorage.removeItem('soalList');
  </script>

  <form method="POST" class=form-welcome>
    <h1 class="hideit">Selamat Datang di JajanSoal! Dengan Menjawab Soal Kuis kamu akan dapat Uang Jajan.</h1>

    <div class="tengah blok-welcome-logo">
      <div class="f24 orange bold mb4">Selamat Datang di </div>
      <img src="assets/img/hero.webp" alt="jajansoal-logo" class="logo">
      <p class="mb4 orange mt3">Jawab Soal dapat Uang Jajan</p>
    </div>

    <div class='blok-opsies-and-login hideit'>
      <div class="blok-opsies">
        <?= $opsi_quiz ?>
      </div>

      <div class="mt4 pt4 border-top">
        <a href=?register class="btn btn-secondary">
          Register 👉
        </a>
        <a href=?login class="btn btn-secondary">
          Login 👉
        </a>

      </div>


    </div>


    <div class="welcome-blok-bawah">
      <input class="mt3 f22 tengah proper mb3" type="text" name="nama" id="nama" required placeholder="Nama kamu..." autocomplete="off" minlength="3" maxlength="20">
      <span class="btn btn-secondary" id=span-opsi>
        ⚙️ Opsi
      </span>
      <button type="submit" name=btn_start_quiz class="btn btn-primary"><span class="f18">🚀 Start Quiz</span></button>
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
        $(".blok-opsies-and-login").slideToggle();
        $(".blok-welcome-logo").slideToggle();
      });

    })
  </script>
<?php }
