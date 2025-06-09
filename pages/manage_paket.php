<?php
akses('manage_paket');
include 'includes/key2kolom.php';
$is_admin = $user['is_admin'];
$get_jenjang = $_GET['jenjang'] ?? 'SD';
include 'option_kelas.php';


# ============================================================
# PROCESS
# ============================================================
include 'manage_paket-process.php';


# ============================================================
# NAVIGASI JENJANG
# ============================================================
$s = "SELECT * FROM tb_jenjang ORDER BY urutan";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$nav = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  if ($get_jenjang == $d['jenjang']) {
    $item_nav = "<button class='btn btn-sm btn-primary'>$d[nama_jenjang]</button>";
  } else {
    $item_nav = "<a href=?manage_paket&jenjang=$d[jenjang]><button class='btn btn-sm btn-secondary'>$d[nama_jenjang]</button></a>";
  }
  $nav .= "<div>$item_nav</div>";
}
echo "<div class='flex-center gap1 gradasi-kuning' style='z-index:3; position:sticky; top:0; padding:15px; margin:-15px;'>$nav</div>";



















# ============================================================
# ALL MATERI SESUAI JENJANG U/ SELECT  
# ============================================================
$s = "SELECT 
b.singkatan as mapel,
c.nama_jenjang,
c.min_kelas,
c.max_kelas,
a.*,
(SELECT COUNT(1) FROM tb_soal WHERE id_materi=a.id) jumlah_soal,
(SELECT COUNT(1) FROM tb_paket_soal_detail WHERE id_materi=a.id) trx_paket,
(SELECT COUNT(1) FROM tb_paket_soal WHERE id_mapel=b.id) paket_mapel_available,
(
  SELECT COUNT(1) FROM tb_jawaban p 
  JOIN tb_soal q ON p.id_soal=q.id 
  WHERE q.id_materi=a.id 
  AND p.archived=1) trx_jawaban

FROM tb_materi a 
JOIN tb_mapel b ON a.id_mapel=b.id 
JOIN tb_jenjang c ON b.jenjang=c.jenjang 
WHERE b.jenjang='$get_jenjang'
ORDER BY b.urutan, 
a.kelas, 
a.nama_materi
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_materi = mysqli_num_rows($q);

