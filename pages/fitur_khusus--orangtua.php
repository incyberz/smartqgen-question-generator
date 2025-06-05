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
include 'info_paket_soal.php';

# ============================================================
# REQUEST PENCAIRAN
# ============================================================
$jumlah_request_pencairan = 0;
include 'request_pencairan.php';
$info_request_pencairan = $jumlah_request_pencairan ? "
  <a href=?manage_trx class='btn btn-danger w-100 mb1 mt1'>Ada <span class=f24>$jumlah_request_pencairan</span> Request Pencairan</a>
" : "
  <a href=?manage_trx class='btn w-100 mb1 btn-warning mt1'>💰 Manage Saldo dan Trx</a>
";














# ============================================================
# GET TMP SALDO
# ============================================================
include 'tmp_data.php';
$selisih = strtotime('now') - strtotime($tmp['last_update']);
if ($selisih > 300) {
  # ============================================================
  # JIKA LEBIH DARI 5 MENIT MAKA REUPDATE SALDO
  # ============================================================
  include 'update_saldo.php'; // update DB
  $tmp['saldo'] = $real_saldo; // update var
}
$saldo_show = number_format($tmp['saldo']);



























$fitur = "
  <h2 class='tengah f20 pt4 mt4 border-top'>Saldo Rewards</h2>
  <div class='score-box'>
    💸<strong id=nilai>$saldo_show</strong> Rupiah
  </div>
  <p class='abu f12 mt1 mb3'>Saldo ini sebagai Info Reward Virtual Anda. Berikan reward jika anak mengajukan pencairan Learning Point</p>

  $info_request_pencairan
  <a href=?konfigurasi_pencairan class='btn w-100 mb1 btn-success'>💸 Konfigurasi Pencairan</a>

  $info_peserta_kelas
  <a href=?manage_kelas class='btn w-100 mb1 btn-success' >👨‍👩‍👧‍👦 Manajemen Kelas</a>

  $info_paket_soal
  <a href=?manage_trx class='btn w-100 mb1 btn-primary mt3'>📚 Beli Paket Soal (LKS)</a>
";
