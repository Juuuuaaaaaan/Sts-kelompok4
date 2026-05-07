<?php
namespace App\Controllers;
use PDO;
use PDOException;

class ClassController {
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

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit;
        }

        $username = $_SESSION['username'];

        $stmt_my_classes = $this->db->prepare("SELECT * FROM classes WHERE created_by = :username");
        $stmt_my_classes->execute(['username' => $username]);
        $my_classes = $stmt_my_classes->fetchAll(PDO::FETCH_ASSOC);

        $query_history = "SELECT c.*, cc.id as history_id, cc.points_earned 
                          FROM completed_classes cc 
                          JOIN classes c ON cc.class_id = c.id 
                          WHERE cc.username = :username 
                          ORDER BY cc.id DESC";
        $stmt_history = $this->db->prepare($query_history);
        $stmt_history->execute(['username' => $username]);
        $history_classes = $stmt_history->fetchAll(PDO::FETCH_ASSOC);

        $msg = $_GET['msg'] ?? '';

        require_once __DIR__ . '/../views/class.php';
    }

    public function create() {
        require_once __DIR__ . '/../views/classes/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_kelas = $_POST['nama_kelas'];
            $deskripsi = $_POST['deskripsi'];

            $stmt = $this->db->prepare("INSERT INTO classes (nama_kelas, deskripsi) VALUES (:nama_kelas, :deskripsi)");
            $stmt->bindParam(':nama_kelas', $nama_kelas);
            $stmt->bindParam(':deskripsi', $deskripsi);
            
            if ($stmt->execute()) {
                header("Location: /class");
                exit;
            } else {
                echo "<script>alert('Gagal menambah kelas!'); window.history.back();</script>";
            }
        }
    }

    public function edit($id) {
        $stmt = $this->db->prepare("SELECT * FROM classes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $class = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$class) {
            echo "Data kelas tidak ditemukan!";
            exit;
        }

        require_once __DIR__ . '/../views/classes/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
            $nama_kelas = $_POST['nama_kelas'];
            $deskripsi = $_POST['deskripsi'];

            $stmt = $this->db->prepare("UPDATE classes SET nama_kelas = :nama_kelas, deskripsi = :deskripsi WHERE id = :id");
            $stmt->bindParam(':nama_kelas', $nama_kelas);
            $stmt->bindParam(':deskripsi', $deskripsi);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                header("Location: /class");
                exit;
            } else {
                echo "<script>alert('Gagal mengupdate kelas!'); window.history.back();</script>";
            }
        }
    }

    public function destroy($id) {
        $stmt = $this->db->prepare("DELETE FROM classes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            header("Location: /class");
            exit;
        } else {
            echo "<script>alert('Gagal menghapus kelas!'); window.history.back();</script>";
        }
    }

    public function complete($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit;
        }

        $stmt = $this->db->prepare("UPDATE user_classes SET status = 'completed' WHERE class_id = :id AND username = :username");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $_SESSION['username']);

        if ($stmt->execute()) {
            header("Location: /class");
            exit;
        } else {
            echo "<script>alert('Gagal menyelesaikan kelas!'); window.history.back();</script>";
        }
    }
}