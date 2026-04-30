<?php
session_start();
require_once dirname(__DIR__, 2) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $class_id = $_POST['class_id'];
    $points = $_POST['points'];

    // 1. Catat bahwa user sudah menyelesaikan kelas ini
    $stmt1 = $conn->prepare("INSERT INTO completed_classes (username, class_id, points_earned) VALUES (?, ?, ?)");
    $stmt1->bind_param("sii", $username, $class_id, $points);
    $stmt1->execute();

    // 2. Tambahkan Poin (Streak) ke tabel users
    // Asumsi tabel users punya kolom bernama 'streak'
    $stmt2 = $conn->prepare("UPDATE users SET streak = streak + ? WHERE username = ?");
    $stmt2->bind_param("is", $points, $username);
    $stmt2->execute();

    // 3. Kembali ke halaman kelas
    header("Location: ../views/class.php?success=Kelas selesai, poin ditambahkan!");
    exit();
}
?>