<?php
// app/db.php
$host = 'localhost';
$dbname = 'fun_streak_db';
$username = 'root'; // Sesuaikan dengan server kamu
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>