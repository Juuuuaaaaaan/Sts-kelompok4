<?php
session_start();
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kunci_soal_1 = "A";
    $kunci_soal_2 = "C";

    $jawaban_1 = $_POST['soal_1'] ?? '';
    $jawaban_2 = $_POST['soal_2'] ?? '';
 
    $skor = 0;
    $total_soal = 2;
 
    if ($jawaban_1 === $kunci_soal_1) {
        $skor++;
    }
 
    if ($jawaban_2 === $kunci_soal_2) {
        $skor++;
    }
 
    $nilai_akhir = ($skor / $total_soal) * 100;
 
    $_SESSION['terakhir_skor'] = $nilai_akhir;
    $_SESSION['terakhir_status'] = ($nilai_akhir >= 70) ? "Lulus" : "Gagal";
 
    if ($nilai_akhir >= 70) {
    }
 
    header("Location: ../views/quiz_result.php");
    exit();
}