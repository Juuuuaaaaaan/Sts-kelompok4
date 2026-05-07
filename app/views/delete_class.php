<?php
session_start();
require_once '../core/Database.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$class_id = $_GET['id'] ?? null;
$username = $_SESSION['username'];

if ($class_id) {
    try {
        $stmt_check = $pdo->prepare("SELECT created_by FROM classes WHERE id = ?");
        $stmt_check->execute([$class_id]);
        $class = $stmt_check->fetch();

        if ($class && $class['created_by'] === $username) {
            $pdo->beginTransaction();

            $stmt_del_q = $pdo->prepare("DELETE FROM questions WHERE class_id = ?");
            $stmt_del_q->execute([$class_id]);

            $stmt_del_comp = $pdo->prepare("DELETE FROM completed_classes WHERE class_id = ?");
            $stmt_del_comp->execute([$class_id]);

            $stmt_del_class = $pdo->prepare("DELETE FROM classes WHERE id = ?");
            $stmt_del_class->execute([$class_id]);

            $pdo->commit();
            header("Location: class.php?msg=deleted");
            exit();
        } else {
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