<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php?error=Silakan login terlebih dahulu");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streak - Fun Streak</title>
    <link rel="stylesheet" href="../../public/css/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-[#f9f9f9] min-h-screen flex flex-col font-['Outfit']">

    <nav class="flex justify-between items-center p-6 bg-white shadow-sm z-10 sticky top-0 w-full">
        <a href="../../index.php" class="text-3xl font-bold text-[#b829e3] tracking-wide cursor-pointer hover:scale-105 transition-transform duration-300">Fun Streak</a>
        
        <div class="flex gap-10 text-gray-500 font-semibold items-center text-lg">
            <a href="../../index.php" class="hover:text-[#b829e3] transition-colors duration-300">Home</a>
            <a href="class.php" class="hover:text-[#b829e3] transition-colors duration-300">Class</a>
            <a href="streak.php" class="text-[#b829e3] border-b-2 border-[#b829e3] pb-1">Streak</a>
        </div>
        
        <div class="flex gap-5 items-center">
            <div class="h-10 w-10 rounded-full bg-purple-200 flex items-center justify-center text-[#b829e3] font-bold text-xl uppercase shadow-sm">
                <?= substr($_SESSION['username'], 0, 1) ?>
            </div>
            <a href="../controllers/logout.php" class="border-2 border-red-400 text-red-500 px-6 py-1.5 rounded-full font-semibold shadow-sm hover:bg-red-50 transition-all duration-300">Log Out</a>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-6 py-12 flex flex-col items-center">
        <h1 class="text-4xl font-extrabold text-gray-800 mb-2">Your Learning Streak</h1>
        <p class="text-gray-500 mb-10 text-lg">Keep up the momentum! Don't break the streak.</p>

        <div class="bg-white rounded-3xl shadow-sm p-12 flex flex-col items-center justify-center w-full max-w-3xl border border-gray-100">
            <div class="text-[120px] leading-none mb-4 animate-bounce">🔥</div>
            <h2 class="text-6xl font-black text-[#b829e3] mb-2 tracking-tight">3 Days</h2>
            <p class="text-xl text-gray-400 font-semibold mb-10 uppercase tracking-widest">Current Streak</p>
            
            <div class="flex gap-6 w-full justify-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-[#b829e3] text-white flex items-center justify-center font-bold text-xl shadow-md">M</div>
                    <span class="text-sm font-bold text-[#b829e3]">Done</span>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-[#b829e3] text-white flex items-center justify-center font-bold text-xl shadow-md">T</div>
                    <span class="text-sm font-bold text-[#b829e3]">Done</span>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-[#b829e3] text-white flex items-center justify-center font-bold text-xl shadow-md">W</div>
                    <span class="text-sm font-bold text-[#b829e3]">Done</span>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xl">T</div>
                    <span class="text-sm font-medium text-gray-400">To do</span>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xl">F</div>
                    <span class="text-sm font-medium text-gray-400">To do</span>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xl">S</div>
                    <span class="text-sm font-medium text-gray-400">To do</span>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xl">S</div>
                    <span class="text-sm font-medium text-gray-400">To do</span>
                </div>
            </div>
        </div>
    </main>

</body>
</html>