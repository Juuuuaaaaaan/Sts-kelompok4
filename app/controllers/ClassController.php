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

    // 1. Menampilkan Halaman Utama Kelas & History
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_kelas = $_POST['nama_kelas'];
            $deskripsi  = $_POST['deskripsi'];
            $created_by = $_SESSION['username']; 

            
            $class_pin = rand(100000, 999999);

            try {
                $this->db->beginTransaction();

                
                $queryClass = "INSERT INTO classes (id, nama_kelas, deskripsi, created_by) VALUES (?, ?, ?, ?)";
                $stmtClass = $this->db->prepare($queryClass);
                $stmtClass->execute([$class_pin, $nama_kelas, $deskripsi, $created_by]);

                
                $queryQuestion = "INSERT INTO questions (class_id, question_type, question_text, option_a, option_b, option_c, option_d, correct_option) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtQuestion = $this->db->prepare($queryQuestion);

                $types     = $_POST['type'] ?? [];
                $questions = $_POST['question'] ?? [];
                $corrects  = $_POST['correct'] ?? [];

                
                for ($i = 0; $i < count($questions); $i++) {
                    $q_type    = $types[$i] ?? 'pg';
                    $q_text    = $questions[$i];
                    $q_correct = $corrects[$i] ?? '';

                  
                    $opt_a = ($q_type === 'pg' && isset($_POST['option_a'][$i]) && $_POST['option_a'][$i] !== '') ? $_POST['option_a'][$i] : '';
                    $opt_b = ($q_type === 'pg' && isset($_POST['option_b'][$i]) && $_POST['option_b'][$i] !== '') ? $_POST['option_b'][$i] : '';
                    $opt_c = ($q_type === 'pg' && isset($_POST['option_c'][$i]) && $_POST['option_c'][$i] !== '') ? $_POST['option_c'][$i] : '';
                    $opt_d = ($q_type === 'pg' && isset($_POST['option_d'][$i]) && $_POST['option_d'][$i] !== '') ? $_POST['option_d'][$i] : '';

                    $stmtQuestion->execute([
                        $class_pin, 
                        $q_type, 
                        $q_text, 
                        $opt_a, 
                        $opt_b, 
                        $opt_c, 
                        $opt_d, 
                        $q_correct
                    ]);
                }

                $this->db->commit();
                
                header("Location: /class?msg=class_created&pin=" . $class_pin);
                exit;

            } catch (PDOException $e) {
                $this->db->rollBack();
                die("Gagal menyimpan data: " . $e->getMessage());
            }
        }
    }


    public function edit($id = null) {
       
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = $_GET['id'];
        }

        if (!$id) {
            $segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
            foreach ($segments as $segment) {
                if (is_numeric($segment)) {
                    $id = $segment;
                    break;
                }
            }
        }

        $stmt = $this->db->prepare("SELECT * FROM classes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $class = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$class) {
            echo "Data kelas tidak ditemukan! ID yang terbaca adalah: " . htmlspecialchars($id ?? 'KOSONG');
            exit;
        }

       
        $stmt_q = $this->db->prepare("SELECT * FROM questions WHERE class_id = :id");
        $stmt_q->bindParam(':id', $id);
        $stmt_q->execute();
        $questions = $stmt_q->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/edit.php';
    }


    public function update($id = null) {
       
        $id = $_POST['class_id'] ?? $id;

        if (!$id) {
            $segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
            foreach ($segments as $segment) {
                if (is_numeric($segment)) {
                    $id = $segment;
                    break;
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_kelas = $_POST['nama_kelas'];
            $deskripsi = $_POST['deskripsi'];

            try {
                $this->db->beginTransaction();

               
                $stmt = $this->db->prepare("UPDATE classes SET nama_kelas = :nama_kelas, deskripsi = :deskripsi WHERE id = :id");
                $stmt->bindParam(':nama_kelas', $nama_kelas);
                $stmt->bindParam(':deskripsi', $deskripsi);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

              
                $stmt_delete = $this->db->prepare("DELETE FROM questions WHERE class_id = :id");
                $stmt_delete->bindParam(':id', $id);
                $stmt_delete->execute();

               
                if (isset($_POST['question']) && is_array($_POST['question'])) {
                    $stmt_q = $this->db->prepare("INSERT INTO questions (class_id, question_type, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    foreach ($_POST['question'] as $index => $question) {
                        if (trim($question) === '') continue; 

                        $q_type = $_POST['type'][$index] ?? 'pg'; 
                        
                        $opt_a = ($q_type === 'pg') ? ($_POST['option_a'][$index] ?? '') : '';
                        $opt_b = ($q_type === 'pg') ? ($_POST['option_b'][$index] ?? '') : '';
                        $opt_c = ($q_type === 'pg') ? ($_POST['option_c'][$index] ?? '') : '';
                        $opt_d = ($q_type === 'pg') ? ($_POST['option_d'][$index] ?? '') : '';
                        
                        $correct = $_POST['correct'][$index] ?? '';

                        $stmt_q->execute([
                            $id,
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
                exit;
                
            } catch (PDOException $e) {
                $this->db->rollBack();
                echo "<script>alert('Gagal mengupdate kelas: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            }
        }
    }

   
    public function destroy($id = null) {
       
        if (!$id) {
            $segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
            foreach ($segments as $segment) {
                if (is_numeric($segment)) {
                    $id = $segment;
                    break;
                }
            }
            if (!$id) { $id = $_GET['id'] ?? null; }
        }

        $stmt = $this->db->prepare("DELETE FROM classes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            header("Location: /class");
            exit;
        } else {
            echo "<script>alert('Gagal menghapus kelas!'); window.history.back();</script>";
        }
    }

  
    public function complete($id = null) {
       
        if (!$id) {
            $segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
            foreach ($segments as $segment) {
                if (is_numeric($segment)) {
                    $id = $segment;
                    break;
                }
            }
        }

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

  
    public function clearHistory() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_SESSION['username'] ?? null;
        if (!$username) {
            header("Location: /login");
            exit();
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM completed_classes WHERE username = ?");
            $stmt->execute([$username]);
            
            $stmt_user = $this->db->prepare("UPDATE users SET points = 0 WHERE username = ?");
            $stmt_user->execute([$username]);

            header("Location: /class?msg=history_cleared");
            exit();
        } catch (PDOException $e) {
            die("Gagal menghapus riwayat kuis: " . $e->getMessage());
        }
    }

   
    public function deleteHistory() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_SESSION['username'] ?? null;
        $history_id = $_GET['id'] ?? null;

        if (!$username || !$history_id) {
            header("Location: /class");
            exit();
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM completed_classes WHERE id = ? AND username = ?");
            $stmt->execute([$history_id, $username]);

            header("Location: /class?msg=history_deleted");
            exit();
        } catch (PDOException $e) {
            die("Gagal menghapus riwayat kuis: " . $e->getMessage());
        }
    }

    
    public function deleteClass() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit();
        }

        $class_id = $_GET['id'] ?? null;
        if (!$class_id) {
            header("Location: /class");
            exit();
        }

        try {
            $this->db->beginTransaction();

            
            $stmt_questions = $this->db->prepare("DELETE FROM questions WHERE class_id = ?");
            $stmt_questions->execute([$class_id]);

            
            $stmt_completed = $this->db->prepare("DELETE FROM completed_classes WHERE class_id = ?");
            $stmt_completed->execute([$class_id]);

            
            $stmt_class = $this->db->prepare("DELETE FROM classes WHERE id = ?");
            $stmt_class->execute([$class_id]);

            $this->db->commit();
            header("Location: /class?msg=class_deleted");
            exit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            die("Gagal menghapus kelas: " . $e->getMessage());
        }
    }

  

  
    public function join() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit;
        }

     
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
           
            $pin = $_POST['pin'] ?? $_POST['class_id'] ?? '';

            if (empty(trim($pin))) {
                echo "<script>alert('PIN Kelas tidak boleh kosong!'); window.history.back();</script>";
                exit;
            }

            try {
               
                $stmt = $this->db->prepare("SELECT id FROM classes WHERE id = :id");
                $stmt->bindParam(':id', $pin);
                $stmt->execute();
                $class = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($class) {
                   
                    header("Location: /play_quiz?id=" . $pin);
                    exit;
                } else {
                   
                    echo "<script>alert('PIN salah atau kelas tidak ditemukan!'); window.history.back();</script>";
                    exit;
                }
            } catch (PDOException $e) {
                die("Error Database saat join kelas: " . $e->getMessage());
            }
        }
    }
}