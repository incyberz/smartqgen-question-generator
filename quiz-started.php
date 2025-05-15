<link rel="stylesheet" href="assets/css/progress.css">
<?php
include 'quiz-process.php';
include 'quiz-styles.php';
include 'quiz-functions.php';
$max_soal = 5; // limitt soal
$total_second = 120; // detik
$get_no = $_GET['no'] ?? 1;
























# ============================================================
# MAIN SELECT DATA SOAL
# ============================================================
$session_id_soals = $_SESSION['id_soals'] ?? null;
if ($session_id_soals) {
  $rid_soal = explode(',', $session_id_soals);
  $sql_id_soal = '';
  foreach ($rid_soal as $id_soal) {
    if ($id_soal) {
      $OR = $sql_id_soal ? 'OR' : '';
      $sql_id_soal .= " $OR id = $id_soal ";
    }
  }
  $sql_id_soal = "WHERE $sql_id_soal ORDER BY id LIMIT $max_soal";;
} else {
  $sql_id_soal = "ORDER BY id LIMIT $max_soal";
}

$s = "SELECT * FROM tb_soal $sql_id_soal ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_soal = mysqli_num_rows($q);
if (!$jumlah_soal) stop("Tidak ada soal!");
$rsoal = [];
$no = 1;
$id_soals = '';
while ($d = mysqli_fetch_assoc($q)) {
  $id_soals .= $d['id'] . ',';
  $rsoal[$no] = $d;
  $no++;
}
$_SESSION['id_soals'] = $id_soals;

# ============================================================
# SISA WAKTU FROM SESSION
# ============================================================
$session_mulai_mengerjakan = $_SESSION['mulai_mengerjakan'] ?? null;
$session_nomor_soal = $_SESSION['nomor_soal'] ?? null;

if (!$session_nomor_soal) {
  // pertama kali mengerjakan
  $_SESSION['mulai_mengerjakan'] = time();
  $_SESSION['nomor_soal'] = 1;
} else {
  // hitung sisa waktu
  $sisa_waktu = $total_second - (time() - $session_mulai_mengerjakan);

  if ($sisa_waktu <= 0) {
    // waktu habis
    // hapus session
    if ($_SESSION['nomor_soal'] < $jumlah_soal) { // lanjut ke soal berikutnya
      $_SESSION['mulai_mengerjakan'] = time();
      $_SESSION['nomor_soal']++;
      jsurl('');
    } else { // selesai
      // selesai
      unset($_SESSION['mulai_mengerjakan']);
      unset($_SESSION['nomor_soal']);
      unset($_SESSION['id_soals']);

      # ============================================================
      # SOAL HABIS
      # ============================================================
      jsurl('?quiz-selesai');
    }
  } else {
    // sisa waktu
    $total_second = $sisa_waktu;
  }
}

echo "<i class=hideit id=total_second>$total_second</i>";

// echo '<pre>';
// print_r($total_second);
// print_r($_SESSION);
// echo '</pre>';


$nomor_soal = $_SESSION['nomor_soal'] ?? null;
if (!$nomor_soal) stop("Tidak ada nomor soal!");
$soal = $rsoal[$nomor_soal];
$id_soal = $soal['id'];
$src = "assets/img/soal/$soal[image]";
$img = !file_exists($src) ? '' : "<img src='$src' class='image-soal' alt='image-soal'>";


# ============================================================
# LAPORKAN SOAL
# ============================================================
$laporkan_soal = '';
include 'laporkan_soal.php';


# ============================================================
# TEMPLATE SOAL PROCESSING
# ============================================================

// Randomisasi variabel
$variabel_json = json_decode($soal['variabel_json'], true);
$randomVars = [];
foreach ($variabel_json as $var => $range) {
  $randomVars[$var] = generateRandomValue($range);
}

// Ganti placeholder di template soal
$kalimat_soal = $soal['soal_template'];
foreach ($randomVars as $key => $val) {
  $kalimat_soal = str_replace("$$key", $val, $kalimat_soal);
}

// Hitung jawaban benar
$jawaban_benar = evaluateFormula($soal['formula'], $randomVars);
$jawaban_benar = round($jawaban_benar, 2); // dibulatkan 2 angka desimal

// Generate opsi PG
$opsi = [
  round(evaluateFormula($soal['wrong_formula_1'], $randomVars), 2),
  round(evaluateFormula($soal['wrong_formula_2'], $randomVars), 2),
  round(evaluateFormula($soal['wrong_formula_3'], $randomVars), 2),
  "$jawaban_benar *"
];
shuffle($opsi);

