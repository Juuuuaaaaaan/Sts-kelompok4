<?php
session_start();
require_once dirname(__DIR__) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $class_id = $_POST['class_id'];
    $points = $_POST['points'];

    // 1. Catat bahwa user sudah menyelesaikan kelas ini
    // Ubah $conn menjadi $pdo, dan hapus bind_param karena PDO bisa langsung mengeksekusi array
    $stmt1 = $pdo->prepare("INSERT INTO completed_classes (username, class_id, points_earned) VALUES (?, ?, ?)");
    $stmt1->execute([$username, $class_id, $points]);

    // 2. Tambahkan Poin (Streak) ke tabel users
    // Asumsi tabel users punya kolom bernama 'streak'
    $stmt2 = $pdo->prepare("UPDATE users SET points = points + ? WHERE username = ?");
    $stmt2->execute([$points, $username]);

    // 3. Kembali ke halaman kelas
    header("Location: ../views/class.php?success=Kelas selesai, poin ditambahkan!");
    exit();
}
?>