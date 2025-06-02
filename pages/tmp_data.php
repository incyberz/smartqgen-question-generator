<?php
$s = "SELECT * FROM tb_tmp WHERE username = '$username'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tmp = mysqli_fetch_assoc($q);
