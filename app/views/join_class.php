<?php
session_start();
require_once '../core/Database.php';

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
    $stmt = $pdo->prepare("SELECT id FROM classes WHERE id = ?");
    $stmt->execute([$class_pin]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($class) {
        $stmt_check = $pdo->prepare("SELECT id FROM completed_classes WHERE username = ? AND class_id = ?");
        $stmt_check->execute([$_SESSION['username'], $class_pin]);
        
        if ($stmt_check->fetch()) {
            header("Location: class.php?msg=played");
            exit();
        }

        header("Location: play_quiz.php?id=" . $class['id']);
        exit();
        
    } else {
        header("Location: class.php?msg=invalid");
        exit();
    }

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>