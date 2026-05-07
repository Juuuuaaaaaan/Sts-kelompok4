<?php
session_start();
require_once '../core/Database.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$action = $_GET['action'] ?? '';
$history_id = $_GET['id'] ?? null;

try {
    if ($action === 'clear_all') {
        $stmt = $pdo->prepare("DELETE FROM completed_classes WHERE username = ?");
        $stmt->execute([$username]);
        
        header("Location: class.php?msg=history_cleared");
        exit();
        
    } elseif ($history_id) {
        $stmt = $pdo->prepare("DELETE FROM completed_classes WHERE id = ? AND username = ?");
        $stmt->execute([$history_id, $username]);
        
        header("Location: class.php?msg=history_deleted");
        exit();
        
    } else {
        header("Location: class.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>