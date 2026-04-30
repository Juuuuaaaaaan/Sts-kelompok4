<?php
session_start();

require_once dirname(__DIR__, 2) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];

        if ($password !== $confirm) {
            header("Location: ../views/register.php?error=Password dan Confirm Password tidak sama!");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $hashed_password])) {
                header("Location: ../views/login.php?error=Registrasi berhasil! Silakan Log In.");
                exit();
            }
        } catch(PDOException $e) {
            header("Location: ../views/register.php?error=Gagal mendaftar. Email mungkin sudah terdaftar.");
            exit();
        }
    }

    elseif ($action === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['username'] = $user['username'];
                    header("Location: ../views/class.php");
                    exit();
                } else {
                    header("Location: ../views/login.php?error=Password atau Email salah!");
                    exit();
                }
            } else {
                header("Location: ../views/login.php?error=Email tidak terdaftar!");
                exit();
            }
        } catch(PDOException $e) {
            header("Location: ../views/login.php?error=Terjadi kesalahan sistem.");
            exit();
        }
    }
}
?>