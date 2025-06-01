<?php
akses('konfigurasi_pencairan');
include 'user-process.php';
$info_pencairan = 'Jika tidak ditentukan, Konfigurasi Pencairan Anda mengikuti Default Configurations';


$s = "DESCRIBE tb_config";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$Desc = [];
while ($d = mysqli_fetch_assoc($q))  $Desc[$d['Field']] = $d;

$configs = '';
foreach ($Desc as $k => $rv) {
  if ($rv['Key'] == 'PRI') continue; // PKey
  $kolom = ucwords(str_replace('_', ' ', $k));
  if (strpos("salt$rv[Type]", 'int(')) {
    $type =  'number';
  } elseif (strpos("salt$rv[Type]", 'char(')) {
    $type =  'text';
  } else {
    stop("Undefined input-type untuk field-Type: $rv[Type]");
  }

  $default_value = $config_default[$k]['value'] ?? stop("Belum ada default value untuk field [$k]");
  $default_min = $config_default[$k]['min'] ?? stop("Belum ada default min untuk field [$k]");
  $default_max = $config_default[$k]['max'] ?? stop("Belum ada default max untuk field [$k]");
  $step = $config_default[$k]['step'] ?? 1;
  $value = $config[$k] ?? $default_value;
  $default_info = (!$config[$k] || $default_value == $config[$k]) ? '' : "default: $default_value";

  $configs .= "
    <div class='mb3'>
      <label class='mb1'>$kolom</label>
      <input 
        class='consolas f24 tengah'
        type='$type'
        step='$step'
        name='$k'
        id='$k'
        value='$value'
        min='$default_min'
        max='$default_max'
      />
      <div class='f12 abu miring mt1'>$default_info</div>
    </div>
  ";
}



echo "
  <form method=post class='w-500 ortu mb4'>
    <h2 class='tengah f20 pt4 mt4'>*️⃣ Konfigurasi Pencairan</h2>
    <p class='f12 border-bottom pb2 mb3'>$info_pencairan</p>
    <div>
      $configs
    </div>
    <button class='btn btn-primary w-100 mb4' name=btn_simpan_konfigurasi>Simpan Konfigurasi</button>
    <a href=?>Back to Home</a>

  </form>
";
