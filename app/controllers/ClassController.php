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
        require_once __DIR__ . '/../views/create_class.php';
    }

public function store() {
        // Cek login untuk memastikan ada $_SESSION['username']
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_kelas = $_POST['nama_kelas'];
            $deskripsi = $_POST['deskripsi'];
            $username = $_SESSION['username'];

        try {
                $this->db->beginTransaction();

                // 1. Insert ke tabel classes
                $stmt = $this->db->prepare("INSERT INTO classes (nama_kelas, deskripsi, created_by) VALUES (?, ?, ?)");
                $stmt->execute([$nama_kelas, $deskripsi, $username]);
                $class_id = $this->db->lastInsertId();

                // 2. Insert soal-soal ke tabel questions (DIPERBAIKI UNTUK DUKUNG ISIAN)
                if (isset($_POST['question']) && is_array($_POST['question'])) {
                    $stmt_q = $this->db->prepare("INSERT INTO questions (class_id, question_type, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    foreach ($_POST['question'] as $index => $question) {
                        $q_type = $_POST['type'][$index]; // 'pg' atau 'isian'
                        
                        // Jika isian, kosongkan opsi A-D
                        $opt_a = ($q_type === 'pg') ? $_POST['option_a'][$index] : '';
                        $opt_b = ($q_type === 'pg') ? $_POST['option_b'][$index] : '';
                        $opt_c = ($q_type === 'pg') ? $_POST['option_c'][$index] : '';
                        $opt_d = ($q_type === 'pg') ? $_POST['option_d'][$index] : '';
                        
                        // Menangkap kunci jawaban (dari dropdown PG atau input Isian)
                        $correct = $_POST['correct'][$index];

                        $stmt_q->execute([
                            $class_id,
                            $q_type,
                            $question,
                            $opt_a,
                            $opt_b,
                            $opt_c,
                            $opt_d,
                            $correct
                        ]);
                    }
                }

                $this->db->commit();
                header("Location: /class");
                exit();
                
            } catch (\PDOException $e) {
                $this->db->rollBack();
                echo "<script>alert('Gagal menyimpan data: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
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

    // 1. Fungsi untuk menampilkan halaman form Join Class
    public function join() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit;
        }

        // Pastikan nama file view-nya sesuai (misal: join_class.php)
        require_once __DIR__ . '/../views/join_class.php';
    }

public function processJoin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $class_id = $_POST['class_pin'] ?? null; 
            $username = $_SESSION['username'];

            if ($class_id) {
                try {
                    // 1. CEK DULU APAKAH KELASNYA ADA DI DATABASE
                    $cek_kelas = $this->db->prepare("SELECT id FROM classes WHERE id = ?");
                    $cek_kelas->execute([$class_id]);
                    
                    if (!$cek_kelas->fetch()) {
                        // Jika kelas tidak ditemukan, munculkan pesan ramah ini
                        echo "<script>alert('Oops! Game PIN tidak ditemukan. Coba cek lagi kodenya ya!'); window.history.back();</script>";
                        exit;
                    }

                    $stmt = $this->db->prepare("INSERT INTO user_classes (username, class_id, status) VALUES (?, ?, 'in_progress')");
                    $stmt->execute([$username, $class_id]);

                    header("Location: /play_quiz?id=" . $class_id);
                    exit;
                } catch (\PDOException $e) {
                    echo "<script>alert('Error dari Database: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
                }
            } else {
                echo "<script>alert('Game PIN tidak boleh kosong!'); window.history.back();</script>";
            }
        }
    }
}