$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  $th = '';
  $last_mapel = '';
  $last_id = '';
  $last_id_mapel = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;

    # ============================================================
    # AUTO SET MIN KELAS FROM JENJANG
    # ============================================================
    if (!$d['kelas']) {
      $s2 = "UPDATE tb_materi SET kelas = $d[min_kelas] WHERE id=$d[id]";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      $d['kelas'] = $d['min_kelas'];
    }

    $td = '';
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'date_created'
        || $key == 'urutan'
        || $key == 'id_mapel'
        || $key == 'paket_mapel_available'
        || $key == 'nama_jenjang'
        || $key == 'min_kelas'
        || $key == 'max_kelas'
      ) {
        continue;
      } elseif ($key == 'nama_materi') {

        $btn_hapus = ($d['trx_jawaban'] || $d['trx_paket']) ? '<span style="cursor:not-allowed" onclick="alert(`Tidak dapat menghapus materi karena sudah ada Trx Jawaban/Paket.`)">🟣</span>' : "<span class='hapus-materi pointer' id='hapus-materi--$d[id]' onclick='return confirm(`Hapus materi ini?`)'>❌</span>";

        $value = "
          <div class='flex-between'>
            <div class='blue'>$d[urutan]. <span class='ubah-materi hover' id=ubah-materi--$d[id]>$value</span></div>
            <div class=''>$btn_hapus</div>
          </div>
        ";
      } elseif ($key == 'kelas') {
        $kelas = $d['kelas'] ?? '❓';
        $value = "<span class='kelas-materi hover' id=kelas-materi--$d[id]>kls $kelas ↔️</span>";
      } elseif ($key == 'jumlah_soal') {
        $value = $value ? "
          <div class='hideit wadah gradasi-kuning blok-soal pointer' id=blok-soal--$d[id]></div>
          <span class='lihat-soal hover' id=blok-soal-$d[id]--toggle--$d[id] >$value ✅</span> 
        " : "<span class=red>0 ⚠️</span>";
        $value .= " <a href=?manage_soal&id_materi=$d[id]>➕</a>"; // link tambah soal
      } elseif ($key == 'trx_paket') {
        if ($d['jumlah_soal']) {
          $nama_jenjang = str_replace(' ', '-', $d['nama_jenjang']);
          $value = !$d['paket_mapel_available'] ? '' : " 
            <span class='btn-aksi hover' id=assign_to_paket-$d[id]--toggle>$value ✅</span>
          ";
          # ============================================================
          # SELECT PAKET SESUAI MAPEL MATERI INI
          # ============================================================
          $s2 = "SELECT a.*,
            ( 
              SELECT 1 FROM tb_paket_soal_detail 
              WHERE id_materi = $d[id]
              AND id_paket=a.id) assigned 
            FROM tb_paket_soal a 
            WHERE a.harga > 0 
            AND a.harga is not null 
            AND a.id_mapel = $d[id_mapel] --  SESUAI MAPEL MATERI INI 
            AND a.jenjang = '$get_jenjang' -- PAKET SESUAI JENJANG
            ";
          $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
          $li = '';
          while ($d2 = mysqli_fetch_assoc($q2)) {
            $checked = $d2['assigned'] ? 'checked' : '';
            $li .= "
                <li id=li--checkbox-assign-paket--$d2[id]--$d[id]>
                  <label class=pointer>
                    <input 
                      class=checkbox-assign-paket 
                      id=checkbox-assign-paket--$d2[id]--$d[id] 
                      type=checkbox 
                      $checked
                    /> $d2[nama_paket]
                  </label>
                </li>
              ";
          }

          $hideit = $d['trx_paket'] ? 'hideit' : '';
          $value = !$li ? "
            0 <i class='f12 red'>(Belum ada Paket $d[mapel])</i>
          " : "
            <style>.assign_to_paket li{list-style:none}</style>
            <ol class='$hideit pl4 assign_to_paket mt1 wadah gradasi-kuning' id=assign_to_paket-$d[id]>
              $li
            </ol>
            $value
          ";
        } else { // belum ada soal
          $value .= " <i class='red f12'>(belum ada soal)</i>";
        }
      }
      if ($i == 1) {
        $kolom = key2kolom($key);
        $th .= "<th>$kolom</th>";
      }
      $td .= "<td>$value</td>";
    }

    $separator = ($last_mapel != $d['mapel'] and $i > 1) ? 'style="border-top:solid 15px #fcf"' : '';
    $tr_form = '';
    if ($last_mapel != $d['mapel'] and $i > 1) {
      include 'manage_paket-tr_form.php';
    }
    $tr .= "
      $tr_form
      <tr $separator>
        $td
      </tr>
    ";
    $last_mapel = $d['mapel'];
    $last_id = $d['id'];
    $last_id_mapel = $d['id_mapel'];

    $tr_form_final = '';
    if ($i == $jumlah_materi) {
      echo "<div class=hideit><i id=min_kelas>$d[min_kelas]</i><i id=max_kelas>$d[max_kelas]</i></div>";
      $cnama_jenjang = $d['nama_jenjang'];
      include 'manage_paket-tr_form.php';
      $tr .= $tr_form;
    }
  }
}

