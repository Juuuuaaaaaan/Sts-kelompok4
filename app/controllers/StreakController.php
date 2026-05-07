<?php
namespace App\Controllers;
use PDO;
use PDOException;

class StreakController {
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
        $total_points = 0;

        try {
            $stmt = $this->db->prepare("SELECT points FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $total_points = $user['points'];
            }
        } catch(PDOException $e) {
            die("Error mengambil data poin: " . $e->getMessage());
        }

        $level = floor($total_points / 100) + 1;
        $next_level_points = $level * 100;
        $progress_percent = ($total_points % 100); 
        if ($progress_percent == 0 && $total_points > 0) {
            $progress_percent = 100;
        }

        require_once __DIR__ . '/../views/streak.php';
    }
}