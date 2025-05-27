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

  include 'opsi_quiz.php';

























?>
  <script>
    localStorage.removeItem('jawaban_kuis_encrypted');
    localStorage.removeItem('soalList');
  </script>

  <form method="POST" class=form-welcome>
    <h1>Selamat Datang di <span style="color:#00bcd4;">SmartQGen</span></h1>
    <p class="mb4">Soal Dinamis, Evaluasi Otomatis!</p>

    <div class="tengah blok-logo">
      <img src="assets/img/logo.png" alt="logo" class="logo">
    </div>

    <div class='blok-opsies-and-login hideit'>
      <div class="blok-opsies">
        <?= $opsi_quiz ?>
      </div>

      <div class="mt4 pt4 border-top">
        <a href=?register class="btn btn-secondary">
          Register 👉
        </a>
        <span class="btn btn-secondary" id=show-login>
          Login 👉
        </span>
        <div id="login" class="hideit blok-login mt3 wadah">
          <input class="mb2 tengah" type="text" id="input-username" placeholder="Masukan username..." maxlength="20">
          <input class="mb2 tengah" type="password" id="input-password" placeholder="Masukan password..." maxlength="20">
          <div class="login-error red mb2"></div>
          <span class='btn btn-primary w-100 mb2' id=btn_login>Login</span>
          <span class="btn btn-secondary w-100" id=back-to-opsi>
            ⚙️ Back to Opsi
          </span>
        </div>

      </div>


    </div>


    <div class="blok-bawah">
      <input class="mt3 f22 tengah proper mb3" type="text" name="nama" id="nama" required placeholder="Nama kamu..." autocomplete="off" minlength="3" maxlength="20">
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
        $(".blok-opsies-and-login").slideToggle();
        $(".blok-logo").slideToggle();
      });

      $("#show-login").click(function() {
        $("#show-login").slideUp();
        $(".blok-login").slideDown();
        $(".blok-opsies").slideUp();
      });

      $("#back-to-opsi").click(function() {
        $("#show-login").slideDown();
        $(".blok-login").slideUp();
        $(".blok-opsies").slideDown();
      });

      // =================================================
      // LOGIN HANDLER
      // =================================================
      $("#input-username").keyup(function() {
        $(this).val(
          $(this).val()
          .trim()
          .toLowerCase()
          .replace(/[^a-z` ]/g, "")
        );
      });

      $("#btn_login").click(function() {
        let username = $('#input-username').val()
          .trim()
          .toLowerCase()
          .replace(/[^a-z]/g, '');
        let password = $('#input-password').val();
        if (!username) {
          $('#input-username').focus();
          return;
        } else if (!password) {
          $('#input-password').focus();
          return;
        }

        $.ajax({
          url: `ajax/ajax-login-process.php?username=${username}&password=${password}`,
          success: function(a) {
            if (a.trim() == 'OK') {
              location.replace('?dashboard');
            } else {
              $('.login-error').html(a);
            }
          }
        })
      });
    })
  </script>
<?php }