// Simpan data ke tb_jawaban
$cek = $conn->query("SELECT id FROM tb_jawaban WHERE id_pengunjung=$id_pengunjung AND id_soal=$id_soal");
if ($cek->num_rows == 0) {
  echo "insert";
  $stmt = $conn->prepare("INSERT INTO tb_jawaban (id_pengunjung, id_soal, variabel_json, jawaban_benar) VALUES (?, ?, ?, ?)");
  $var_json = json_encode($randomVars);
  $stmt->bind_param("iisd", $id_pengunjung, $id_soal, $var_json, $jawaban_benar);
  $stmt->execute();
} else {
  echo "update";
  // jika sudah ada, update data, pengunjung melakukan refresh
  $stmt = $conn->prepare("UPDATE tb_jawaban SET variabel_json = ?, jawaban_benar = ? WHERE id_pengunjung = ? AND id_soal = ?");
  $var_json = json_encode($randomVars);
  $stmt->bind_param("sdii", $var_json, $jawaban_benar, $id_pengunjung, $id_soal);
  $stmt->execute();
}

$halo_user = '';
if ($session_nomor_soal == 1) {
  $halo_user = "<p class='pt3 mb2 border-top pt2 mt2'>Halo $nama_pengunjung! $debug_id_pengunjung<br>Jawablah pertanyaan berikut: </p>";
}
$persen_progress = $session_nomor_soal * 100 / $jumlah_soal;






















# ============================================================
# FINAL ECHO
# ============================================================
echo "
  <form method=post>
    <h1>Quiz Started</h1>
    $halo_user
    <div class=info-soal>
      <div><b>Soal</b>: Fisika</div>
      <div class=right><b>Tingkat</b>: SMA</div>
      <div><b>Materi</b>: GLBB</div>
      <div class=right><b>Level</b>: <i>medium</i></div>
    </div>

    <div class='blok-progress mb3'>
      <div class='f14 mb1'>Soal ke-$_SESSION[nomor_soal] dari $jumlah_soal soal</div>
      <div class='progress'>
        <div class='progress-bar progress-bar-primary progress-bar-animated' style='width:$persen_progress%'></div>
      </div>
    </div>

    <div class=debug>$_SESSION[id_soals] | $id_soal</div>

    <div class=kalimat-soal>
      $kalimat_soal
      $img
    </div>
    
    <div class=blok-opsi>
      <div class='opsi'>$opsi[0]</div>
      <div class='opsi'>$opsi[1]</div>
      <div class='opsi'>$opsi[2]</div>
      <div class='opsi'>$opsi[3]</div>
    </div>
    <div class='hideit blok-jawaban'>
      Jawaban:
      <input id=jawaban name=jawaban />
    </div>
    <div class=blok-timer>
      <div class='item-timer' id=mnt>00</div>
      <div class='item-timer'>:</div>
      <div class='item-timer' id=dtk>00</div>
    </div>
    <div class='blok-silahkan'>
      <span class='btn btn-secondary w-100 btn-lg mb4' disabled id=btn_silahkan>Silahkan Pilih Jawaban!</span>
    </div>
    <div class='hideit blok-submit'>
      <button class='btn btn-primary w-100 btn-lg mb4' name=btn_submit_jawaban id=btn_submit_jawaban value=$id_soal>Submit</button>
    </div>
    <div class='hideit blok-next-soal'>
      <span class='btn btn-primary w-100 btn-lg mb4' onclick=location.reload()>Next Soal</span>
    </div>
    $laporkan_soal
  </form>
";





























?>
<script>
  $(function() {
    $('.opsi').click(function() {
      $('.opsi').removeClass('opsi-selected');
      $(this).addClass('opsi-selected');
      $('#jawaban').val($(this).text());
      $('.blok-silahkan').slideUp();
      $('.blok-submit').slideDown();
    })
  })
</script>

<script>
  $(document).ready(function() {
    let totalSeconds = parseInt($('#total_second').text()); // Total waktu pengerjaan (120 detik)
    let timer;

    function updateDisplay() {
      const minutes = Math.floor(totalSeconds / 60);
      const seconds = totalSeconds % 60;

      $('#mnt').text(minutes.toString().padStart(2, '0'));
      $('#dtk').text(seconds.toString().padStart(2, '0'));
    }

    function startTimer() {
      timer = setInterval(() => {
        totalSeconds--;

        if (totalSeconds < 0) {
          clearInterval(timer);
          $('#mnt, #dtk').text('00');
          alert("Waktu habis!");
          $('#btn_submit_jawaban').click();
          // Bisa juga auto-submit form di sini
          return;
        }

        updateDisplay();
      }, 1000); // 1 detik
    }

    updateDisplay();
    startTimer();
  });
</script>