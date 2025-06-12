<style>
  .row-paket {
    display: grid;
    grid-template-columns: 20px auto;
    gap: 5px;
  }
</style>
<?php
$jenis = $jenis ?? 'free';
$JOIN_paid = $jenis == 'free' ? '' : "JOIN tb_paid c ON a.id=c.id_paket";

$username_ortu = $kelas['username_ortu'] ?? $username;
$WHERE = $jenis == 'free' ? '(a.harga is null OR a.harga = 0)' : "c.pembeli = '$username_ortu'";
# ============================================================
# FREE PAKET
# ============================================================
$s = "SELECT a.*,
(SELECT nama_mapel FROM tb_mapel WHERE id=a.id_mapel) mapel, 
(
  SELECT COUNT(1) FROM tb_soal p 
  JOIN tb_materi q ON p.id_materi=q.id 
  JOIN tb_mapel r ON q.id_mapel=r.id 
  WHERE r.jenjang=a.jenjang) jumlah_soal_jenjang,
(
  SELECT COUNT(1) FROM tb_soal p 
  JOIN tb_materi q ON p.id_materi=q.id 
  JOIN tb_paket_soal_detail r ON q.id=r.id_materi 
  WHERE r.id_paket=a.id) jumlah_soal,
(
  SELECT paid_until FROM tb_paid 
  WHERE id_paket=a.id 
  AND pembeli = '$username'
  ) paid_until
FROM tb_paket_soal a 
JOIN tb_jenjang b ON a.jenjang=b.jenjang
$JOIN_paid
WHERE $WHERE
ORDER BY paid_until, b.urutan DESC
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;

  $jumlah_soal = $d['jumlah_soal'] ?? 0;
  $jumlah_soal_jenjang = $d['jumlah_soal_jenjang'] ?? 0;
  $jumlah_soal = $d['harga'] ? $jumlah_soal : $jumlah_soal_jenjang;
  $Random = ($d['mapel'] || !$jumlah_soal) ? '' : 'Random, ';

  $star = $d['paid_until'] ? '🌟' : '';
  $paid_until = $d['paid_until'] ? $d['paid_until'] : '<i>unlimitted</i>';
  $green = $d['paid_until'] ? 'bold green' : 'darkabu';
  $gradasi = $d['paid_until'] ? 'hijau' : 'abu';

  $tr .= "
    <tr>
      <td class='gradasi-$gradasi'>
        <div class=row-paket>
          <div>$i.</div>
          <div>
            <span class='$green'>$d[nama_paket] $star</span> 
            <div class='f12 abu'>$Random $jumlah_soal soal</div> 
            <div class='f12 abu'>Min level: $d[min_level]</div> 
            <div class='f12 abu'>Valid until: $paid_until</div> 
          </div>
        </div>
      </td>
    </tr>
  ";
}
