<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

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
    <title>Profile - Fun Streak</title>
    
    <link rel="stylesheet" href="../../public/css/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body { animation: fadeInPage 0.15s ease-out forwards; }
        body.page-exit { animation: fadeOutPage 0.15s ease-in forwards; }
        @keyframes fadeInPage { 0% { opacity: 0; } 100% { opacity: 1; } }
        @keyframes fadeOutPage { 0% { opacity: 1; } 100% { opacity: 0; } }
        
        .char-option:focus, .char-option.active {
            border-color: #b829e3;
            background-color: #f3d9fa;
            transform: scale(1.05);
        }
    </style>
</head>

<body class="bg-[#f9f9f9] min-h-screen flex flex-col font-['Outfit'] relative overflow-x-hidden">

    <nav class="flex items-center w-full p-6 bg-white/95 backdrop-blur-sm shadow-sm z-30 sticky top-0">
        
        <div class="flex-1 flex justify-start">
            <a href="/" class="text-3xl font-bold text-[#b829e3] tracking-wide cursor-pointer hover:scale-105 transition-transform duration-300">Fun Streak</a>
        </div>
        
        <div class="flex justify-center gap-10 text-gray-500 font-semibold items-center text-lg hidden md:flex">
            <a href="/" class="relative group <?= $current_page == 'index.php' ? 'text-[#b829e3]' : 'hover:text-[#b829e3]' ?> transition-colors duration-300">
                Home
                <span class="absolute -bottom-1 left-0 h-[3px] rounded-full bg-[#b829e3] transition-all duration-300 <?= $current_page == 'index.php' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>
            <a href="/class" class="relative group <?= $current_page == 'class.php' ? 'text-[#b829e3]' : 'hover:text-[#b829e3]' ?> transition-colors duration-300">
                Class
                <span class="absolute -bottom-1 left-0 h-[3px] rounded-full bg-[#b829e3] transition-all duration-300 <?= $current_page == 'class.php' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>
            <a href="/streak" class="relative group <?= $current_page == 'streak.php' ? 'text-[#b829e3]' : 'hover:text-[#b829e3]' ?> transition-colors duration-300">
                Streak
                <span class="absolute -bottom-1 left-0 h-[3px] rounded-full bg-[#b829e3] transition-all duration-300 <?= $current_page == 'streak.php' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>
        </div>

        <div class="flex-1 flex justify-end items-center gap-4">
            <?php if (isset($_SESSION['username'])): ?>
                <a href="/profile" class="h-10 w-10 rounded-full bg-purple-200 hover:bg-purple-300 hover:scale-105 flex items-center justify-center text-[#b829e3] font-bold text-xl uppercase shadow-sm transition-all duration-300 cursor-pointer" title="Go to Profile">
                    <?= substr($_SESSION['username'], 0, 1) ?>
                </a>
            <?php else: ?>
                <a href="/login" class="text-[#b829e3] font-bold hover:text-[#9b1ebf] px-4 py-2 transition-colors duration-300">Log In</a>
                <a href="/register" class="bg-[#b829e3] hover:bg-[#9b1ebf] text-white px-8 py-2.5 rounded-full font-bold shadow-md hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300">Sign Up</a>
            <?php endif; ?>
        </div>
        
    </nav>

    <main class="container mx-auto max-w-6xl px-6 py-10 flex-grow grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
        
        <div class="md:col-span-1 flex flex-col gap-6">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col items-center text-center">
                <div class="h-32 w-32 rounded-full bg-purple-100 border-4 border-[#b829e3] flex items-center justify-center text-6xl shadow-inner mb-4 overflow-hidden">
                    🧑‍💻
                </div>
                <h2 class="text-3xl font-extrabold text-gray-800"><?= htmlspecialchars($_SESSION['username']) ?></h2>
                <p class="text-[#b829e3] font-semibold mb-6">Fun Streak Member</p>
                
                <a href="/logout" class="w-full flex items-center justify-center gap-2 border-2 border-red-400 text-red-500 py-3 rounded-xl font-bold hover:bg-red-50 hover:-translate-y-0.5 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" /></svg>
                    Log Out
                </a>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span>📝</span> Tentang Saya
                </h3>
                <textarea class="w-full h-32 p-4 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#b829e3] resize-none text-gray-600" placeholder="Tuliskan sesuatu tentang dirimu, tujuan belajarmu, atau hobi..."></textarea>
                <button class="mt-4 bg-[#b829e3] hover:bg-[#9b1ebf] text-white px-6 py-2 rounded-lg font-bold shadow transition-all duration-300 text-sm">Simpan Bio</button>
            </div>
        </div>

        <div class="md:col-span-2 flex flex-col gap-6">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 h-full">
                <h2 class="text-3xl font-extrabold text-[#b829e3] mb-2">Desain Karaktermu ✨</h2>
                <p class="text-gray-500 mb-8 font-medium">Pilih avatar dan warna dasar untuk mewakili dirimu di Fun Streak!</p>

                <div class="mb-8">
                    <h4 class="text-lg font-bold text-gray-700 mb-4">1. Pilih Gaya Avatar</h4>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-4">
                        <?php 
                        $avatars = ['🧑‍💻', '👩‍💻', '🦸‍♂️', '🦸‍♀️', '🧙‍♂️', '🥷', '🧑‍🚀', '🕵️', '🧑‍🎓', '🤖'];
                        foreach ($avatars as $index => $icon): 
                        ?>
                            <button tabindex="0" class="char-option h-20 bg-gray-50 border-2 border-gray-200 rounded-2xl text-4xl flex justify-center items-center cursor-pointer transition-all duration-200 hover:border-[#b829e3] hover:bg-purple-50">
                                <?= $icon ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-8">
                    <h4 class="text-lg font-bold text-gray-700 mb-4">2. Pilih Warna Tema Karakter</h4>
                    <div class="flex gap-4 flex-wrap">
                        <?php 
                        $colors = ['bg-[#b829e3]', 'bg-blue-400', 'bg-green-400', 'bg-yellow-400', 'bg-red-400', 'bg-pink-400', 'bg-gray-800'];
                        foreach ($colors as $color): 
                        ?>
                            <button tabindex="0" class="char-option w-12 h-12 <?= $color ?> rounded-full border-4 border-white shadow-md cursor-pointer transition-transform hover:scale-110 focus:ring-4 focus:ring-purple-300 focus:outline-none"></button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <button class="bg-[#b829e3] hover:bg-[#9b1ebf] text-white px-8 py-3 rounded-full font-bold shadow-md hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300 text-lg">
                        Simpan Karakter
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const links = document.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.hostname === window.location.hostname && this.target !== '_blank' && !this.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        const destination = this.href;
                        document.body.classList.add('page-exit');
                        setTimeout(() => {
                            window.location.href = destination;
                        }, 150); 
                    }
                });
            });
        });

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                document.body.classList.remove('page-exit');
            }
        });
    </script>
</body>
</html>