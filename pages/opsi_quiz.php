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
$paids = '';
if (isset($kelas) and $kelas) {
  $s = "SELECT 
  a.id,
  a.diskon,
  a.paid_until,
  a.status,
  a.verif_by,
  a.verif_date,
  b.nama_paket,
  (SELECT COUNT(1) FROM tb_soal WHERE id_paket=b.id) jumlah_soal

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
        $expire = tanggal($d['paid_until']);
        $paids .= "
          <div class='wadah border-biru gradasi-hijau' >
            <div class='' style='display:grid; grid-template-columns:auto 15%; gap:15px'>
              <div>
                <div class='blue'>$d[nama_paket]</div>
              </div>
              <div>
                <a href=?quiz_started&id_paket=$d[id]>
                  <button class='btn btn-primary btn-sm'>Play</button>
                </a>
              </div>
            </div>
            <div class='f12 abu flex-between'>
              <div>hingga: $expire</div>
              <div class=''>$d[jumlah_soal] soal</div>
            </div>
          </div>
        ";
      } else {
        $suspended_paket[$id] = $d;
      }
    }
  }
}













# ============================================================
# FINAL OUTPUT
# ============================================================
$opsi_quiz = "
  <h2>Paket Tersedia</h2>
  <div class=left>
    $paids
  </div>


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