<?php
$s = "SELECT 1 FROM tb_ortu WHERE username='$username'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

// auto insert jika num_rows == 0
if (mysqli_num_rows($q) == 0) {
  $s = "INSERT INTO tb_ortu (username) VALUES ('$username')";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
}

?>
<!-- form update posisi ortu, 1 = ayah, 2 = ibu, 3 = wali -->
<form method=post class="wadah gradasi-kuning">
  <h2>Posisi Orangtua</h2>
  <p class="darkblue mb2">Posisi Saya sebagai:</p>
  <button class="btn w-100" value="1" name=posisi_ortu>Ayah</button>
  <button class="btn w-100" value="2" name=posisi_ortu>Ibu</button>
  <button class="btn w-100" value="3" name=posisi_ortu>Wali</button>
</form>