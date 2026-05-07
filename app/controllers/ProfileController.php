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

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");

        require_once __DIR__ . '/../views/profile.php';
    }
}