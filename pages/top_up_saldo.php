<?php
$top_up_saldo = "
  <form method=post class=mb4>
    <h2 class='tengah f20 pt4 mt4'>Top Up Saldo</h2>
    <div>Saya bersedia menyediakan reward sebesar Rp:</div>
    <input class='tengah consolas f30 mt1 mb1' required min=10000 max=1000000 type=number step=1000 name=nominal-top-up id=nominal-top-up>
    <div class=mb2>Untuk anak didik saya dengan syarat Penukaran dengan Learning Point, hasil mereka belajar di aplikasi ini.</div>
    <button class='btn btn-success w-100' onclick='return confirm(`Confirm Top Up?`)'>Top Up</button>
    <div class='mt4 border-top pt2 f12'><b>Catatan</b>: Nominal Top Up hanyalah <b>Virtual Saldo</b>, sebuah janji untuk memberi semangat kepada anak didik Anda, tidak ada online payment, atau melibatkan transaksi bank.</div>
  </form>
";
