<style>
  .blok-opsi {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
  }

  label input[type=radio] {
    display: none;
  }

  .mapel-active,
  .jenjang-active,
  .label-jumlah-active {
    background-color: #0d6efd;
    color: white;
  }
</style>
<?php
$jenjang_default = $user['jenjang'] ?? 'SD';

$s = "SELECT * FROM tb_jenjang ORDER BY urutan";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opsi_jenjang = '';
while ($d = mysqli_fetch_assoc($q)) {
  $jenjang = $d['jenjang'];
  $active = $d['jenjang'] == $jenjang_default ? 'jenjang-active' : '';
  $opsi_jenjang .= "
    <label class='label-jenjang label-jenjang--$jenjang $active btn-outlined btn-outlined-$d[bg]' id='jenjang--$jenjang' >
      <input class=jenjang type='radio' name='jenjang' id='radio--$jenjang' value='$jenjang' > $d[nama_jenjang]
    </label>
  ";
}












# ============================================================
# MAPEL DB
# ============================================================
$s = "SELECT * FROM tb_mapel ORDER BY nama_mapel ASC";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opsi_mapel = "
  <label class='label-mapel label-mapel--random mapel-active btn-outlined btn-outlined-primary '>
    <input class=mapel type='radio' name='mapel' id='mapel--random' value='random' checked> Random
  </label>
";
while ($d = mysqli_fetch_assoc($q)) {
  $hideit = $d['jenjang'] == $jenjang_default ? '' : 'hideit';
  $opsi_mapel .= "
    <div class='$hideit mapel-custom mapel--$d[jenjang]'>
      <label class='label-mapel label-mapel--$d[id] btn-outlined btn-outlined-primary '>
        <input class=mapel type='radio' name='mapel' id='mapel--$d[id]' value='$d[id]' > $d[singkatan]
      </label>
    </div>
  ";
}








# ============================================================
# JUMLAH SOAL
# ============================================================
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











# ============================================================
# PAID PAKET SOAL
# ============================================================
$paid_paket = [];
$suspended_paket = []; // paid until masih ada namun status 0
if (isset($kelas) and $kelas) {
  $s = "SELECT 
  a.id,
  a.diskon,
  a.paid_until,
  a.status,
  a.verif_by,
  a.verif_date,
  b.nama_paket

  FROM tb_paid a 
  JOIN tb_paket_soal b ON a.id_paket=b.id 
  WHERE a.pembeli = '$kelas[username_ortu]'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    $id = $d['id'];
    if (strtotime($d['paid_until'] < strtotime('now'))) {
      $s = "UPDATE tb_paid SET status=0 WHERE id=$id";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    } else {
      if ($d['status']) {
        $paid_paket[$id] = $d;
      } else {
        $suspended_paket[$id] = $d;
      }
    }
  }
}

// echo '<pre>';
// print_r($paid_paket);
// echo '</pre>';












# ============================================================
# FINAL OUTPUT
# ============================================================
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

if ($param == 'opsi_quiz') echo $opsi_quiz;
?>
<script>
  $(function() {
    $(".jenjang").click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let jenjang = rid[1];
      console.log(aksi, jenjang);
      $(".label-jenjang").removeClass('jenjang-active');
      $('.label-jenjang--' + jenjang).addClass('jenjang-active');

      $('.mapel-custom').hide();
      $('.mapel--' + jenjang).slideDown();

      // default random
      $('#mapel--random').click();
    });

    $(".mapel").click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id = rid[1];
      console.log(aksi, id);

      $(".label-mapel").removeClass('mapel-active');
      $('.label-mapel--' + id).addClass('mapel-active');
    });

    $(".label-jumlah").click(function() {
      $(".label-jumlah").removeClass('label-jumlah-active');
      $(this).addClass('label-jumlah-active');
    });
  })
</script>