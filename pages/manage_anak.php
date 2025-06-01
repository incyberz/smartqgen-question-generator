<?php
akses('manage_kelas');
$get_id_kelas = $_GET['id_kelas'] ?? '';

$pesan_error = '';
$post_username_anak = $_POST['username_anak'] ?? '';
$post_tanggal_lahir = $_POST['tanggal_lahir'] ?? '';

include 'user-process.php';
if (!$user['jumlah_kelas']) {
  set_h2('Tambah Kelas', 'Anda belum punya kelas, silahkan buat dahulu untuk mewadahi kegiatan belajar!');
?>
  <form method=post class="wadah left mt3">
    <div class="mb3">
      <div class="f14 mb2">Nama Kelas </div>
      <input required type="text" minlength="5" maxlength="30" name="nama_kelas" id="nama_kelas" autocomplete="off" placeholder="Contoh: Kelas SD, Homeschooling Albaiti">
    </div>
    <button class="btn btn-primary w-100">Tambah Kelas</button>
  </form>
  <script>
    $(function() {
      $('#nama_kelas').keyup(function() {
        // buat title case
        $(this).val($(this).val().replace(/^\w/, c => c.toUpperCase()))
      })
    })
  </script>

<?php

} else {
  // get kelas
  $sql_id_kelas = $get_id_kelas ? "id=$get_id_kelas" : '1';
  $s = "SELECT * FROM tb_kelas WHERE username = '$username' AND $sql_id_kelas";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q) > 1) {
    $links = '';
    while ($d = mysqli_fetch_assoc($q)) {
      $links .= "<div class='border-top mt2 pt2'><a href=?manage_kelas&id_kelas=$d[id]>👉 Manage Kelas $d[nama_kelas]</a></div>";
    }
    echo "
      <div class='ortu w-500 left'>
        Mana yang ingin Anda manage?
        $links
      </div>
    ";
    exit;
  } else {
    $kelas = mysqli_fetch_assoc($q);
  }

  $show_pesan_error = !$pesan_error ? '' : "<div class='alert alert-danger'>$pesan_error</div>";
  // set_h2('Tambah Anak', "Menambahkan Putra/Putri Anda ke Grup Kelas yang Anda buat $show_pesan_error");

  // select peserta kelas
  $s = "SELECT 
  a.id_kelas, 
  b.username, 
  b.nama as nama_peserta 
  FROM tb_peserta a 
  JOIN tb_user b ON a.username=b.username
  WHERE a.id_kelas = $kelas[id]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  // $tb_peserta = "<div class='alert f12 miring abu '>belum ada peserta pada kelas ini.</div>";
  $tb_peserta = '';
  $primary = 'primary'; // button tambah anak
  $caption = 'Tambahkan'; // button tambah anak
  if (mysqli_num_rows($q)) {
    $primary = 'secondary'; // button tambah anak
    $caption = 'Tambahkan Lagi'; // button tambah anak
    $tr = '';
    $i = 0;
    while ($d = mysqli_fetch_assoc($q)) {
      $i++;
      $tr .= "
        <tr id=tr--$d[id_kelas]--$d[username]>
          <td>$i</td>
          <td>$d[nama_peserta]</td>
          <td>
            <span class='btn-drop btn btn-danger btn-sm' id=btn-drop--$d[id_kelas]--$d[username]>Drop</span>
          </td>
        </tr>
      ";
    }
    $tb_peserta = "
      <h2 class='pt3 mt4 f18'>Tabel Peserta Kelas</h2>
      <p><b>Kelas</b>: $kelas[nama_kelas]</p>
      <table>
        <thead>
          <th>No</th>
          <th>Nama</th>
          <th>Aksi</th>
        </thead>
        $tr
      </table>
      <a href=? class='btn w-100 mt3 btn-primary'>Back to Home</a>
    ";
  }


?>
  <div class="w-500 ortu">
    <?= $tb_peserta ?>
    <form method=post class="wadah left mt4">
      <div class="mb3">
        <div class="f14 mb2">Username Anak Anda</div>
        <input type="hidden" value="<?= $kelas['id'] ?>" name=id_kelas>
        <input required type="text" name="username_anak" id="username_anak" autocomplete="off" value="<?= $post_username_anak ?>" placeholder="Contoh: ahmad, salwa, yusuf">
      </div>
      <div class="mb3">
        <div class="f14 mb2">Tanggal Lahir</div>
        <input required type="date" name="tanggal_lahir" id="tanggal_lahir" autocomplete="off" min='2000-01-01' max='2023-01-01' value="<?= $post_tanggal_lahir ?>">
      </div>
      <button class="btn btn-<?= $primary ?> w-100" name=btn_tambah_peserta><?= $caption ?></button>
    </form>


  </div>
<?php
}


























?>
<script>
  $(function() {
    $('.btn-drop').click(function() {

      let y = confirm('Yakin Drop anak ini?');
      if (!y) return;

      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id_kelas = rid[1];
      let username = rid[2];
      console.log(aksi, id_kelas, username);

      let link_ajax = `ajax/ajax-drop_peserta_kelas.php?id_kelas=${id_kelas}&username=${username}`;

      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'OK') {
            $('#tr--' + id_kelas + '--' + username).fadeOut();
          } else {
            alert(a);
          }
        }
      })


    })
  })
</script>