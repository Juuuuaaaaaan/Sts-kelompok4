<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Jawaban yang benar (Kunci Jawaban)
    $kunci_soal_1 = "A";
    $kunci_soal_2 = "C";

    // Ambil jawaban dari user
    $jawaban_1 = $_POST['soal_1'] ?? '';
    $jawaban_2 = $_POST['soal_2'] ?? '';

    $skor = 0;
    $total_soal = 2;

    // Cek Soal 1
    if ($jawaban_1 === $kunci_soal_1) {
        $skor++;
    }

    // Cek Soal 2
    if ($jawaban_2 === $kunci_soal_2) {
        $skor++;
    }

    // Hitung Nilai
    $nilai_akhir = ($skor / $total_soal) * 100;

    // Simpan hasil ke session agar bisa ditampilkan di halaman hasil
    $_SESSION['terakhir_skor'] = $nilai_akhir;
    $_SESSION['terakhir_status'] = ($nilai_akhir >= 70) ? "Lulus" : "Gagal";

    // Jika lulus, kita anggap streak bertambah (simulasi)
    if ($nilai_akhir >= 70) {
        // Di sini nantinya kamu bisa tambah kode UPDATE database untuk nambah streak user
    }

    header("Location: ../views/quiz_result.php");
    exit();
}