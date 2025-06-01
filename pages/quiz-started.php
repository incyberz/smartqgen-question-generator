<link rel="stylesheet" href="assets/css/progress.css">
<link rel="stylesheet" href="assets/css/quiz.css">
<!-- <link rel="stylesheet" href="assets/css/bootstrap.min.css"> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script> -->
<script src="./assets/js/crypto-js.min.js"></script>
<?php
include 'quiz-process.php';
include 'quiz-functions.php';
include 'quiz-hasil_quiz.php';
$max_soal = 5; // limitt soal
$total_second = 1200; // detik
$get_no = $_GET['no'] ?? 1;

// mulai quiz dalam time
$mulai_quiz = $_SESSION['qgen_mulai_quiz'] ?? null;
$detik_berlalu = time() - $mulai_quiz;
$total_second -= $detik_berlalu;
echo "<i class=hideit id=total_second>$total_second</i>";

# ============================================================
# LAPORKAN SOAL
# ============================================================
$laporkan_soal = '';
include 'laporkan_soal.php';



















# ============================================================
# NAV SPAN SOAL
# ============================================================

$nav_soals = '';
for ($i = 1; $i <= 30; $i++) {
  $nav_soals .= "<div class='nav-soal' id=nav-soal--$i>$i</div>";
}

$link_logout = $username ? '&nbsp;' : "<a href='?logout' onclick='return confirm(`Ahhh kaburrr?`)'>$img_logout</a>";



# ============================================================
# HTML AWAL
# ============================================================
echo "
  <div class='w-500'>
    <form method=post id=form-quiz>
      <h1 class=judul>Quiz Started</h1>

      <div class=blok-timer-logout>
        <div class='flex flex-between'>
          <div class=blok-timer>
            <div class='item-timer' id=mnt>00</div>
            <div class='item-timer'>:</div>
            <div class='item-timer' id=dtk>00</div>
          </div>
          <div>
            $link_logout
          </div>
        </div>
      </div>


      
      <div class=konten-soal>
        <div class=info-soal>
          <div class=row-info-soal>
            <div><b>Mapel</b>: <span id=mapel>mapel</span> - <span id=jenjang>jenjang</span></div>
            <div class=right><b>Level</b>: <i>medium</i></div>
          </div>
          <div><b>Materi</b>: <span id=materi>materi</span></div>
        </div>

        <div class=blok-kalimat-soal>
          <div id=kalimatSoal>kalimatSoal</div>
          <div id=gambarSoal>gambarSoal</div>
        </div>
        
        <div class=blok-opsi>
          <div class='opsi' id=opsi1>opsi1</div>
          <div class='opsi' id=opsi2>opsi2</div>
          <div class='opsi' id=opsi3>opsi3</div>
          <div class='opsi' id=opsi4>opsi4</div>
        </div>
      </div>

      <div class=blok-bawah>
        <div class='blok-progress'>
          <div class='f14 mb2 blok-nav-soal'>
            $nav_soals
          </div>
          <div class='progress'>
            <div id=progressBar class='progress-bar progress-bar-success progress-bar-animated' style='width:0%'></div>
          </div>
        </div>    

        <div class='blok-btn-submit hideit'>
          <span class='btn btn-primary w-100 btn-lg mb4' id=submit-btn>Submit</span>
        </div>

        <div class='blok-laporkan-soal'>
          $laporkan_soal
        </div>
      </div>
      
      
    </form>
    <form method=post id=form-hasil-quiz>
      $hasil_quiz;
    </form>
  </div>
";





























