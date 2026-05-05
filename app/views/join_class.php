<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: class.php");
    exit();
}

$class_pin = trim($_POST['class_pin'] ?? '');

if (empty($class_pin)) {
    header("Location: class.php?msg=empty");
    exit();
}

try {
    // 1. Cek apakah kelas dengan PIN (ID) tersebut ada
    $stmt = $pdo->prepare("SELECT id FROM classes WHERE id = ?");
    $stmt->execute([$class_pin]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($class) {
        // 2. Cek apakah user sudah pernah menyelesaikan kelas ini
        $stmt_check = $pdo->prepare("SELECT id FROM completed_classes WHERE username = ? AND class_id = ?");
        $stmt_check->execute([$_SESSION['username'], $class_pin]);
        
        if ($stmt_check->fetch()) {
            // Jika sudah pernah main, kembalikan dengan pesan error
            header("Location: class.php?msg=played");
            exit();
        }

        // 3. Jika kelas ada dan belum pernah dimainkan, gas masuk ke kuis!
        header("Location: play_quiz.php?id=" . $class['id']);
        exit();
        
    } else {
        // Jika kelas tidak ditemukan
        header("Location: class.php?msg=invalid");
        exit();
    }

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>