$tb = $tr ? "
  <table class=table>
    <style>thead th{border:solid 1px #ccc; background:darkblue; color:white}</style>
    <thead style='position:sticky;top:55px;'>$th</thead>
    $tr

    
    <tr>
      <td colspan=100%>
        <h3 class=mb2>Tambah Mapel Jenjang $cnama_jenjang</h3>
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
            <button name=btn_add_mapel id=btn_add_mapel value='$get_jenjang'>Add Mapel <span class=cnama_jenjang>$cnama_jenjang</span></button>
          </div>
        </form>
      </td>
    </tr>

  </table>
" : alert("Data XXX tidak ditemukan.");
echo "$tb";

























# ============================================================
# MAIN SELECT PAKET PAID ONLY
# ============================================================
$s = "SELECT 
a.urutan as No,
a.*,
b.nama_jenjang,
(SELECT COUNT(1) FROM tb_materi WHERE id_mapel=a.id_mapel) materi_dan_count_soal, 
(SELECT singkatan FROM tb_mapel WHERE id=a.id_mapel) singkatan_mapel,
(SELECT nama_materi FROM tb_materi WHERE id=a.id_materi) nama_materi 
FROM tb_paket_soal a 
JOIN tb_jenjang b ON a.jenjang=b.jenjang  
AND a.jenjang = '$get_jenjang' 
AND (harga > 0 AND harga is not null) -- manage paid only
ORDER BY b.urutan, a.urutan, a.nama_paket
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  $th = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $total_soal = 0;

    if (!$d['urutan']) {
      # ============================================================
      # AUTO INSERT URUTAN BY ORDER A-Z
      # ============================================================
      $s3 = "UPDATE tb_paket_soal SET urutan=$i WHERE id=$d[id]";
      mysqli_query($cn, $s3) or die(mysqli_error($cn));
    }

    $td = '';
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'urutan'
        || $key == 'jenjang'
        || $key == 'nama_jenjang'
        || $key == 'singkatan_mapel'
        || $key == 'nama_materi'
        || $key == 'created_at'
        || $key == 'created_by'
        || $key == 'status'
        || $key == 'singkatan'
        || $key == 'id_materi'
        || $key == 'id_mapel'
        || $key == 'harga'
        || $key == 'min_level'
        || $key == 'load_per_play'
      ) {
        continue;
        // } elseif ($key == 'status') {
        //   $value = $value ? '✅' : '❌';
      } elseif ($key == 'materi_dan_count_soal') {
        $li_materi = '';
        if ($value) {
          $s2 = "SELECT *,
          b.nama_materi,
          (SELECT COUNT(1) from tb_soal WHERE id_materi=b.id) jumlah_soal 
          FROM tb_paket_soal_detail a 
          JOIN tb_materi b ON a.id_materi=b.id
          WHERE a.id_paket=$d[id] 
          ORDER BY a.urutan, jumlah_soal DESC
          ";
          $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
          $urutan = 0;
          while ($d2 = mysqli_fetch_assoc($q2)) {
            $urutan++;
            if (!$d2['urutan']) {
              # ============================================================
              # AUTO INSERT URUTAN BY ORDER A-Z
              # ============================================================
              $s3 = "UPDATE tb_paket_soal_detail SET urutan=$urutan WHERE id=$d2[id]";
              mysqli_query($cn, $s3) or die(mysqli_error($cn));
            }
            $total_soal += $d2['jumlah_soal'];
            $red = $d2['jumlah_soal'] ? '' : 'red';
            $kelas = $d2['kelas'] ?? '❓';
            $li_materi .= "
              <li class='border-bottom pb1 mb1'>
                <div class='grid2 gap2'>
                  <div>$d2[nama_materi]</div>
                  <div class='flex-between pr3'>
                    <div>kelas $kelas</div>
                    <div class='$red'>$d2[jumlah_soal] soal</div>
                  </div>
                </div>
              </li>
            ";
          }
        }

        $blue_total = $total_soal ? 'blue' : 'red';

        $value = "
          <ol class='ml1 pl4'>
            $li_materi
            <li style='list-style:none' class='right $blue_total f20'>
              <b>Total</b>: $total_soal soal  
            </li>
          </ol>
        ";
      } elseif ($key == 'nama_paket') {

        $d['singkatan_mapel'] = $d['singkatan_mapel'] ? "<span class='blue'>$d[singkatan_mapel]</span>" : '<i>(all mapel)</i>';
        $d['nama_materi'] = $d['nama_materi'] ? "<span class='blue'>$d[nama_materi]</span>" : '<i>(all materi)</i>';

        $biru = $d['harga'] ? 'biru' : 'green';
        $harga = $d['harga'] ? 'Rp ' . number_format($d['harga']) . ',-' : '<i class="brown">Free</i>';
        $value = "
          <div>
            <div class='$biru'>$d[nama_paket]</div>
            <div class='f24'>$harga</div>
            <div class='f12 abu mt1'>
              <div class='grid2 gap1'>
                <div><b>Singkatan</b>: $d[singkatan]</div>
                <div><b>Jenjang</b>: $d[nama_jenjang]</div>
              </div>
              <div class='grid2 gap1'>
                <div><b>Mapel</b>: $d[singkatan_mapel]</div>
                <div><b>Min Level</b>: $d[min_level]</div>
              </div>
              <div class='grid2 gap1'>
                <div><b>Materi</b>: $d[nama_materi]</div>
                <div><b>Load per Play</b>: $d[load_per_play]</div>
              </div>
            </div>
          </div>
        ";
      }
      if ($i == 1) {
        $kolom = key2kolom($key);
        $th .= "<th>$kolom</th>";
      }
      $td .= "<td>$value</td>";
    }

    $tr .= "
      <tr>
        $td
      </tr>
    ";
  }
}


