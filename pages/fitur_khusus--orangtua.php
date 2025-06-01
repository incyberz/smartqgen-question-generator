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
$info_peserta_kelas = '<div class="wadah gradasi-kuning abu miring ">Anda belum punya Anak Didik</div>';
include 'info_peserta_kelas.php';

# ============================================================
# REQUEST PENCAIRAN
# ============================================================
$jumlah_request_pencairan = 0;
$info_request_pencairan = '';
include 'request_pencairan.php';
if ($jumlah_request_pencairan) {
  $info_request_pencairan = "<a href=?manage_trx class='btn btn-danger w-100'>Ada $jumlah_request_pencairan Request Pencairan</a>";
}














# ============================================================
# GET TMP SALDO
# ============================================================
$s = "SELECT * FROM tb_tmp WHERE username = '$username'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tmp = mysqli_fetch_assoc($q);
$selisih = strtotime('now') - strtotime($tmp['last_update']);
if ($selisih > 300) {
  # ============================================================
  # JIKA LEBIH DARI 5 MENIT MAKA REUPDATE SALDO
  # ============================================================
  include 'history_trx.php'; // update DB
  $tmp['saldo'] = $csaldo; // update var
}
$saldo_show = number_format($tmp['saldo']);



























$fitur = "
  <h2 class='tengah f20 pt4 mt4 border-top'>Saldo Rewards</h2>
  <div class='score-box'>
    💸<strong id=nilai>$saldo_show</strong> Rupiah
  </div>
  <p class='abu f12 mt1 mb3'>Saldo ini sebagai Info Reward Virtual Anda. Berikan reward jika anak mengajukan pencairan Learning Point</p>

  $info_request_pencairan
  <a href=?manage_trx class='btn w-100 mb1 btn-warning mt1'>💰 Manage Saldo dan Trx</a>
  <a href=?konfigurasi_pencairan class='btn w-100 mb1 btn-success'>💸 Konfigurasi Pencairan</a>

  <h2 class='tengah f20 pt4 mt4 border-top'>Paket Soal (LKS)</h2>
  <table class=table>
    <thead>
      <th>Paket Soal anak Anda:</th>
    </thead>
    <tr>
      <td>
        <div class=flexy>
          <div>1.</div>
          <div>
            Free Soal SD (Random) ZZZ
            <div class='f12 abu'>valid until: <i>unlimitted</i></div> 
          </div>
        </div>
      </td>
    </tr>
    <tr>
      <td>
        <div class=flexy>
          <div>2.</div>
          <div>
            Free Soal Matematika SMP ZZZ
            <div class='f12 abu'>valid until: <i>unlimitted</i></div> 
          </div>
        </div>
      </td>
    </tr>
  </table>

  <p class='abu f12 mt1'>Anda bisa membeli Paket Soal lainnya pada Katalog LKS</p>
  <a href=?manage_trx class='btn w-100 mb1 btn-primary mt3'>📚 Beli Paket Soal</a>


  <div class=''>
    $info_peserta_kelas
  </div>


  <a href=?manage_kelas class='btn w-100 mb1 btn-success' >👨‍👩‍👧‍👦 Manajemen Kelas</a>
";
