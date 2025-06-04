<?php
if ($username) jsurl('?dashboard');
$pesan_login = '';
include 'login-process.php';

?>
<style>
  .blok-login {
    display: flex;
    align-items: center;
    min-height: 90vh;
  }
</style>
<div class="blok-login">
  <form method=post class="wadah w-500">
    <h2 class="mb2 tengah">Login</h2>
    <img src="assets/img/hero.webp" alt="jajansoal-logo" class="logo">
    <p class="mb4 orange mt3">Jawab Soal dapat Uang Jajan</p>

    <div class="mb2 red"><?= $pesan_login ?></div>
    <input required
      class="mb2 tengah"
      type="text"
      id="username"
      name="username"
      placeholder="Masukan username..."
      maxlength="20" />
    <input required
      class="mb2 tengah"
      type="password"
      id="password"
      name="password"
      placeholder="Masukan password..."
      maxlength="20" />
    <div class="login-error red mb2"></div>
    <button class='btn btn-primary w-100 mb2' id=btn_login name=btn_login>Login</button>
    <a href=? class="btn btn-secondary w-100">
      ⚙️ Back to Home
    </a>
  </form>
</div>

<script>
  $(function() {
    $("#username").keyup(function() {
      $(this).val(
        $(this).val()
        .trim()
        .toLowerCase()
        .replace(/[^a-z` ]/g, "")
      );
    });

  })
</script>