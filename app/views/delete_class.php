<?php
session_start();
require_once '../db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$class_id = $_GET['id'] ?? null;
$username = $_SESSION['username'];

if ($class_id) {
    try {
        // 1. Pastikan user adalah pemilik kelas tersebut sebelum menghapus
        $stmt_check = $pdo->prepare("SELECT created_by FROM classes WHERE id = ?");
        $stmt_check->execute([$class_id]);
        $class = $stmt_check->fetch();

        if ($class && $class['created_by'] === $username) {
            // Mulai Transaksi (agar jika satu gagal, semua batal)
            $pdo->beginTransaction();

            // 2. Hapus soal-soal yang ada di kelas tersebut
            $stmt_del_q = $pdo->prepare("DELETE FROM questions WHERE class_id = ?");
            $stmt_del_q->execute([$class_id]);

            // 3. Hapus riwayat pengerjaan user lain di kelas tersebut
            $stmt_del_comp = $pdo->prepare("DELETE FROM completed_classes WHERE class_id = ?");
            $stmt_del_comp->execute([$class_id]);

            // 4. Hapus kelasnya
            $stmt_del_class = $pdo->prepare("DELETE FROM classes WHERE id = ?");
            $stmt_del_class->execute([$class_id]);

            $pdo->commit();
            header("Location: class.php?msg=deleted");
            exit();
        } else {
            // Jika bukan pemilik, tolak akses
            die("You do not have permission to delete this class.");
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Failed to delete class: " . $e->getMessage());
    }
} else {
    header("Location: class.php");
    exit();
}
?>