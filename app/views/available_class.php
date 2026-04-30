<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover Classes - Fun Streak</title>
    <link rel="stylesheet" href="../../public/css/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body class="bg-[#f9f9f9] min-h-screen font-['Outfit']">

    <nav class="flex justify-between items-center p-6 bg-white shadow-sm sticky top-0 z-50">
        <a href="../../index.php" class="text-3xl font-bold text-[#b829e3]">Fun Streak</a>
        <div class="flex gap-10 text-gray-500 font-semibold items-center text-lg">
            <a href="../../index.php">Home</a>
            <a href="class.php" class="text-[#b829e3] border-b-2 border-[#b829e3] pb-1">Class</a>
            <a href="streak.php">Streak</a>
        </div>
        <div class="flex gap-5 items-center">
            <div class="h-10 w-10 rounded-full bg-purple-200 flex items-center justify-center text-[#b829e3] font-bold">
                <?= substr($_SESSION['username'], 0, 1) ?>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-10">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-800">Available Classes</h1>
                <p class="text-gray-500 mt-2">Temukan tantangan baru untuk menjaga streak kamu!</p>
            </div>
            <a href="class.php" class="text-[#b829e3] font-bold hover:underline">← Back to My Classes</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div class="h-40 bg-purple-100 rounded-2xl mb-5 flex items-center justify-center text-5xl">🎨</div>
                <h3 class="text-xl font-bold text-gray-800">UI/UX Design Basic</h3>
                <p class="text-gray-500 text-sm mt-2 mb-6">Pelajari cara membuat tampilan aplikasi yang cantik dan fungsional.</p>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-purple-400 uppercase">12 Lessons</span>
                    <button class="bg-[#b829e3] text-white px-6 py-2 rounded-full font-bold text-sm hover:bg-[#a01ebd]">Join Class</button>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div class="h-40 bg-blue-100 rounded-2xl mb-5 flex items-center justify-center text-5xl">💻</div>
                <h3 class="text-xl font-bold text-gray-800">PHP for Beginners</h3>
                <p class="text-gray-500 text-sm mt-2 mb-6">Kuasai logika backend dan database menggunakan PHP & MySQL.</p>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-blue-400 uppercase">15 Lessons</span>
                    <button class="bg-[#b829e3] text-white px-6 py-2 rounded-full font-bold text-sm hover:bg-[#a01ebd]">Join Class</button>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div class="h-40 bg-green-100 rounded-2xl mb-5 flex items-center justify-center text-5xl">📊</div>
                <h3 class="text-xl font-bold text-gray-800">Data Science 101</h3>
                <p class="text-gray-500 text-sm mt-2 mb-6">Mulai perjalananmu menjadi Data Analyst profesional di sini.</p>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-green-400 uppercase">8 Lessons</span>
                    <button class="bg-[#b829e3] text-white px-6 py-2 rounded-full font-bold text-sm hover:bg-[#a01ebd]">Join Class</button>
                </div>
            </div>

        </div>
    </main>
</body>
</html>