?>
<script>
  const secretKey = "SmartQGen2025";
  let currentIndex = 0;
  let persen = 0;
  let jawabanList = [];
  let soalList = [];


  // Enkripsi data JSON dan simpan localStorage
  function simpanJawabanEnkripsi(idSoal, jawaban) {
    if (!idSoal) {
      alert('undefined idSoal pada function simpanJawabanEnkripsi');
      return;
    }
    let data = loadJawabanEnkripsi();
    data[idSoal] = jawaban;
    let dataString = JSON.stringify(data);

    let ciphertext = CryptoJS.AES.encrypt(dataString, secretKey).toString();
    localStorage.setItem("jawaban_kuis_encrypted", ciphertext);
  }

  // Load dan dekripsi data dari localStorage
  function loadJawabanEnkripsi() {

    let ciphertext = localStorage.getItem("jawaban_kuis_encrypted");
    if (!ciphertext) return {};
    let bytes = CryptoJS.AES.decrypt(ciphertext, secretKey);
    let decryptedData = bytes.toString(CryptoJS.enc.Utf8);
    try {
      return JSON.parse(decryptedData);
    } catch {
      return {};
    }
  }

  // Render soal ke halaman
  function renderSoal(index) {
    const soalObj = soalList[index];
    if (!soalObj) {
      alert('undefined soalObj pada function renderSoal');
      return;
    }




    $('.konten-soal').fadeOut();


    $('#jenjang').text(soalObj.jenjang);
    $('#mapel').text(soalObj.mapel);
    $('#materi').text(soalObj.materi);
    $('#kalimatSoal').text(soalObj.kalimat_soal);

    if (soalObj.gambar) {
      $('#gambarSoal').html(`<img src="assets/img/soal/${soalObj.gambar}" class="gambar-soal" alt="gambar-soal">`);
    } else {
      $('#gambarSoal').html('');
    }

    // manajemen opsi
    $('.opsi').removeClass('opsi-selected');
    const savedJawaban = loadJawabanEnkripsi()[soalObj.id] || null;
    let i = 0;
    soalObj.opsi.forEach((pil, i) => {
      i++;
      $('#opsi' + i).text(pil);
      const opsiSelected = pil == savedJawaban ? 1 : 0;
      if (opsiSelected) {
        $('#opsi' + i).addClass("opsi-selected");
        $('#nav-soal--' + (index + 1)).addClass("terjawab");
      }
    });



    // update nav soal
    $('.nav-soal').removeClass('soal-ke');
    $('#nav-soal--' + (currentIndex + 1)).addClass('soal-ke');
    $('.konten-soal').fadeIn();

  }
































  $(function() {
    jawabanList = loadJawabanEnkripsi() || null;
    soalList = JSON.parse(localStorage.getItem('soalList') || '[]');

    if (soalList.length) {
      for (let i = 1; i <= soalList.length; i++) {
        $('#nav-soal--' + i).fadeIn();
      }

      // render first soal
      renderSoal(currentIndex);
    } else {

      // jika soal tidak ada di localStorage
      // ambil soal dari server
      $.ajax({
        url: 'pages/quiz-get_soal.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          soalList = Object.values(response);
          if (!soalList) {
            alert('List soal dari server kosong');
            return;
          } else {
            localStorage.setItem('soalList', JSON.stringify(soalList));
            location.reload();
          }

        },
        error: function(xhr, status, error) {
          alert('gagal mengambil data soal ke server.');
          console.error('Gagal mengambil soal:', error);
        }
      });
    }





    $('.opsi').click(function() {
      $('.opsi').removeClass('opsi-selected');
      $(this).addClass('opsi-selected');

      const soalObj = soalList[currentIndex];
      simpanJawabanEnkripsi(soalObj.id, $(this).text());
      // update progress
      const jawaban = loadJawabanEnkripsi();

      persen = Object.keys(jawaban).length * 100 / soalList.length;
      $('#progressBar').prop('style', `width:${persen}%`);

      // show btn_submit
      if (persen == 100) {
        $('.blok-btn-submit').slideDown();
        $('.progress').slideUp();
      }


      // update status soal: terjawab | belum
      $('#nav-soal--' + (currentIndex + 1)).addClass('terjawab');

      // auto NEXT ketika menjawab
      if (currentIndex < soalList.length - 1) {
        currentIndex++;
        renderSoal(currentIndex);
      }
    });

    $('.nav-soal').click(function() {
      currentIndex = parseInt($(this).text()) - 1;
      renderSoal(currentIndex);
    });

    $('#submit-btn').click(function() {
      clearInterval(timer);

      const jawabanList = loadJawabanEnkripsi() || null;
      if (!jawabanList) {
        alert('Tidak bisa membaca list jawaban pada local storage.');
        return;
      } else if (!Object.keys(jawabanList).length) {
        alert('List Jawaban kosong, silahkan refresh untuk memulai Quiz Baru.');
        localStorage.removeItem('jawaban_kuis_encrypted');
        localStorage.removeItem('soalList');
        return;
      }

      const jsonStr = JSON.stringify(jawabanList);
      const encoded = encodeURIComponent(jsonStr);

      $.ajax({
        url: 'pages/quiz-cek_jawaban.php?jawabans=' + encoded,
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('.hasil-quiz').html(res.html).slideDown();

            // clear local storage jawaban_kuis_encrypted dan soalList
            localStorage.removeItem('jawaban_kuis_encrypted');
            localStorage.removeItem('soalList');


          } else {
            alert('❌ Error: ' + res.msg);
            console.warn('Detail error:', res.msg);
          }
        },
        error: function(xhr, status, error) {
          alert('❌ Gagal terhubung ke server.');
          console.error('Gagal CEK JAWABAN:', error);
        }
      });
    });























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
          // alert("Waktu habis!");
          $('#submit-btn').click();
          // Bisa juga auto-submit form di sini
          return;
        }

        updateDisplay();
      }, 1000); // 1 detik
    }

    // renderSoal(currentIndex);
    updateDisplay();
    startTimer();
  });
</script>

<?php
// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';
