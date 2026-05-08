<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

if (!isset($_SESSION['username'])) {
    header("Location: /login");
    exit();
}

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

        input[type="radio"]:checked + .avatar-box {
            border-color: #b829e3 !important;
            background-color: #f3d9fa !important;
            transform: scale(1.05);
        }

        input[type="radio"]:checked + .color-box {
            transform: scale(1.25);
            box-shadow: 0 0 0 4px #f3d9fa, 0 0 0 6px #b829e3;
        }
    </style>
</head>

<body class="bg-[#f9f9f9] min-h-screen flex flex-col font-['Outfit'] relative overflow-x-hidden">

    <nav class="flex items-center w-full p-6 bg-white/95 backdrop-blur-sm shadow-sm z-30 sticky top-0">
        <div class="flex-1 flex justify-start">
            <a href="/" class="text-3xl font-bold text-[#b829e3] tracking-wide cursor-pointer hover:scale-105 transition-transform duration-300">Fun Streak</a>
        </div>
        
        <div class="flex justify-center gap-10 text-gray-500 font-semibold items-center text-lg hidden md:flex">
            <a href="/" class="relative group hover:text-[#b829e3] transition-colors duration-300">Home</a>
            <a href="/class" class="relative group hover:text-[#b829e3] transition-colors duration-300">Class</a>
            <a href="/streak" class="relative group hover:text-[#b829e3] transition-colors duration-300">Streak</a>
        </div>

        <div class="flex-1 flex justify-end items-center gap-4">
            <a href="/profile" class="h-10 w-10 rounded-full bg-purple-200 hover:bg-purple-300 hover:scale-105 flex items-center justify-center text-[#b829e3] font-bold text-xl uppercase shadow-sm transition-all duration-300 cursor-pointer">
                <?= substr($_SESSION['username'], 0, 1) ?>
            </a>
        </div>
    </nav>

    <div class="container mx-auto max-w-6xl px-6 py-10 flex-grow flex flex-col gap-6 relative z-10">
        
        <?php if(isset($_GET['success'])): ?>
            <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl text-center font-medium shadow-sm animate-pulse">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <main class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full">
            
            <div class="md:col-span-1 flex flex-col gap-6">
                
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col items-center text-center transition-colors duration-300">
                    <div id="preview-avatar" class="h-32 w-32 rounded-full <?= htmlspecialchars($current_theme) ?> flex items-center justify-center text-6xl shadow-inner mb-4 overflow-hidden border-4 border-white ring-4 ring-gray-100 transition-colors duration-300">
                        <?= htmlspecialchars($current_avatar) ?>
                    </div>
                    <h2 class="text-3xl font-extrabold text-gray-800"><?= htmlspecialchars($_SESSION['username']) ?></h2>
                    <p class="text-[#b829e3] font-semibold mb-6">Fun Streak Member</p>
                    
                    <a href="/logout" class="w-full flex items-center justify-center gap-2 border-2 border-red-400 text-red-500 py-3 rounded-xl font-bold hover:bg-red-50 hover:-translate-y-0.5 transition-all duration-300">
                        Log Out
                    </a>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span>📝</span> Tentang Saya
                    </h3>
                    <form action="/profile/update" method="POST">
                        <input type="hidden" name="action" value="update_bio">
                        <textarea name="bio" class="w-full h-32 p-4 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#b829e3] resize-none text-gray-600" placeholder="Tuliskan sesuatu tentang dirimu..."><?= htmlspecialchars($current_bio) ?></textarea>
                        <button type="submit" class="mt-4 bg-[#b829e3] hover:bg-[#9b1ebf] text-white px-6 py-2 rounded-lg font-bold shadow transition-all duration-300 text-sm">Simpan Bio</button>
                    </form>
                </div>
            </div>

            <div class="md:col-span-2 flex flex-col gap-6">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 h-full">
                    <h2 class="text-3xl font-extrabold text-[#b829e3] mb-2">Desain Karaktermu ✨</h2>
                    <p class="text-gray-500 mb-8 font-medium">Pilih avatar dan warna dasar untuk mewakili dirimu di Fun Streak!</p>

                    <form action="/profile/update" method="POST">
                        <input type="hidden" name="action" value="update_character">
                        
                        <div class="mb-8">
                            <h4 class="text-lg font-bold text-gray-700 mb-4">1. Pilih Gaya Avatar</h4>
                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-4">
                                <?php 
                                $avatars = ['🧑‍💻', '👩‍💻', '🦸‍♂️', '🦸‍♀️', '🧙‍♂️', '🥷', '🧑‍🚀', '🕵️', '🧑‍🎓', '🤖'];
                                foreach ($avatars as $icon): 
                                    $isChecked = ($current_avatar === $icon) ? 'checked' : '';
                                ?>
                                    <label class="cursor-pointer block">
                                        <input type="radio" name="avatar" value="<?= $icon ?>" <?= $isChecked ?> class="avatar-radio">
                                        <div class="avatar-box h-20 bg-gray-50 border-2 border-gray-200 rounded-2xl text-4xl flex justify-center items-center transition-all duration-200 hover:border-[#b829e3] hover:bg-purple-50">
                                            <?= $icon ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h4 class="text-lg font-bold text-gray-700 mb-4">2. Pilih Warna Tema Karakter</h4>
                            <div class="flex gap-6 flex-wrap py-2">
                                <?php 
                                $colors = ['bg-[#b829e3]', 'bg-blue-400', 'bg-green-400', 'bg-yellow-400', 'bg-red-400', 'bg-pink-400', 'bg-gray-800'];
                                foreach ($colors as $color): 
                                    $isChecked = ($current_theme === $color) ? 'checked' : '';
                                ?>
                                    <label class="cursor-pointer block">
                                        <input type="radio" name="theme_color" value="<?= $color ?>" <?= $isChecked ?> class="theme-radio">
                                        <div class="color-box w-10 h-10 <?= $color ?> rounded-full border-2 border-white shadow-md transition-all duration-200 hover:scale-110"></div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-100 flex justify-end">
                            <button type="submit" class="bg-[#b829e3] hover:bg-[#9b1ebf] text-white px-8 py-3 rounded-full font-bold shadow-md hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300 text-lg">
                                Simpan Karakter
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>

    <script>
        // --- SCRIPT UNTUK LIVE PREVIEW ---
        document.addEventListener('DOMContentLoaded', () => {
            const previewAvatarBox = document.getElementById('preview-avatar');
            const avatarRadios = document.querySelectorAll('.avatar-radio');
            const themeRadios = document.querySelectorAll('.theme-radio');

            // Daftar semua warna yang ada untuk dihapus sebelum menambah yang baru
            const allColors = ['bg-[#b829e3]', 'bg-blue-400', 'bg-green-400', 'bg-yellow-400', 'bg-red-400', 'bg-pink-400', 'bg-gray-800'];

            // Event Listener untuk Avatar
            avatarRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if(this.checked) {
                        previewAvatarBox.innerText = this.value; // Ubah icon
                    }
                });
            });

            // Event Listener untuk Warna
            themeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if(this.checked) {
                        // Hapus semua class warna lama
                        previewAvatarBox.classList.remove(...allColors);
                        // Tambah class warna baru
                        previewAvatarBox.classList.add(this.value);
                    }
                });
            });

            // Script untuk transisi perpindahan halaman
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
    </script>
</body>
</html>