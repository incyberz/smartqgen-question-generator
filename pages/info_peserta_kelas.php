<?php
if ($user['jumlah_kelas']) {
  $default_src = 'assets/img/pelajar.png';

  $s = "SELECT * FROM tb_kelas a WHERE username='$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $tr_kelas = '';
  $tr = '';
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;

    $s2 = "SELECT a.*,
    b.nama as nama_peserta,
    b.image,  
    b.id_pengunjung,
    (SELECT poin FROM tb_tmp WHERE username=a.username) tmp_poin,  
    (SELECT play_count FROM tb_tmp WHERE username=a.username) tmp_play_count,  
    (SELECT nilai FROM tb_tmp WHERE username=a.username) tmp_nilai
    FROM tb_peserta a 
    JOIN tb_user b ON a.username=b.username 
    WHERE a.id_kelas=$d[id] 
    ORDER BY tmp_poin DESC
    ";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $pesertas = 'belum ada peserta pada kelas ini';
    if (mysqli_num_rows($q2)) {
      $pesertas = '';


      while ($d2 = mysqli_fetch_assoc($q2)) {
        $poin = $d2['tmp_poin'];
        $poin_show = number_format($poin);
        $play_count = $d2['tmp_play_count'] ?? 0;
        $nilai = $d2['tmp_nilai'];
        $nilai_show = $play_count > 1 ? "$nilai x $play_count" : $nilai;
        $user_src = "assets/img/user/$d2[image]";
        $src = ($d2['image'] and file_exists($user_src)) ? $user_src : $default_src;
        $src = $user_src;

        $nama_peserta = ucwords(strtolower($d2['nama_peserta']));
        $pesertas .= "
          <div class='bordered mb3 br5 p2 tengah gradasi-toska row-peserta'>
            <a href=?detail_peserta&username=$d2[username]>
              <div><img src=$src class=foto_profil></div>
              <div>$nama_peserta</div>
            </a>
            <div class='flexy flex-between gap3 border-top mt2 pt2 f12'>
              <div>🎖️ $poin_show LP</div>
              <div>📊 $nilai_show</div>
            </div>
          </div>
        ";
      }
    }



    $tr .= "
      <tr>
        <td class='tengah gradasi-toska f20 yellow'>Kelas $d[nama_kelas]</td>
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

  $info_peserta_kelas = "
    <h2 class='tengah f20 pt4 mt4 border-top'>Peserta Kelas</h2>
    <table class='table mb3'>
      $tr
    </table>";
}
