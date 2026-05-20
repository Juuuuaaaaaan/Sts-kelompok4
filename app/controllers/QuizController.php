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
                    // Hitung akumulasi poin dari pengerjaan saat ini (1 Soal Benar = 100 Poin)
                    $points_earned = $skor * 100;

                    // Ambil data riwayat kuis sebelumnya jika user sudah pernah mengerjakan kelas ini
                    $stmt_check = $this->db->prepare("SELECT id, points_earned FROM completed_classes WHERE username = ? AND class_id = ?");
                    $stmt_check->execute([$username, $class_id]);
                    $existing_record = $stmt_check->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$existing_record) {
                        // KONDISI 1: Jika baru PERTAMA KALI mengerjakan kuis ini
                        $stmt_insert = $this->db->prepare("INSERT INTO completed_classes (username, class_id, points_earned) VALUES (?, ?, ?)");
                        $stmt_insert->execute([$username, $class_id, $points_earned]);

                        // Tambahkan seluruh poin baru ke kolom points di tabel users
                        $stmt_update_user = $this->db->prepare("UPDATE users SET points = points + ? WHERE username = ?");
                        $stmt_update_user->execute([$points_earned, $username]);
                    } else {
                        // KONDISI 2: Jika kuis diulang, cek apakah skor barunya lebih tinggi dari skor lama
                        $old_points = $existing_record['points_earned'];
                        if ($points_earned > $old_points) {
                            $selisih_poin = $points_earned - $old_points;

                            // Perbarui poin tertinggi di tabel riwayat completed_classes
                            $stmt_update_history = $this->db->prepare("UPDATE completed_classes SET points_earned = ? WHERE id = ?");
                            $stmt_update_history->execute([$points_earned, $existing_record['id']]);

                            // Tambahkan selisih poinnya ke tabel users agar poin/streak naik
                            $stmt_update_user = $this->db->prepare("UPDATE users SET points = points + ? WHERE username = ?");
                            $stmt_update_user->execute([$selisih_poin, $username]);
                        }
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