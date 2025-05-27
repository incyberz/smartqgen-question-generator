<?php
$s = "SELECT SUM(a.poin) as sum_poin FROM tb_paket a 
WHERE a.id_pengunjung = $id_pengunjung 
AND status = 100 -- sudah di claim 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$lp = $d['sum_poin'] + $user['basic_lp'];
$lp_show = number_format($lp);
$info_as_ortu = $user['role'] >= 2 ? "
  <div class='tengah f20 pt4 mt4 border-top yellow'>Preview as Pelajar</div>
  <p class='f12 yellow'>Tampilan dibawah adalah preview tampilan sebagai Pelajar</p>
" : '';
$hitung_lp = "
  $info_as_ortu
  <h2 class='tengah f20 pt4 mt4 border-top'>Learning Points</h2>
  <div class='score-box'>
    💲<strong id=nilai>$lp_show</strong> LP
  </div>
  <div class='f14 mt2 hover btn-aksi' id=cairkan--toggle>Cairkan Points 👉</div>
  <div class=hideit id=cairkan>
    <div class='wadah kiri mt3' >
      <div class='mb1 tengah'>Hi Learners!! 😇</div>
      Learning Points dapat dicairkan menjadi Rupiah. Learning Points bisa kamu kumpulkan dari hasil menjawab Quiz. Prasyarat pencairan adalah:
      <ul class='pl4 f14'>
        <li>Saldo mencukupi ✅</li>
        <li>
          <span class='hover btn-aksi' id=span1--toggle>My Profile 100% 👉</span> 
          <span class=hideit id=span1>lengkapi foto, biodata, whatsapp tervalidasi Admin.</span>
        </li>
        <li>
          <span class='hover btn-aksi' id=span2--toggle>Approved by Akun Orangtua 👉 </span> 
          <span class='hideit' id=span2>Yakni Orangtua kamu, guru kamu, atau siapa saja yang menjadi Wali Murid untuk akun kamu di SmartQgen, ajak mereka untuk gabung ya! 😄 </span> 
        </li>
      </ul>
      So, lengkapi persyaratannya dahulu ya!!
      <button class='btn btn-secondary w-100 mt2 btn-aksi' id=cairkan--toggle--close>Close</button>
    </div>
  </div>
";
