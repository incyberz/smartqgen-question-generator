<?php
$id_soal = $_POST['id_soal'];
$id_siswa = $_POST['id_siswa'];
$jawaban_siswa = $_POST['jawaban'];

// Update ke jawaban_siswa
$conn = new mysqli("localhost", "root", "", "db_fisika");
$conn->query("UPDATE jawaban_siswa SET jawaban_siswa = '$jawaban_siswa' WHERE id_siswa = $id_siswa AND id_soal = $id_soal");
echo "Jawaban berhasil disimpan!";
