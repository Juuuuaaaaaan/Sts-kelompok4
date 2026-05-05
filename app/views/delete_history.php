<?php
session_start();
require_once '../db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$action = $_GET['action'] ?? '';
$history_id = $_GET['id'] ?? null;

try {
    if ($action === 'clear_all') {
        // Hapus SEMUA riwayat user ini
        $stmt = $pdo->prepare("DELETE FROM completed_classes WHERE username = ?");
        $stmt->execute([$username]);
        
        // Kembalikan ke halaman class dengan pesan sukses
        header("Location: class.php?msg=history_cleared");
        exit();
        
    } elseif ($history_id) {
        // Hapus SATU riwayat berdasarkan ID completed_classes
        $stmt = $pdo->prepare("DELETE FROM completed_classes WHERE id = ? AND username = ?");
        $stmt->execute([$history_id, $username]);
        
        // Kembalikan ke halaman class dengan pesan sukses
        header("Location: class.php?msg=history_deleted");
        exit();
        
    } else {
        // Jika tidak ada aksi apa-apa, kembalikan ke class.php
        header("Location: class.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>