<?php
set_h2('Tambah Anak', 'Menambahkan Peserta Didik atau Putra/Putri Anda ke Area Asuhan (Kelas) Anda');
akses('tambah_anak');
include 'tambah_anak-process.php';
if (!$user['jumlah_kelas']) {
?>
  <div class="yellow border-top mt3 pt3">Anda belum punya kelas, silahkan buat dahulu!</div>
  <form method=post class="wadah left mt3">
    <div class="mb3">
      <div class="f14 mb2">Nama Kelas </div>
      <input required type="text" minlength="5" maxlength="30" name="nama_kelas" id="nama_kelas" autocomplete="off" placeholder="Contoh: Kelas SD, Homeschooling Albaiti">
    </div>
    <button class="btn btn-primary w-100">Tambah Kelas</button>
  </form>

<?php

} else {
?>
  <form method=post class="wadah left mt3">
    <div class="mb3">
      <div class="f14 mb2">Username Anak Anda</div>
      <input required type="text" name="username_anak" id="username_anak" autocomplete="off">
    </div>
    <div class="mb3">
      <div class="f14 mb2">Tanggal Lahir</div>
      <input required type="date" name="tanggal_lahir" id="tanggal_lahir" autocomplete="off" min='2000-01-01' max='2023-01-01'>
    </div>
    <button class="btn btn-primary w-100">Tambahkan</button>
  </form>

  <h2 class="border-top pt3 mt4 f18">Anak Asuhan Kelas Anda</h2>
  <table>
    <thead>
      <th>No</th>
      <th>Nama</th>
      <th>Aksi</th>
    </thead>
    <tr>
      <td colspan=100% class="gradasi-merah abu miring tengah f14">
        <div>belum ada</div>
      </td>
    </tr>
    <tr>
      <td>No</td>
      <td>Nama</td>
      <td>Aksi</td>
    </tr>
  </table>
<?php
}
?>