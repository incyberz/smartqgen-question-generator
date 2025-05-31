<style>
  .blok-peserta {
    flex-wrap: wrap;
  }

  @media (max-width:600px) {
    .blok-peserta {
      display: block;
    }
  }
</style>
<?php
# ============================================================
# FITUR KHUSUS ORANG TUA
# ============================================================
$info_anak_didik = '<div class="wadah gradasi-kuning abu miring ">Anda belum punya Anak Didik</div>';
$default_src = 'assets/img/pelajar.png';

if ($user['jumlah_kelas']) {
  $s = "SELECT * FROM tb_kelas a WHERE username='$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $tr_kelas = '';
  $tr = '';
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;

    $s2 = "SELECT a.*,
    b.nama as nama_peserta,
    b.id_pengunjung  
    FROM tb_peserta a 
    JOIN tb_user b ON a.username=b.username 
    WHERE a.id_kelas=$d[id]";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $pesertas = 'belum ada peserta pada kelas ini';
    if (mysqli_num_rows($q2)) {
      $pesertas = '';


      while ($d2 = mysqli_fetch_assoc($q2)) {
        # ============================================================
        # AMBIL POIN DAN NILAI DARI DATA PAKET
        # ============================================================
        $s3 = "SELECT * FROM tb_paket WHERE id_pengunjung=$d2[id_pengunjung]";
        $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
        $sum_nilai = 0;
        $sum_poin = 0;
        $jumlah_paket = mysqli_num_rows($q3);

        if ($jumlah_paket) {
          while ($d3 = mysqli_fetch_assoc($q3)) {
            $sum_nilai += $d3['nilai'];
            $sum_poin += $d3['poin'];
          }
          $poin = $sum_poin;
          $nilai = round($sum_nilai / $jumlah_paket);
        } else {
          $poin = 0;
          $nilai = 0;
        }

        $src = $default_src;

        $nama_peserta = ucwords(strtolower($d2['nama_peserta']));
        $pesertas .= "
          <div class='bordered mb3 br5 p2 tengah gradasi-toska row-peserta'>
            <a href=?detail_peserta&username=$d2[username]>
              <div><img src=$src class=foto_profil></div>
              <div>$nama_peserta</div>
            </a>
            <div class='flexy flex-between gap3 border-top mt2 pt2 f12'>
              <div>Poin: $poin</div>
              <div>R.Nilai: $nilai</div>
            </div>
          </div>
        ";
      }
    }



    $tr .= "
      <tr>
        <td class='tengah'>Kelas $d[nama_kelas]</td>
      </tr>
      <tr>
        <td>
          <div class='flex flex-center gap2 blok-peserta'>
            $pesertas
          </div>
        </td>
      </tr>
    ";
  }

  $info_anak_didik = "
    <table class='table mb3'>
      $tr
    </table>";
}


$fitur = "
  $info_anak_didik

  <a href=?manage_anak class='btn w-100 mb1 btn-success' >🧒 Manajemen Anak</a>
  <a href=?top_up_saldo class='btn w-100 mb1 btn-warning'>💰 Top-Up Saldo</a>
  <a href=?konfigurasi_penarikan class='btn w-100 mb1 btn-success'>💰 Konfigurasi Penarikan</a>
";
