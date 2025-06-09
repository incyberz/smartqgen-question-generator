<?php
akses('manage_materi');
include 'includes/key2kolom.php';
$is_admin = $user['is_admin'];
$get_jenjang = $_GET['jenjang'] ?? 'SD';
include 'option_kelas.php';


# ============================================================
# PROCESS
# ============================================================
include 'manage_materi-process.php';


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
  if ($get_jenjang == $d['jenjang']) {
    $cjenjang = $d['jenjang'];
    $cnama_jenjang = $d['nama_jenjang'];
    $item_nav = "<button class='btn btn-sm btn-primary'>$d[nama_jenjang]</button>";
  } else {
    $item_nav = "<a href=?manage_materi&jenjang=$d[jenjang]><button class='btn btn-sm btn-secondary'>$d[nama_jenjang]</button></a>";
  }
  $nav .= "<div>$item_nav</div>";
}
echo "<div class='flex-center gap1 gradasi-kuning' style='z-index:3; position:sticky; top:0; padding:15px; margin:-15px;'>$nav</div>";


$s = "SELECT 
a.urutan as No,
a.*,
b.nama_jenjang,
(SELECT COUNT(1) FROM tb_materi WHERE id_mapel=a.id) materi 
FROM tb_mapel a 
JOIN tb_jenjang b ON a.jenjang=b.jenjang 
WHERE a.jenjang = '$get_jenjang'
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
        || $key == 'status'
        || $key == 'nama_mapel'
      ) {
        continue;
        // } elseif ($key == 'status') {
        //   $value = $value ? '✅' : '❌';
      } elseif ($key == 'materi') {
        $li_materi = '';
        if ($value) {
          $s2 = "SELECT *,
          (SELECT COUNT(1) from tb_soal WHERE id_materi=a.id) jumlah_soal 
          FROM tb_materi a 
          WHERE a.id_mapel=$d[id] 
          ORDER BY kelas, nama_materi
          ";
          $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
          $urutan = 0;
          while ($d2 = mysqli_fetch_assoc($q2)) {
            $urutan++;
            if (!$d2['urutan']) {
              # ============================================================
              # AUTO INSERT URUTAN BY ORDER A-Z
              # ============================================================
              $s3 = "UPDATE tb_materi SET urutan=$urutan WHERE id=$d2[id]";
              mysqli_query($cn, $s3) or die(mysqli_error($cn));
            }
            $red = $d2['jumlah_soal'] ? '' : 'red';
            $kelas = $d2['kelas'] ?? "<span class='kelas-materi hover' id=kelas-materi--$d2[id]>❓</span>";
            $li_materi .= "
              <li class='border-bottom pb1 mb1'>
                <div class='grid2 gap2'>
                  <div>⬆️ $d2[nama_materi]</div>
                  <div class='flex-between pr3'>
                    <div>kelas $kelas</div>
                    <div class='$red'>$d2[jumlah_soal] soal  <a href=?manage_soal&id_materi=$d2[id]>➕</a></div>
                  </div>
                </div>
              </li>
            ";
          }
        }

        $urutan = $urutan ?? 0; // ambil dari urutan sebelumnya atau 1
        $urutan++;

        $value = "
          <ol class='ml1 pl4'>
            $li_materi
            <li style='list-style:none'>
              <span class='btn-aksi green hover' id=form-add-$d[id]--toggle>➕ Materi $d[singkatan]</span>
              <div class='hideit mt2' id=form-add-$d[id]>
                <form method=post class='flexy gap1' >
                  <div class=hideit>
                    <input required name=urutan value=$urutan type=number placeholder='Urutan...'>
                  </div>
                  <div>
                    <input required name=nama_materi type=text placeholder='Nama materi ...'>
                  </div>
                  <div>
                    <select required name=kelas>
                      <option value=''>--pilih kelas--</option>
                      $option_kelas
                    </select>
                  </div>
                  <div>
                    <button name=btn_add_materi value=$d[id]>Add</button>
                  </div>
                </form>
              </div>
            </li>
          </ol>
        ";
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
  <div class='pt2' style='position:relative '>
    <table class=table>
      <style>thead th{border:solid 1px #ccc; background:darkblue; color:white}</style>
      <thead style='position:sticky;top:55px;'>$th<th>Aksi</th></thead>
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
  </div>
" : alert("Data mapel tidak ditemukan.");
echo "$tb";
?>
<script>
  $(function() {
    $('.kelas-materi').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id_materi = rid[1];
      console.log(aksi, id_materi);

      let kelas = prompt("Enter kelas antara 1 s.d 13");
      if (!kelas) return;
      kelas = parseInt(kelas);
      if (kelas && kelas <= 13) {
        $.ajax({
          url: `ajax/ajax_update_kelas_materi.php?id_materi=${id_materi}&kelas=${kelas}`,
          success: function(a) {
            if (a.trim() == 'OK') {
              alert('OK');
              $('#' + tid).text(kelas);
            } else {
              alert(a);
            }
          }
        })
      }

    });
  })
</script>