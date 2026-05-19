<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['username'])) { header("Location: /login"); exit(); }

$current_avatar = $current_avatar ?? '🧑‍💻';
$current_theme = $current_theme ?? 'bg-[#b829e3]';
$current_bio = $current_bio ?? '';
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
        
        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .avatar-box { border-color: #b829e3 !important; background-color: #f3d9fa !important; transform: scale(1.05); }
        input[type="radio"]:checked + .color-box { transform: scale(1.25); box-shadow: 0 0 0 3px #f3d9fa, 0 0 0 5px #b829e3; }
    </style>
</head>

<body class="bg-[#f9f9f9] min-h-screen flex flex-col font-['Outfit'] relative overflow-x-hidden">

    <nav class="flex items-center w-full px-8 py-5 bg-white/95 backdrop-blur-sm shadow-sm z-30 sticky top-0">
        <div class="flex-1 flex justify-start">
            <a href="/" class="text-3xl font-bold text-[#b829e3] tracking-wide cursor-pointer hover:scale-105 transition-transform duration-300">Fun Streak</a>
        </div>
        <div class="flex justify-center gap-10 text-gray-500 font-semibold items-center text-lg hidden md:flex">
            <a href="/" class="relative group hover:text-[#b829e3] transition-colors duration-300">Home</a>
            <a href="/class" class="relative group hover:text-[#b829e3] transition-colors duration-300">Class</a>
            <a href="/streak" class="relative group hover:text-[#b829e3] transition-colors duration-300">Streak</a>
        </div>
        <div class="flex-1 flex justify-end items-center gap-4">
            <a href="/profile" class="h-11 w-11 rounded-full <?= htmlspecialchars($_SESSION['theme_color'] ?? 'bg-[#b829e3]') ?> hover:scale-105 flex items-center justify-center text-xl shadow-sm transition-all duration-300 cursor-pointer border-2 border-white overflow-hidden" title="Go to Profile">
                <?= htmlspecialchars($_SESSION['avatar'] ?? '🧑‍💻') ?>
            </a>
        </div>
    </nav>

    <div class="container mx-auto max-w-5xl px-4 py-6 flex-grow flex flex-col gap-4 relative z-10">
        
        <?php if(isset($_GET['success'])): ?>
            <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-2.5 rounded-lg text-center text-sm font-medium shadow-sm animate-pulse">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="w-full bg-red-100 border border-red-400 text-red-700 px-4 py-2.5 rounded-lg text-center text-sm font-medium shadow-sm animate-pulse">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <main class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
            
            <div class="md:col-span-1 flex flex-col gap-5">
                
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col items-center text-center transition-colors duration-300">
                    <div id="preview-avatar" class="h-24 w-24 rounded-full <?= htmlspecialchars($current_theme) ?> flex items-center justify-center text-5xl shadow-inner mb-3 overflow-hidden border-[3px] border-white ring-[3px] ring-gray-100 transition-colors duration-300">
                        <?= htmlspecialchars($current_avatar) ?>
                    </div>
                    
                    <div class="w-full flex items-center justify-center mb-1">
                        <div id="display-name-area" class="flex items-center justify-center gap-2 h-12 w-full">
                            <h2 class="text-2xl font-extrabold text-gray-800"><?= htmlspecialchars($_SESSION['username']) ?></h2>
                            <button onclick="toggleNameEdit()" class="text-gray-300 hover:text-[#b829e3] transition-colors focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                            </button>
                        </div>

                        <form id="edit-name-form" action="/profile/update" method="POST" class="hidden items-center justify-center gap-1.5 h-12 w-full">
                            <input type="hidden" name="action" value="update_username">
                            <input type="text" name="new_username" value="<?= htmlspecialchars($_SESSION['username']) ?>" class="w-[55%] text-center px-2 py-1 text-sm border-2 border-[#b829e3] rounded-lg focus:outline-none text-gray-800 font-bold" required>
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-2.5 py-1.5 rounded-lg text-xs font-bold shadow-sm whitespace-nowrap">Simpan</button>
                            <button type="button" onclick="toggleNameEdit()" class="bg-gray-200 hover:bg-gray-300 text-gray-600 px-2.5 py-1.5 rounded-lg text-xs font-bold shadow-sm whitespace-nowrap">Batal</button>
                        </form>
                    </div>

                    <p class="text-[#b829e3] text-sm font-semibold mb-4">Fun Streak Member</p>
                    
                    <a href="/logout" class="w-full flex items-center justify-center gap-2 bg-red-500 border-2 border-red-500 text-white py-2 rounded-lg text-sm font-bold hover:bg-red-600 hover:border-red-600 hover:-translate-y-0.5 transition-all duration-300">
                        Log Out
                    </a>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span>📝</span> Tentang Saya
                    </h3>
                    <form action="/profile/update" method="POST">
                        <input type="hidden" name="action" value="update_bio">
                        <textarea name="bio" class="w-full h-24 p-3 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b829e3] resize-none text-gray-600" placeholder="Tuliskan sesuatu tentang dirimu..."><?= htmlspecialchars($current_bio) ?></textarea>
                        <button type="submit" class="mt-3 bg-[#b829e3] hover:bg-[#9b1ebf] text-white px-5 py-2 rounded-md font-bold shadow transition-all duration-300 text-xs w-full sm:w-auto">Simpan Bio</button>
                    </form>
                </div>
            </div>

            <div class="md:col-span-2 flex flex-col gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 h-full">
                    <h2 class="text-2xl font-extrabold text-[#b829e3] mb-1">Desain Karaktermu ✨</h2>
                    <p class="text-gray-500 text-sm mb-6 font-medium">Pilih avatar dan warna dasar untuk mewakili dirimu di Fun Streak!</p>

                    <form action="/profile/update" method="POST">
                        <input type="hidden" name="action" value="update_character">
                        
                        <div class="mb-6">
                            <h4 class="text-base font-bold text-gray-700 mb-3">1. Pilih Gaya Avatar</h4>
                            <div class="grid grid-cols-4 sm:grid-cols-5 gap-3">
                                <?php 
                                $avatars = ['🧑‍💻', '👩‍💻', '🦸‍♂️', '🦸‍♀️', '🧙‍♂️', '🥷', '🧑‍🚀', '🕵️', '🧑‍🎓', '🤖'];
                                foreach ($avatars as $icon): 
                                    $isChecked = ($current_avatar === $icon) ? 'checked' : '';
                                ?>
                                    <label class="cursor-pointer block">
                                        <input type="radio" name="avatar" value="<?= $icon ?>" <?= $isChecked ?> class="avatar-radio">
                                        <div class="avatar-box h-16 bg-gray-50 border-2 border-gray-200 rounded-xl text-3xl flex justify-center items-center transition-all duration-200 hover:border-[#b829e3] hover:bg-purple-50">
                                            <?= $icon ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-base font-bold text-gray-700 mb-3">2. Pilih Warna Tema Karakter</h4>
                            <div class="flex gap-4 flex-wrap py-1">
                                <?php 
                                $colors = ['bg-[#b829e3]', 'bg-blue-400', 'bg-green-400', 'bg-yellow-400', 'bg-red-400', 'bg-pink-400', 'bg-gray-800'];
                                foreach ($colors as $color): 
                                    $isChecked = ($current_theme === $color) ? 'checked' : '';
                                ?>
                                    <label class="cursor-pointer block">
                                        <input type="radio" name="theme_color" value="<?= $color ?>" <?= $isChecked ?> class="theme-radio">
                                        <div class="color-box w-8 h-8 <?= $color ?> rounded-full border-2 border-white shadow-sm transition-all duration-200 hover:scale-110"></div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end">
                            <button type="submit" class="bg-[#b829e3] hover:bg-[#9b1ebf] text-white px-6 py-2.5 rounded-full font-bold shadow-sm hover:-translate-y-0.5 hover:shadow transition-all duration-300 text-sm">
                                Simpan Karakter
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>

    <script>
        
        function toggleNameEdit() {
            const displayArea = document.getElementById('display-name-area');
            const formArea = document.getElementById('edit-name-form');
            
            if(displayArea.classList.contains('hidden')) {
                displayArea.classList.remove('hidden');
                displayArea.classList.add('flex');
                formArea.classList.add('hidden');
                formArea.classList.remove('flex');
            } else {
                displayArea.classList.add('hidden');
                displayArea.classList.remove('flex');
                formArea.classList.remove('hidden');
                formArea.classList.add('flex');
            }
        }

        
        document.addEventListener('DOMContentLoaded', () => {
            const previewAvatarBox = document.getElementById('preview-avatar');
            const avatarRadios = document.querySelectorAll('.avatar-radio');
            const themeRadios = document.querySelectorAll('.theme-radio');
            const allColors = ['bg-[#b829e3]', 'bg-blue-400', 'bg-green-400', 'bg-yellow-400', 'bg-red-400', 'bg-pink-400', 'bg-gray-800'];

            avatarRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if(this.checked) previewAvatarBox.innerText = this.value; 
                });
            });

            themeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if(this.checked) {
                        previewAvatarBox.classList.remove(...allColors);
                        previewAvatarBox.classList.add(this.value);
                    }
                });
            });

            const links = document.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href') && this.hostname === window.location.hostname && this.target !== '_blank' && !this.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        const destination = this.href;
                        document.body.classList.add('page-exit');
                        setTimeout(() => window.location.href = destination, 150); 
                    }
                });
            });
        });
    </script>
</body>
</html>