$tb = $tr ? "
  <div class='pt2' style='position:relative '>
    <table class=table>
      <style>thead th{border:solid 1px #ccc; background:darkblue; color:white}</style>
      <thead style='position:sticky;top:55px;'>$th</thead>
      $tr
      <tr>
        <td>
          #
        </td>
        <td colspan=100%>
          <h3>Form Tambah Paket (Multi Mapel)</h3>
          <form method=post class='flexy gap1' id=form-tambah-paket>
            <div>
              <input 
                type=text
                required 
                id=nama_paket 
                name=nama_paket 
                minlength='3' 
                maxlength='50' 
                placeholder='Nama paket...'
              />
            </div>
            <div>
              <input 
                type=text
                required 
                id=singkatan 
                name=singkatan 
                minlength='2' 
                maxlength='10' 
                placeholder='Singkatan...'
              />
            </div>
            <div>
              <button name=btn_add_paket id=btn_add_paket value='$get_jenjang'>Add Paket</button>
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
    $('.checkbox-assign-paket').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id_paket = rid[1];
      let id_materi = rid[2];
      let checked = $(this).prop('checked');
      console.log(aksi, id_paket, id_materi, checked);
      if (id_materi && id_paket) {
        $.ajax({
          url: `ajax/ajax_assign_materi_to_paket.php?id_materi=${id_materi}&id_paket=${id_paket}&assign=${checked}`,
          success: function(a) {
            if (a.trim() == 'OK') {
              $('#li--' + tid).slideUp();
            } else {
              alert(a);
            }
          }
        })
      }
    });

    $('.kelas-materi').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id_materi = rid[1];
      console.log(aksi, id_materi);

      let min_kelas = parseInt($('#min_kelas').text());
      let max_kelas = parseInt($('#max_kelas').text());
      let tmp = $(this).text().split(' ');
      let kelas_default = parseInt(tmp[1]);
      kelas_default = kelas_default ?? 1;

      let kelas = prompt(`Enter kelas antara ${min_kelas} s.d ${max_kelas}\n\nJika selain itu akan berpindah ke Jenjang lain. Max nilai kelas adalah 13 (Perguruan Tinggi).`, kelas_default);
      if (!kelas) return;
      kelas = parseInt(kelas);
      if (kelas && kelas <= 13) {
        if (!(kelas >= min_kelas && kelas <= max_kelas)) {
          let y = confirm(`Pindahkan ke Jenjang lain?\n\nPada jenjang ini antara kelas ${min_kelas} dan ${max_kelas}.`);
          if (!y) return;
        }
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
      } else {
        alert(`Invalid kelas [${kelas}]`);
      }
    });

    $('.lihat-soal').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id_materi = rid[2];
      $.ajax({
        url: `ajax/ajax_lihat_kalimat_soal_per_materi.php?id_materi=${id_materi}`,
        success: function(a) {
          $('#blok-soal--' + id_materi).html(a);
          $('.blok-soal').slideUp();
          $('#blok-soal--' + id_materi).slideDown();
        }
      })

    });

    $('.ubah-materi').click(function() {
      alert('ZZZ');
      return;
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id_materi = rid[2];
      $.ajax({
        url: `ajax/ajax_lihat_kalimat_soal_per_materi.php?id_materi=${id_materi}`,
        success: function(a) {
          $('#blok-soal--' + id_materi).html(a);
          $('.blok-soal').slideUp();
          $('#blok-soal--' + id_materi).slideDown();
        }
      })

    });

    $('.hapus-materi').click(function() {
      alert('ZZZ');
      return;
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id_materi = rid[2];
      $.ajax({
        url: `ajax/ajax_lihat_kalimat_soal_per_materi.php?id_materi=${id_materi}`,
        success: function(a) {
          $('#blok-soal--' + id_materi).html(a);
          $('.blok-soal').slideUp();
          $('#blok-soal--' + id_materi).slideDown();
        }
      })

    });

    $('.blok-soal').click(function() {
      $(this).slideUp()
    });
  })
</script>