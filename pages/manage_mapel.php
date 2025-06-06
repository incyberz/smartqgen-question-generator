<?php
akses('manage_mapel');
include 'includes/key2kolom.php';
$is_admin = $user['is_admin'];


# ============================================================
# PROCESS
# ============================================================
if (isset($_POST['btn_add_mapel'])) {
  $jenjang = $_POST['btn_add_mapel'];
  $nama_mapel = $_POST['nama_mapel'];
  $singkatan = $_POST['singkatan'];

  $nama_mapel = ucwords(preg_replace('/[^a-z0-9 ]/', '', strtolower($nama_mapel)));
  $singkatan = preg_replace('/[^a-z0-9]/', '', strtolower($singkatan));

  $s = "SELECT MAX(urutan)+1 as urutan FROM tb_mapel WHERE jenjang='$jenjang'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);
  $urutan = $d['urutan'] ?? 1;

  $s = "INSERT INTO tb_mapel (
    jenjang,
    nama_mapel,
    singkatan,
    urutan,
    created_by
  ) VALUES (
    '$jenjang',
    '$nama_mapel',
    '$singkatan',
    '$urutan',
    '$username'
  )";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
} elseif (isset($_POST) and $_POST) {
  echo '<pre>';
  print_r($_POST);
  echo '<b style=color:red>Belum ada handler untuk Manage Mapel POST diatas.</b></pre>';
  exit;
}


# ============================================================
# NAVIGASI JENJANG
# ============================================================
$s = "SELECT * FROM tb_jenjang ORDER BY urutan";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$nav = '';
$i = 0;
$cnama_jenjang = '';
$cjenjang = '';
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $btn_type = '';
  if ($i == 1) {
    $cjenjang = $d['jenjang'];
    $cnama_jenjang = $d['nama_jenjang'];
    $btn_type = 'btn-primary';
  }
  $nav .= "<div><button class='btn btn-sm btn-secondary $btn_type btn-nav' id=btn-nav--$d[jenjang]>$d[nama_jenjang]</button></div>";
}
echo "<div class='flex-center gap1'>$nav</div>";


$s = "SELECT 
a.urutan as No,
a.*,
b.nama_jenjang,
(SELECT COUNT(1) FROM tb_materi WHERE id_mapel=a.id) materi 
FROM tb_mapel a 
JOIN tb_jenjang b ON a.jenjang=b.jenjang 
ORDER BY b.urutan, a.urutan, a.nama_mapel
";
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
        || $key == 'urutan'
        || $key == 'jenjang'
        || $key == 'nama_jenjang'
        || $key == 'created_at'
        || $key == 'created_by'
      ) {
        continue;
      } elseif ($key == 'status') {
        $value = $value ? '✅' : '❌';
      } elseif ($key == 'materi') {
        $li_materi = '';
        if ($value) {
          $s2 = "SELECT * FROM tb_materi WHERE id_mapel=$d[id]";
          $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
          while ($d2 = mysqli_fetch_assoc($q2)) {
            $li_materi .= "<li>$d2[nama_materi]</li>";
          }
        }
        $value = "<ol class='f12 pl3'>$li_materi<li><a href='?add_materi&id_mapel=$d[id]'>... Add Materi</a></li></ol>";
      }
      if ($i == 1) {
        $kolom = key2kolom($key);
        $th .= "<th>$kolom</th>";
      }
      $td .= "<td>$value</td>";
    }

    $hide_tr = $cnama_jenjang == $d['nama_jenjang'] ? '' : 'hideit';

    $tr .= "
      <tr class='$hide_tr tr--mapel tr--$d[nama_jenjang]'>
        $td
        <td>Hapus</td>
      </tr>
    ";
  }
}


$tb = $tr ? "
  <table class=table>
    <thead>$th<th>Aksi</th></thead>
    $tr
    <tr>
      <td>
        #
      </td>
      <td colspan=100%>
        <form method=post class='flexy gap1'>
          <div>
            <input 
              type=text
              required 
              name=nama_mapel 
              minlength='3' 
              maxlength='50' 
              placeholder='Nama mapel...'
            />
          </div>
          <div>
            <input 
              type=text
              required 
              name=singkatan 
              minlength='2' 
              maxlength='10' 
              placeholder='Singkatan...'
            />
          </div>
          <div>
            <button name=btn_add_mapel id=btn_add_mapel value='$cjenjang'>Add Mapel <span class=cnama_jenjang>$cnama_jenjang</span></button>
          </div>
        </form>
      </td>
    </tr>
  </table>
" : alert("Data mapel tidak ditemukan.");
echo "$tb";
?>
<script>
  $(function() {
    $('.btn-nav').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let jenjang = rid[1];
      let nama_jenjang = $(this).text();
      console.log(aksi, jenjang, nama_jenjang);
      $('#btn_add_mapel').text('Add Mapel ' + nama_jenjang);
      $('#btn_add_mapel').val(jenjang);

      $('.btn-nav').removeClass('btn-primary');
      $(this).addClass('btn-primary');

      $('.tr--mapel').hide();
      $('.tr--' + nama_jenjang).show();

    })
  })
</script>