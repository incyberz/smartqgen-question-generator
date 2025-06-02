<style>
  .transparan:hover {
    background: none;
    color: yellow;
    font-weight: bold;
  }
</style>
<?php
include 'user-process.php';
include 'opsi_quiz.php';
include "$dotdot/includes/hari_tanggal.php";

# ============================================================
# WAJIB VERIFIKASI WHATSAPP
# ============================================================
$whatsapp_info = '';
$fitur_khusus = '';
if (!$user['whatsapp_status']) {
  if ($user['role'] > 1) { // wajib verifikasi bagi ortu, pengajar, dll
    $time = date('F d, Y, H:i:s');
    $pesan = "Yth. Admin SmartQGen%0a%0aMohon untuk verifikasi whatsapp saya:%0a- Nama: $user[nama]%0a- Whatsapp: $user[whatsapp]%0a- Sebagai: $user[sebagai] [$user[role]]%0a- Tanggal Register: $user[created_at]%0aTerimakasih.%0a%0aFrom: Gamified Quiz System, $time";
    $text_wa = urlencode($pesan);
    $text_preview = str_replace('%0a', '<br>', $pesan);
    $link_whatsapp = "<a class='btn btn-primary w-100' href='https://api.whatsapp.com/?send&phone=6287729007318&text=$text_wa'>Kirim Whatsapp</a>";
    $whatsapp_info = "
      <div class='wadah gradasi-merah red'>
        Whatsapp Anda belum terverifikasi oleh Admin
        <div class='gradasi-toska p3 mb2 mt2 f12 left abu'>$text_preview</div>
        $link_whatsapp
      </div>
    ";
  }
} else {
  if ($user['role'] == 2 and !$user['posisi_ortu']) {
    include 'form_posisi_ortu.php'; // posisi ortu belum jelas
  } else {
    include 'fitur_khusus.php'; // fitur khusus ortu, guru, dll
  }
};
// $whatsapp_info = $user['whatsapp_status'] ? '' : 'Whatsapp Anda belum terverifikasi';
// jsurl('?verifikasi_whatsapp');

# ============================================================
# POSISI ORANG TUA
# ============================================================
$info_sub_role = '';
if ($user['role'] >= 2) {
  $rposisi_ortu = [
    '1' => 'Ayah',
    '2' => 'Ibu',
    '3' => 'Wali'
  ];
  if ($user['role'] == 2) {
    $info_sub_role = $user['posisi_ortu'] ? '(' . $rposisi_ortu[$user['posisi_ortu']] . ')' : '';
  }
}


$history = $_GET['history'] ?? null;
$unclaim_poin = '';
$hitung_lp = '';

if (!$history) {
  # ============================================================
  # HITUNG LP
  # ============================================================
  include 'hitung-lp.php';

  $limit = 'LIMIT 1';
  $judul = "
    <h1>Dashboard</h1>
    <p>Selamat Datang $user[nama] 👐</p>
    <div class='f12 mb2'>Anda login sebagai $user[sebagai] $info_sub_role</div>
  ";
  $claimed_title = "
    <h2 class='tengah f20 pt4 mt4 border-top'>✅ Claimed Last Kuis</h2>
    <p class=f14>Claimed Kuis tidak dapat dihapus karena poinnya sudah ditambahkan ke Learning Point</p>
  ";
  $bottom_links = "
    <div class='pt2 tengah' style='display:grid; grid-template-columns: 50% 50%'>
      <a href=?dashboard&history=1><h2 class='f18 hover'>⏲️ History Kuis</h2></a>
      <h2 class='f18 hover' id=play-again>▶️ Play Again</h2>
    </div>
  ";
} else {
  # ============================================================
  # HISTORY IS ON
  # ============================================================
  $limit = 'LIMIT 1000';
  $judul = "
    <h2>⏲️ History Kuis</h2>
    <p class=f14>Kuis-kuis yang pernah kamu jawab dan sudah Claim Point.</p>
  ";
  $claimed_title = '';
  $bottom_links = "<a class='btn btn-secondary mt3' href=?dashboard>Back to Dashboard</a>";
}

$tr_unclaim = '';
include 'unclaim_poin.php';

$hideit = '';
$class_ortu = '';
$info_as_ortu = '';
if ($user['role'] >= 2) {
  $class_ortu = 'ortu';
  $hideit = 'hideit';
  $info_as_ortu = "<div class='tengah f20 pt4 mt4 border-top yellow btn-aksi hover' id=tampilan-pelajar--toggle>Lihat Tampilan as Pelajar</div>";
}


echo "
  <div style='max-width:800px;margin:auto;' class='$class_ortu'>
    $judul
    $whatsapp_info
    $fitur_khusus

    <div id=tampilan-pelajar class='$hideit pelajar'>
      <div id=main-dashboard>
        $hitung_lp
        $unclaim_poin
      </div>

      $bottom_links
      
      <div class=hideit id=blok-play-again>
        $opsi_quiz
        <div class='mt2'><a href='?quiz-started' class='btn btn-primary w-100'> Start Quiz</a></div>
      </div>
    </div>
    $info_as_ortu

  </div>
";
?>
<script>
  $(function() {
    $('#play-again').click(function() {
      $('#main-dashboard').slideToggle();
      $('#blok-play-again').slideToggle();
    })
  })
</script>