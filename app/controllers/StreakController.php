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

        $points_per_level = 1000; 

        $level = floor($total_points / $points_per_level) + 1;
        $next_level_points = $level * $points_per_level;

        $current_level_progress = ($total_points % $points_per_level);
        $progress_percent = ($current_level_progress / $points_per_level) * 100;

        require_once __DIR__ . '/../views/streak.php';
    }
}