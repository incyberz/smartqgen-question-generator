<?php
if ($username) jsurl('?dashboard');
$pengunjung['nama'] = $pengunjung['nama'] ?? '';
$post_nama = $_POST['nama'] ?? $pengunjung['nama'];
$post_whatsapp = $_POST['whatsapp'] ?? null;
$post_username = $_POST['username'] ?? null;
$post_sebagai = $_POST['sebagai'] ?? null;


# ============================================================
# PROCESS
# ============================================================
if (isset($_POST['btn_submit'])) {
  $username = preg_replace('/^a-z0-9/', '', strtolower($_POST['username']));
  $nama = preg_replace('/^A-Z `/', '', strtoupper($_POST['nama']));
  $whatsapp = preg_replace('/^0-9/', '', $_POST['whatsapp']);
  $role = $_POST['sebagai'] == 'pelajar' ? 1 : 2;

  # ============================================================
  # ID PENGUNJUNG
  # ============================================================
  if (!$id_pengunjung) { // langsung register
    $s = "INSERT INTO tb_pengunjung (nama) VALUES ('$nama')";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $id_pengunjung = mysqli_insert_id($cn);
  }

  $s = "INSERT INTO tb_user (
    username,
    nama,
    whatsapp,
    role,
    id_pengunjung
  ) VALUES (
    '$username',
    '$nama',
    '$whatsapp',
    '$role',
    $id_pengunjung
  )";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $_SESSION['qgen_username'] = $username;
  $_SESSION['qgen_id_pengunjung'] = $id_pengunjung;
  jsurl('?');
}
?>
<style>
  .form-label {
    font-size: 13px;
    color: #ccc;
    text-align: left;
    margin: 10px 0 5px 0;
  }

  .input-info {
    display: none;
    font-size: 12px;
    color: #aaa;
    text-align: left;
    margin-top: 5px;
    color: brown;
  }

  .blok-sebagai {
    display: grid;
    grid-template-columns: 50% 50%;
    border: solid 1px #ccc;
    border-radius: 10px;
    padding: 30px 0 15px 0;
    position: relative;
    margin-top: 40px;
  }

  .blok-sebagai input[type=radio] {
    display: none;
  }

  .saya-sebagai {
    position: absolute;
    top: -20px;
    background: #55f;
    color: white;
    left: 50%;
    transform: translateX(-50%);
    padding: 5px 15px;
    border-radius: 10px;
    border: solid 1px #ccc;
  }

  .img-icon {
    width: 100px;
    height: 100px;
    object-fit: cover;
    background: white;
    border-radius: 50%;
    cursor: pointer;
    transition: .3s;
  }

  .img-icon:hover {
    transform: scale(1.1);
  }

  .img-icon-active {
    width: 103px;
    height: 103px;
    border: solid 3px blue;
    box-shadow: 0 0 15px white;
  }
</style>
<form method="post" class='w-500'>
  <h3>Register</h3>
  <p>Masukan data kamu agar dapat menyimpan dan menukarkan poin belajar.</p>
  <div class="mb3">
    <label for="whatsapp" class="form-label">WhatsApp Aktif</label>
    <input type="text" class="input-form" id="whatsapp" name=whatsapp placeholder="Masukkan No. WhatsApp" required minlength="11" maxlength="14" value="<?= $post_whatsapp ?>" autocomplete="off">
    <div class="input-info input-info-info" id=whatsapp--info>Gunakanlah whatsapp aktif agar dapat melakukan verifikasi registrasi dan menerima info penting lainnya.</div>
  </div>
  <div class="mb3">
    <label for="username" class="form-label">Username</label>
    <input type="text" class="input-form" id="username" name=username placeholder="Masukkan Username..." required minlength="3" maxlength="20" value="<?= $post_username ?>">
    <div class="red left" id=username_error></div>
    <div class="input-info input-info-info" id=username--info>*) tanpa special character <br>*) password default sama dengan username</div>
  </div>
  <div class="mb3">
    <label for="nama" class="form-label">Nama Lengkap</label>
    <input type="text" class="input-form" id="nama" name=nama placeholder="Masukkan Nama..." required minlength="3" maxlength="30" value="<?= $post_nama ?>">
    <div class="input-info input-info-info" id=nama--info>*) a-z only</div>
  </div>
  <div class="mb3">
    <div class="blok-sebagai">
      <div class="saya-sebagai">Saya sebagai:</div>
      <label>
        <input required type="radio" name="sebagai" id="sebagai--pelajar" value=pelajar>
        <img id=img-icon--pelajar class="img-icon" src="./assets/img/pelajar.png" alt="img-pelajar">
        <div>Pelajar</div>
      </label>
      <label>
        <input required type="radio" name="sebagai" id="sebagai--pengajar" value=pengajar>
        <img id=img-icon--pengajar class="img-icon" src="./assets/img/pengajar.png" alt="img-pengajar">
        <div>Pengajar/Ortu</div>
      </label>

    </div>
  </div>

  <button type="submit" class="btn btn-primary w-100" name=btn_submit>Submit</button>
</form>
<div class="tengah mt3">
  Sudah punya akun? Silahkan <a href="?login_pmb">login</a>.
</div>

<script>
  $(document).ready(function() {
    $("input[type=radio]").change(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let sebagai = rid[1];

      $('.img-icon').removeClass('img-icon-active');
      $('#img-icon--' + sebagai).addClass('img-icon-active');
    });

    $(".input-form").focus(function() {
      let id = $(this).prop('id');
      $('.input-info').slideUp();
      $('#' + id + '--info').slideDown();
    });

    $("#nama").on("keyup", function() {
      let val = $(this).val();
      val = val.replace(/'/g, "`"); // Ubah tanda petik menjadi backtick
      val = val.replace(/[^a-zA-Z` ]/g, ""); // Hanya huruf, spasi, dan tanda backtick
      $(this).val(val.toUpperCase()); // Ubah ke uppercase
    });


    $("#whatsapp").on("keyup", function() {
      let val = $(this).val();
      val = val.replace(/[^0-9]/g, ""); // Hanya angka
      if (val.startsWith("08")) {
        val = "628" + val.substring(2);
      } else if (!val.startsWith("628") && val.length >= 4) {
        val = "";
      }
      $(this).val(val);
    });

    $("#whatsapp").focus(function() {
      $('#whatsapp_info').slideDown();
    });

    $("#whatsapp").focusout(function() {
      $('#whatsapp_info').slideUp();
    });

    $("#username").on("keyup", function() {
      let val = $(this).val();
      val = val.replace(/[^a-zA-Z0-9]/g, ""); // Hanya huruf kecil dan angka
      $(this).val(val.toLowerCase()); // Ubah ke lowercase
    });
    $("#username").focusout(function() {
      let username = $(this).val();
      let link_ajax = "ajax/ajax-cek_available_username.php?username=" + username;
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'OK') {
            $('#username_error').text('');
          } else {
            $('#username').val('');
            $('#username_error').text(a);
          }
        }
      })
    });
  });
</script>