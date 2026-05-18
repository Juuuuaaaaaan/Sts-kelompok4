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

        // 1. PANGGIL DATABASE DI DALAM FUNGSI SINI
        require dirname(__DIR__) . '/core/Database.php';

        // 2. Sekarang $pdo otomatis bisa langsung dipakai!
        $stmt = $pdo->prepare("SELECT avatar, theme_color, bio FROM users WHERE username = ?");
        $stmt->execute([$_SESSION['username']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Beri nilai default jika database masih kosong (user baru)
        $current_avatar = $user['avatar'] ?? '🧑‍💻';
        $current_theme = $user['theme_color'] ?? 'bg-[#b829e3]';
        $current_bio = $user['bio'] ?? '';

        // ⭐ INI JUGA PENTING ⭐
        // Perbarui sesi dengan data database terbaru setiap kali halaman profil dimuat.
        // Ini memastikan bilah navigasi selalu selaras dengan apa yang ada di database.
        $_SESSION['avatar'] = $current_avatar;
        $_SESSION['theme_color'] = $current_theme;

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");

        // Kirim data ke file view profile.php
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

        // 1. PANGGIL DATABASE DI DALAM FUNGSI SINI JUGA
        require dirname(__DIR__) . '/core/Database.php';
        
        $action = $_POST['action'] ?? '';
        $username = $_SESSION['username'];

        // Tangkap Form Username
        if ($action === 'update_username') {
            $new_username = trim($_POST['new_username'] ?? '');

            if (empty($new_username)) {
                header("Location: /profile?error=Username tidak boleh kosong!");
                exit();
            }

            // Jalankan query update ke database
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE username = ?");
            $stmt->execute([$new_username, $username]);

            // PENTING: Update juga nama di session agar tampilan navbar & profil langsung berubah
            $_SESSION['username'] = $new_username;

            header("Location: /profile?success=Username berhasil diperbarui!");
            exit();
        } 
        
        // Jika form Bio yang dikirim
        elseif ($action === 'update_bio') {
            $bio = $_POST['bio'] ?? '';
            $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE username = ?");
            $stmt->execute([$bio, $username]);
            header("Location: /profile?success=Bio berhasil diperbarui!");
            exit();
        } 
        
        // Jika form Karakter yang dikirim
        elseif ($action === 'update_character') {
            $avatar = $_POST['avatar'] ?? '🧑‍💻';
            $theme = $_POST['theme_color'] ?? 'bg-[#b829e3]';
            $stmt = $pdo->prepare("UPDATE users SET avatar = ?, theme_color = ? WHERE username = ?");
            $stmt->execute([$avatar, $theme, $username]);
            
            // ⭐ INI BAGIAN PALING PENTINGNYA ⭐
            // Menyimpan avatar dan tema baru ke session agar navbar di halaman lain ikut berubah!
            $_SESSION['avatar'] = $avatar;
            $_SESSION['theme_color'] = $theme;
            
            header("Location: /profile?success=Karakter berhasil diperbarui!");
            exit();
        }

        // JALUR AMAN: Jika ada request aneh yang lolos, paksa redirect ke profil biar tidak blank page
        header("Location: /profile");
        exit();
    }
}