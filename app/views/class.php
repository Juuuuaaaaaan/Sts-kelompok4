<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memanggil file db.php yang ada di folder app
require_once '../db.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Ambil data kelas menggunakan PDO (Hanya yang belum dikerjakan)
try {
    $query = "SELECT * FROM classes WHERE id NOT IN (SELECT class_id FROM completed_classes WHERE username = :username)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);
    
    // Simpan semua hasil query ke dalam array
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes - Fun Streak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-[#f9f9f9] min-h-screen flex flex-col font-['Outfit']">

    <nav class="flex justify-between items-center p-6 bg-white shadow-sm z-10 sticky top-0">
        <a href="../../index.php" class="text-3xl font-bold text-[#b829e3] tracking-wide cursor-pointer hover:scale-105 transition-transform duration-300">Fun Streak</a>
        <div class="flex gap-10 text-gray-500 font-semibold items-center text-lg">
            <a href="../../index.php" class="hover:text-[#b829e3] transition-colors duration-300">Home</a>
            <a href="class.php" class="text-[#b829e3] border-b-2 border-[#b829e3] pb-1">Class</a>
            <a href="streak.php" class="hover:text-[#b829e3] transition-colors duration-300">Streak</a>
        </div>
        <div class="flex gap-5 items-center">
            <div class="h-10 w-10 rounded-full bg-purple-200 flex items-center justify-center text-[#b829e3] font-bold text-xl uppercase">
                <?= substr($_SESSION['username'], 0, 1) ?>
            </div>
            <a href="../controllers/logout.php" class="border-2 border-red-400 text-red-500 px-6 py-1.5 rounded-full font-semibold shadow-sm hover:bg-red-50 transition-all duration-300">Log Out</a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-6xl flex-grow">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight mb-2">Your Classes</h1>
                <p class="text-gray-500 font-medium">Keep up the good work! Choose a class to start learning.</p>
            </div>
            
            <a href="available_class.php" class="bg-[#b829e3] text-white px-6 py-2.5 rounded-full font-semibold shadow-md hover:bg-[#a01ebd] hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Join New Class
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <?php if (count($classes) > 0) : ?>
                <?php foreach ($classes as $row) : ?>
                    <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_15px_40px_rgba(184,41,227,0.15)] hover:-translate-y-2 transition-all duration-300 border border-transparent hover:border-purple-100 group flex flex-col h-full">
                        
                        <div class="h-40 bg-purple-100 rounded-2xl mb-6 flex items-center justify-center group-hover:bg-purple-200 transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-[#b829e3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">
                            <?= htmlspecialchars($row['nama_kelas'] ?? 'Nama Kelas') ?>
                        </h2>
                        
                        <p class="text-gray-500 text-sm mb-6 line-clamp-2">
                            <?= htmlspecialchars($row['deskripsi'] ?? 'Deskripsi kelas belum tersedia.') ?>
                        </p>
                        
                        <div class="mt-auto flex justify-between items-center">
                            <span class="text-[#b829e3] font-bold bg-purple-50 px-4 py-1.5 rounded-full text-sm">Ready!</span>
                            
                            <a href="play_quiz.php?id=<?= $row['id'] ?>" class="text-white bg-gray-800 px-6 py-2 rounded-full font-semibold hover:bg-[#b829e3] transition-colors duration-300">Enter</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-span-full text-center py-16 bg-white rounded-3xl shadow-sm border border-gray-100">
                    <div class="text-6xl mb-4">🎉</div>
                    <h3 class="text-2xl text-gray-800 font-bold mb-2">Luar biasa!</h3>
                    <p class="text-gray-500 font-medium">Kamu sudah menyelesaikan semua kelas saat ini. Cek "Join New Class" untuk tantangan baru!</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>