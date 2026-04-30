<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fun Streak</title>
    
    <link rel="stylesheet" href="public/css/output.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body class="bg-[#f9f9f9] min-h-screen flex flex-col font-['Outfit']">

    <nav class="flex justify-between items-center p-6 bg-white shadow-sm z-10 sticky top-0 w-full">
        <a href="index.php" class="text-3xl font-bold text-[#b829e3] tracking-wide cursor-pointer hover:scale-105 transition-transform duration-300">Fun Streak</a>
        
        <div class="flex gap-10 text-gray-500 font-semibold items-center text-lg">
            <a href="index.php" class="text-[#b829e3] border-b-2 border-[#b829e3] pb-1">Home</a>
            <a href="app/views/class.php" class="hover:text-[#b829e3] transition-colors duration-300">Class</a>
            <a href="app/views/streak.php" class="hover:text-[#b829e3] transition-colors duration-300">Streak</a>
        </div>
        
        <div class="flex gap-5 items-center">
            <?php if(isset($_SESSION['username'])): ?>
                <div class="h-10 w-10 rounded-full bg-purple-200 flex items-center justify-center text-[#b829e3] font-bold text-xl uppercase shadow-sm">
                    <?= substr($_SESSION['username'], 0, 1) ?>
                </div>
                <a href="app/controllers/logout.php" class="border-2 border-red-400 text-red-500 px-6 py-1.5 rounded-full font-semibold shadow-sm hover:bg-red-50 transition-all duration-300">Log Out</a>
            
            <?php else: ?>
                <a href="app/views/register.php" class="bg-[#b829e3] text-white px-6 py-2 rounded-full font-semibold shadow-md hover:bg-[#a01ebd] hover:-translate-y-0.5 transition-all duration-300">Register</a>
                <a href="app/views/login.php" class="border-2 border-[#b829e3] text-[#b829e3] px-6 py-1.5 rounded-full font-semibold hover:bg-purple-50 transition-all duration-300">Log In</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="flex-grow flex flex-col items-center justify-center relative bg-white">
        <div class="text-center z-10 mb-32 hover:scale-105 transition-transform duration-500 cursor-default">
            <h1 class="text-[7rem] font-extrabold text-[#b829e3] tracking-tight leading-none mb-2">Fun Streak</h1>
            <p class="text-[2.5rem] text-[#c765e8] font-medium tracking-wide">hard work by yourself</p>
        </div>
        
        <div class="absolute bottom-0 w-full h-[40%] bg-[#b829e3] rounded-t-[100%] flex justify-center shadow-[0_-15px_30px_rgba(184,41,227,0.15)] transition-all duration-500">
            <a href="app/views/class.php" class="absolute -top-7 bg-white text-[#b829e3] text-2xl font-bold px-14 py-4 rounded-full shadow-[0_10px_25px_rgba(184,41,227,0.4)] hover:scale-110 hover:shadow-[0_15px_35px_rgba(184,41,227,0.6)] hover:bg-gray-50 transition-all duration-300 ease-in-out">
                Start Now
            </a>
        </div>
    </main>
</body>
</html>