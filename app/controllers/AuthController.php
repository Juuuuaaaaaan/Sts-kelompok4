<?php
namespace App\Controllers;
use PDO;
use PDOException;

class AuthController {
    private $db;

    public function __construct() {
        $host = 'localhost';
        $dbname = 'funstreak'; 
        $user = 'root'; 
        $pass = ''; 

        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Koneksi Database Gagal: " . $e->getMessage());
        }
    }

    public function loginView() {
        require_once __DIR__ . '/../views/login.php';
    }

    public function registerView() {
        require_once __DIR__ . '/../views/register.php';
    }

    public function registerPost() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email']; 
            $password = $_POST['password'];
            $confirm = $_POST['confirm'];

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if ($password !== $confirm) {
                $_SESSION['error'] = 'Password dan Konfirmasi Password tidak cocok!';
                header("Location: /register");
                exit;
            }

            $stmtUser = $this->db->prepare("SELECT * FROM users WHERE username = :username");
            $stmtUser->bindParam(':username', $username);
            $stmtUser->execute();

            if ($stmtUser->rowCount() > 0) {
                $_SESSION['error'] = 'Username sudah terdaftar! Silakan gunakan nama lain.';
                header("Location: /register");
                exit;
            }
            $stmtEmail = $this->db->prepare("SELECT * FROM users WHERE email = :email");
            $stmtEmail->bindParam(':email', $email);
            $stmtEmail->execute();

            if ($stmtEmail->rowCount() > 0) {
                $_SESSION['error'] = 'Email sudah digunakan! Silakan gunakan email lain atau Login.';
                header("Location: /register");
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $insertStmt->bindParam(':username', $username);
            $insertStmt->bindParam(':email', $email);
            $insertStmt->bindParam(':password', $hashedPassword);
            
            if ($insertStmt->execute()) {
                $_SESSION['success'] = 'Registrasi berhasil! Silakan Login.';
                header("Location: /login");
                exit;
            } else {
                $_SESSION['error'] = 'Terjadi kesalahan saat registrasi.';
                header("Location: /register");
                exit;
            }
        }
    }

    public function loginPost() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginInput = $_POST['username']; 
            $password = $_POST['password'];

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :input OR email = :input");
            $stmt->bindParam(':input', $loginInput);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email']; 
                
                // ⚠️ TAMBAHKAN 2 BARIS INI AGAR AVATAR & WARNA LANGSUNG TERBACA SEJAK AWAL LOGIN ⚠️
                $_SESSION['avatar'] = $user['avatar']; 
                $_SESSION['theme_color'] = $user['theme_color']; 

                header("Location: /"); 
                exit();

            } else {
                $_SESSION['error'] = 'Username/Email tidak terdaftar atau Password salah!';
                header("Location: /login");
                exit;
            }
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        header("Location: /");
        exit;
    }
}