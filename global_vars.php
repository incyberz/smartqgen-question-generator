<?php
# ============================================================
# GLOBAL VARIABLES
# ============================================================
$now = date('Y-m-d H:i:s');
$arr_hari = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$arr_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

$https_api_wa = 'https://api.whatsapp.com/send';
$text_wa_from = "\n\n```From: Smart qgen App \nat $now```";

$arr = explode('?', $_SERVER['REQUEST_URI']);
$nama_server = "$_SERVER[REQUEST_SCHEME]://$_SERVER[SERVER_NAME]$arr[0]";

$null = '<i class="abu">null</i>';
$img_loading = "<img src=assets/img/loading.gif height=30px width=30px>";
