<style>
  .info-soal {
    text-align: left;
    font-size: 14px;
    padding-left: 15px;
    border-top: solid 1px #ccc;
    border-bottom: solid 1px #ccc;
    padding: 10px 0;
    margin: 15px 0;
    display: grid;
    grid-template-columns: 50% 50%;
  }

  .kalimat-soal {
    font-size: 18px;
    color: yellow;
    font-weight: 600;
  }

  .blok-opsi {
    display: grid;
    grid-template-columns: 50% 50%;
    gap: 5px;
    margin-top: 10px;
    margin-bottom: 30px;
  }

  .opsi {
    background: linear-gradient(#005, #00a);
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: .3s;
    color: #aa5;
  }

  .opsi-selected {
    background: linear-gradient(#aa0, #330);
    border: solid 2px yellow;
    color: white;
    font-weight: 600;
  }

  .opsi:hover {
    background: linear-gradient(#aa0, #330);
    letter-spacing: .5px;
    color: white;
  }

  .blok-timer {
    display: flex;
    justify-content: center;
    font-size: 30px;
    font-family: 'consolas', 'Courier New', Courier, monospace;
    margin-bottom: 20px;
  }
</style>
<?php
$max_soal = 5;

$s = "SELECT * FROM tb_soal WHERE id=1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$debug = '';
$debug = "<i class=red>id: $id_pengunjung</i>";

echo "
  <h1>Quiz Started</h1>
  <hr>
  <p class='pt3 mb2'>Halo $nama_pengunjung! $debug<br>Jawablah pertanyaan berikut: </p>
  <div class=info-soal>
    <div><b>Soal</b>: Fisika</div>
    <div class=right><b>Tingkat</b>: SMA</div>
    <div><b>Materi</b>: GLBB</div>
    <div class=right><b>Level</b>: <i>medium</i></div>
  </div>
  <div class=kalimat-soal>
    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquid dignissimos voluptatum ipsum voluptatibus qui eius odio assumenda voluptatem odit, asperiores ut fugiat quos similique laudantium eos.
  </div>
  
  <div class=blok-opsi>
    <div class='opsi opsi-selected'>asdas</div>
    <div class='opsi'>asddee</div>
    <div class='opsi'>zxcsssads</div>
    <div class='opsi'>jjktrg</div>
  </div>
  <div class=blok-timer>
    <div class=''>00</div>
    <div class=''>:</div>
    <div class=''>00</div>
    <div class=''>:</div>
    <div class=''>00</div>
  </div>
  <button class='btn btn-primary w-100 btn-lg'>Submit</button>



";
