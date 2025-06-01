<style>
  .debet {
    background: linear-gradient(#efe, #afa);
  }


  .kredit {
    background: linear-gradient(#ffd, #ffa);
  }

  .request-pencairan {
    background: #d33;
    color: white;
  }

  .request-pencairan td,
  .debet td,
  .kredit td {
    background: none;
  }

  .nominal {
    font-family: 'Courier New', Courier, monospace;
    text-align: right;
  }
</style>
<?php
akses('manage_trx');
include 'user-process.php';
include 'request_pencairan.php';
include 'history_trx.php';
include 'top_up_saldo.php';


echo "
<div class='w-500 ortu'>
  $request_pencairan
  $history_trx
  $top_up_saldo
</div>
";
