<?php
namespace App\Controllers;

use PDO;
use PDOException;

class QuizController {
    private $db;

    public function __construct() {
        // Sesuaikan kredensial database jika berbeda
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

    // 1. Menampilkan Halaman Soal Kuis (GET /play_quiz)
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
            // Ambil data kelas
            $stmt_class = $this->db->prepare("SELECT * FROM classes WHERE id = ?");
            $stmt_class->execute([$class_id]);
            $class = $stmt_class->fetch(PDO::FETCH_ASSOC);

            if (!$class) {
                die("Kelas tidak ditemukan.");
            }

            // Ambil list soal berdasarkan class_id
            $stmt_questions = $this->db->prepare("SELECT * FROM questions WHERE class_id = ?");
            $stmt_questions->execute([$class_id]);
            $questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Error Database: " . $e->getMessage());
        }

        // Panggil halaman view kuis
        require_once __DIR__ . '/../views/play_quiz.php';
    }

    // 2. Memproses Jawaban yang Dikirim Siswa (POST /play_quiz)
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
                // Ambil kunci jawaban asli dari database
                $stmt = $this->db->prepare("SELECT id, question_text, correct_option FROM questions WHERE class_id = ?");
                $stmt->execute([$class_id]);
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $skor = 0; // Menghitung total jawaban benar
                $total_soal = count($questions);
                $detail_jawaban = [];

                if ($total_soal > 0) {
                    foreach ($questions as $q) {
                        $q_id = $q['id'];
                        
                        $kunci = strtolower(trim($q['correct_option'] ?? ''));
                        $jawaban_siswa = isset($answers[$q_id]) ? strtolower(trim($answers[$q_id])) : '';

                        $is_correct = ($kunci === $jawaban_siswa);
                        if ($is_correct) {
                            $skor++; // Tambah 1 jika benar
                        }

                        $detail_jawaban[] = [
                            'pertanyaan' => $q['question_text'],
                            'jawaban_siswa' => $jawaban_siswa,
                            'kunci_jawaban' => $kunci,
                            'is_correct' => $is_correct
                        ];
                    }
                    // Nilai akhir (Persentase 0-100) untuk ditampilkan di laporan Benar/Salah
                    $nilai_akhir = ($skor / $total_soal) * 100;
                } else {
                    $nilai_akhir = 0;
                }

                // Simpan data kuis ke session untuk halaman hasil kuis
                $_SESSION['quiz_result'] = [
                    'skor' => $nilai_akhir,
                    'total_soal' => $total_soal,
                    'benar' => $skor,
                    'detail' => $detail_jawaban
                ];

                $username = $_SESSION['username'] ?? '';
                if ($username) {
                    // Cek apakah user sudah pernah menyelesaikan kuis ini agar poin tidak ganda jika diulang (Anti-Curang)
                    $stmt_check = $this->db->prepare("SELECT id FROM completed_classes WHERE username = ? AND class_id = ?");
                    $stmt_check->execute([$username, $class_id]);
                    
                    if (!$stmt_check->fetch()) {
                        // 1. Hitung poin aslimu: 1 Soal Benar = 100 Poin
                        $points_earned = $skor * 100;

                        // 2. Masukkan ke riwayat completed_classes dengan poin yang didapat
                        $stmt_insert = $this->db->prepare("INSERT INTO completed_classes (username, class_id, points_earned) VALUES (?, ?, ?)");
                        $stmt_insert->execute([$username, $class_id, $points_earned]);

                        // 3. Tambahkan total poin ke tabel 'users' pada kolom 'points' agar muncul di halaman Streak!
                        $stmt_update_user = $this->db->prepare("UPDATE users SET points = points + ? WHERE username = ?");
                        $stmt_update_user->execute([$points_earned, $username]);
                    }
                }

                // Alihkan ke halaman skor hasil kuis
                header("Location: /quiz_result");
                exit();

            } catch (PDOException $e) {
                die("Error Database saat submit kuis: " . $e->getMessage());
            }
        }
    }

    // 3. Menampilkan Halaman Hasil (GET /quiz_result)
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