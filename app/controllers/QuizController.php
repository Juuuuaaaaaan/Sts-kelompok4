<?php
namespace App\Controllers;

use PDO;
use PDOException;

class QuizController {
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

   
    public function play() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit();
        }

        $class_id = isset($_GET['id']) ? $_GET['id'] : null;

        if (!$class_id) {
            header("Location: /class");
            exit();
        }

        try {
           
            $stmt_class = $this->db->prepare("SELECT * FROM classes WHERE id = ?");
            $stmt_class->execute([$class_id]);
            $class = $stmt_class->fetch(PDO::FETCH_ASSOC);

            if (!$class) {
                die("Kelas tidak ditemukan.");
            }

           
            $stmt_questions = $this->db->prepare("SELECT * FROM questions WHERE class_id = ?");
            $stmt_questions->execute([$class_id]);
            $questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Error Database: " . $e->getMessage());
        }

       
        require_once __DIR__ . '/../views/play_quiz.php';
    }

   
    public function submit() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $class_id = $_POST['class_id'] ?? null;
            $answers = $_POST['answer'] ?? []; 

            if (!$class_id) {
                header("Location: /class");
                exit();
            }

            try {
                
                $stmt = $this->db->prepare("SELECT id, question_text, correct_option FROM questions WHERE class_id = ?");
                $stmt->execute([$class_id]);
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $skor = 0; 
                $total_soal = count($questions);
                $detail_jawaban = [];

                if ($total_soal > 0) {
                    foreach ($questions as $q) {
                        $q_id = $q['id'];
                        
                        $kunci = strtolower(trim($q['correct_option'] ?? ''));
                        $jawaban_siswa = isset($answers[$q_id]) ? strtolower(trim($answers[$q_id])) : '';

                        $is_correct = ($kunci === $jawaban_siswa);
                        if ($is_correct) {
                            $skor++; 
                        }

                        $detail_jawaban[] = [
                            'pertanyaan' => $q['question_text'],
                            'jawaban_siswa' => $jawaban_siswa,
                            'kunci_jawaban' => $kunci,
                            'is_correct' => $is_correct
                        ];
                    }
                    
                    $nilai_akhir = ($skor / $total_soal) * 100;
                } else {
                    $nilai_akhir = 0;
                }

               
                $_SESSION['quiz_result'] = [
                    'skor' => $nilai_akhir,
                    'total_soal' => $total_soal,
                    'benar' => $skor,
                    'detail' => $detail_jawaban
                ];

                $username = $_SESSION['username'] ?? '';
                if ($username) {
                   
                    $points_earned = $skor * 100;

                  
                    $stmt_check = $this->db->prepare("SELECT id, points_earned FROM completed_classes WHERE username = ? AND class_id = ?");
                    $stmt_check->execute([$username, $class_id]);
                    $existing_record = $stmt_check->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$existing_record) {
                       
                        $stmt_insert = $this->db->prepare("INSERT INTO completed_classes (username, class_id, points_earned) VALUES (?, ?, ?)");
                        $stmt_insert->execute([$username, $class_id, $points_earned]);

                        
                        $stmt_update_user = $this->db->prepare("UPDATE users SET points = points + ? WHERE username = ?");
                        $stmt_update_user->execute([$points_earned, $username]);
                    } else {
                       
                        $old_points = $existing_record['points_earned'];
                        if ($points_earned > $old_points) {
                            $selisih_poin = $points_earned - $old_points;

                            
                            $stmt_update_history = $this->db->prepare("UPDATE completed_classes SET points_earned = ? WHERE id = ?");
                            $stmt_update_history->execute([$points_earned, $existing_record['id']]);

                            
                            $stmt_update_user = $this->db->prepare("UPDATE users SET points = points + ? WHERE username = ?");
                            $stmt_update_user->execute([$selisih_poin, $username]);
                        }
                    }
                }

       
                header("Location: /quiz_result");
                exit();

            } catch (PDOException $e) {
                die("Error Database saat submit kuis: " . $e->getMessage());
            }
        }
    }

   
    public function result() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['quiz_result'])) {
            header("Location: /class");
            exit();
        }

        $result = $_SESSION['quiz_result'];
        require_once __DIR__ . '/../views/quiz_result.php';
    }
}