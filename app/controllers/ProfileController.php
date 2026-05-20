<?php
namespace App\Controllers;
 
class ProfileController {
   
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
 
        if (!isset($_SESSION['username'])) {
            header("Location: /");
            exit;
        }
 
       
        require dirname(__DIR__) . '/core/Database.php';
 
       
        $stmt = $pdo->prepare("SELECT avatar, theme_color, bio FROM users WHERE username = ?");
        $stmt->execute([$_SESSION['username']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
 
       
        $current_avatar = $user['avatar'] ?? '🧑‍💻';
        $current_theme = $user['theme_color'] ?? 'bg-[#b829e3]';
        $current_bio = $user['bio'] ?? '';
 
       
        $_SESSION['avatar'] = $current_avatar;
        $_SESSION['theme_color'] = $current_theme;
 
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
 
       
        require_once __DIR__ . '/../views/profile.php';
    }
 
    public function update() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
       
        if (!isset($_SESSION['username'])) {
            header("Location: /login");
            exit();
        }
 
       
        require dirname(__DIR__) . '/core/Database.php';
       
        $action = $_POST['action'] ?? '';
        $username = $_SESSION['username'];
 
       
        if ($action === 'update_username') {
            $new_username = trim($_POST['new_username'] ?? '');
 
            if (empty($new_username)) {
                header("Location: /profile?error=Username tidak boleh kosong!");
                exit();
            }
 
           
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE username = ?");
            $stmt->execute([$new_username, $username]);
 
           
            $_SESSION['username'] = $new_username;
 
            header("Location: /profile?success=Username berhasil diperbarui!");
            exit();
        }
       
       
        elseif ($action === 'update_bio') {
            $bio = $_POST['bio'] ?? '';
            $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE username = ?");
            $stmt->execute([$bio, $username]);
            header("Location: /profile?success=Bio berhasil diperbarui!");
            exit();
        }
       
       
        elseif ($action === 'update_character') {
            $avatar = $_POST['avatar'] ?? '🧑‍💻';
            $theme = $_POST['theme_color'] ?? 'bg-[#b829e3]';
            $stmt = $pdo->prepare("UPDATE users SET avatar = ?, theme_color = ? WHERE username = ?");
            $stmt->execute([$avatar, $theme, $username]);
           
           
           
            $_SESSION['avatar'] = $avatar;
            $_SESSION['theme_color'] = $theme;
           
            header("Location: /profile?success=Karakter berhasil diperbarui!");
            exit();
        }
 
       
        header("Location: /profile");
        exit();
    }
}