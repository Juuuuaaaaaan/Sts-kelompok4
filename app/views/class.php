<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class - Fun Streak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
    ::-webkit-scrollbar {
        display: none;
    }

    html, body {
        -ms-overflow-style: none; 
        scrollbar-width: none;  
    }

        body { font-family: 'Outfit', sans-serif; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .shake-animation { animation: shake 0.3s ease-in-out 2; }
    </style>
<style>
        body {
            animation: slideInPage 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        
        body.page-exit {
            animation: slideOutPage 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes slideInPage {
            0% { 
                opacity: 0; 
                transform: translateY(20px);
            }
            100% { 
                opacity: 1; 
                transform: translateY(0);
            }
        }

        @keyframes slideOutPage {
            0% { 
                opacity: 1; 
                transform: translateY(0); 
            }
            100% { 
                opacity: 0; 
                transform: translateY(-20px);
            }
        }
    </style>
</head>
<body class="bg-[#f3f4f6] min-h-screen flex flex-col relative">

<?php 
        
        $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); 
    ?>
    <nav class="flex items-center p-6 bg-white shadow-sm z-10 sticky top-0 w-full">
        
        <div class="flex-1">
            <a href="/" class="text-3xl font-bold text-[#b829e3] tracking-wide cursor-pointer hover:scale-105 transition-transform duration-300 inline-block">Fun Streak</a>
        </div>
        
        <div class="flex gap-10 text-gray-500 font-semibold items-center text-lg hidden md:flex">
            <a href="/" class="relative group <?= ($current_path == '/' || $current_path == '/index.php') ? 'text-[#b829e3]' : 'hover:text-[#b829e3]' ?> transition-colors duration-300">
                Home
                <span class="absolute -bottom-1 left-0 h-[3px] rounded-full bg-[#b829e3] transition-all duration-300 <?= ($current_path == '/' || $current_path == '/index.php') ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>

            <a href="/class" class="relative group <?= $current_path == '/class' ? 'text-[#b829e3]' : 'hover:text-[#b829e3]' ?> transition-colors duration-300">
                Class
                <span class="absolute -bottom-1 left-0 h-[3px] rounded-full bg-[#b829e3] transition-all duration-300 <?= $current_path == '/class' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>

            <a href="/streak" class="relative group <?= $current_path == '/streak' ? 'text-[#b829e3]' : 'hover:text-[#b829e3]' ?> transition-colors duration-300">
                Streak
                <span class="absolute -bottom-1 left-0 h-[3px] rounded-full bg-[#b829e3] transition-all duration-300 <?= $current_path == '/streak' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>
        </div>

        <div class="flex-1 flex justify-end items-center gap-4">
            <a href="/profile" class="h-11 w-11 rounded-full <?= htmlspecialchars($_SESSION['theme_color'] ?? 'bg-[#b829e3]') ?> hover:scale-105 flex items-center justify-center text-xl shadow-sm transition-all duration-300 cursor-pointer border-2 border-white overflow-hidden" title="Go to Profile">
                <?= htmlspecialchars($_SESSION['avatar'] ?? '🧑‍💻') ?>
            </a>
        </div>
    </nav>

    <?php if (in_array($msg, ['deleted', 'class_deleted', 'history_deleted', 'history_cleared'])) : ?>
        <?php
            $alertText = "";
            $alertIcon = "";
            $bgClass = "";
            
            if ($msg === 'deleted' || $msg === 'class_deleted') {
                $alertText = "Class successfully deleted.";
                $alertIcon = "✅";
                $bgClass = "bg-green-100 border-green-400 text-green-700";
            } elseif ($msg === 'history_deleted') {
                $alertText = "History successfully deleted.";
                $alertIcon = "🗑️";
                $bgClass = "bg-gray-100 border-gray-400 text-gray-700";
            } elseif ($msg === 'history_cleared') {
                $alertText = "All history has been cleared!";
                $alertIcon = "✨";
                $bgClass = "bg-purple-100 border-[#b829e3] text-[#b829e3]";
            }
        ?>
        <div id="toastNotification" class="fixed top-24 left-1/2 transform -translate-x-1/2 z-[200] <?= $bgClass ?> border-2 px-6 py-3.5 rounded-full shadow-2xl font-bold flex items-center gap-3 transition-all duration-1000 ease-in-out opacity-100 translate-y-0">
            <span class="text-2xl"><?= $alertIcon ?></span>
            <span class="tracking-wide"><?= $alertText ?></span>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('toastNotification');
                if (toast) {
                    toast.classList.remove('opacity-100', 'translate-y-0');
                    toast.classList.add('opacity-0', '-translate-y-10');
                    setTimeout(() => toast.style.display = 'none', 1000);
                }
            }, 2500);
        </script>
    <?php endif; ?>
    <div class="container mx-auto px-4 py-8 max-w-6xl flex-grow flex flex-col items-center">
        
        <div class="w-full max-w-md mt-6 mb-16">
            <div class="bg-white rounded-[2rem] p-10 shadow-xl border-b-8 border-gray-200 text-center">
                <h1 class="text-4xl font-black text-gray-800 mb-2">Join Game</h1>
                <p class="text-gray-500 font-medium mb-8">Enter the Game PIN provided by your host.</p>
                
                <form action="/join_class" method="POST" class="flex flex-col gap-4">
                    <input type="text" name="pin" placeholder="Game PIN" required autocomplete="off"
                           class="w-full text-center text-3xl font-black tracking-widest text-gray-800 placeholder-gray-300 bg-gray-50 border-4 border-gray-200 rounded-2xl py-5 focus:outline-none focus:border-[#b829e3] focus:bg-white transition-all <?= ($msg == 'invalid' || $msg == 'played') ? 'border-red-400 bg-red-50 shake-animation' : '' ?>">
                    <button type="submit" class="w-full bg-[#b829e3] hover:bg-[#9b1ebf] text-white font-black text-2xl py-5 rounded-2xl shadow-[0_6px_0_#8519a3] hover:shadow-[0_2px_0_#8519a3] hover:translate-y-1 transition-all">
                        Enter
                    </button>
                </form>

                <?php if ($msg === 'invalid'): ?>
                    <p class="text-red-500 font-bold mt-4">We didn't recognize that PIN. Please check and try again.</p>
                <?php elseif ($msg === 'played'): ?>
                    <p class="text-red-500 font-bold mt-4">You have already completed this game!</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="w-full border-t-2 border-gray-200 pt-10 mb-16 flex flex-col items-center">
            <div class="w-full flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight mb-2">Your History</h2>
                    <p class="text-gray-500 font-medium">Replay quizzes you've finished or clear your record.</p>
                </div>
                <?php if (count($history_classes) > 0) : ?>
                    <button onclick="openModal('clear_all_history', 0)" class="text-red-500 font-bold hover:bg-red-50 px-4 py-2 rounded-full transition border border-transparent hover:border-red-200">
                        🗑️ Clear All History
                    </button>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
                <?php if (count($history_classes) > 0) : ?>
                    <?php foreach ($history_classes as $history) : ?>
                        <div class="bg-white rounded-3xl p-6 shadow-sm border-2 border-gray-100 flex flex-col relative group hover:border-[#b829e3] transition-colors">
                            
                            <button onclick="openModal('delete_history', <?= $history['history_id'] ?>)" class="absolute top-4 right-4 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-full p-2 transition-all z-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div class="h-28 bg-gray-50 rounded-2xl mb-4 flex flex-col items-center justify-center border border-gray-100">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">Score Earned</span>
                                <span class="text-3xl font-black text-[#b829e3]"><?= number_format($history['points_earned']) ?></span>
                            </div>
                            
                            <h2 class="text-xl font-bold text-gray-800 mb-1 pr-8 truncate"><?= htmlspecialchars($history['nama_kelas']) ?></h2>
                            <p class="text-gray-400 text-xs mb-4">PIN: <?= $history['id'] ?></p>
                            
                            <div class="mt-auto pt-4 border-t border-gray-100">
                                <a href="/play_quiz?id=<?= $history['id'] ?>" class="block w-full text-center bg-gray-800 text-white font-bold py-2.5 rounded-full hover:bg-[#b829e3] transition shadow-md">
                                    Play Again ➔
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="col-span-full bg-white rounded-3xl p-8 text-center shadow-sm border border-gray-100">
                        <span class="text-4xl mb-2 block">👻</span>
                        <h3 class="text-lg text-gray-800 font-bold">No history yet.</h3>
                        <p class="text-gray-500 text-sm">Join a game to start building your record!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="w-full border-t-2 border-gray-200 pt-10 mb-8 flex flex-col md:flex-row justify-between items-end">
            <div class="mb-4 md:mb-0">
                <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight mb-2">Host a Game</h2>
                <p class="text-gray-500 font-medium">Share your Game PIN below with other players.</p>
            </div>
            <a href="/class/create" class="bg-gray-800 text-white px-6 py-2.5 rounded-full font-semibold shadow-md hover:bg-[#b829e3] hover:-translate-y-1 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Create New Game
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 pb-10 w-full">
            <?php if (count($my_classes) > 0) : ?>
                <?php foreach ($my_classes as $my_row) : ?>
                    <div class="bg-white rounded-3xl p-6 shadow-sm border-2 border-gray-100 group flex flex-col h-full relative">
                        
                        <button onclick="openModal('delete_class', <?= $my_row['id'] ?>)" class="absolute top-8 right-8 text-gray-400 hover:text-red-500 hover:scale-110 transition-all z-10 bg-white rounded-full p-1.5 shadow-md border border-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>

                        <div class="h-32 bg-purple-50 rounded-2xl mb-4 flex flex-col items-center justify-center border-2 border-purple-100">
                            <span class="text-sm font-bold text-purple-400 uppercase tracking-widest mb-1">Game PIN</span>
                            <span class="text-4xl font-black text-[#b829e3] tracking-widest"><?= $my_row['id'] ?></span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2 pr-8"><?= htmlspecialchars($my_row['nama_kelas'] ?? 'Nama Kelas') ?></h2>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2"><?= htmlspecialchars($my_row['deskripsi'] ?? 'Deskripsi kelas belum tersedia.') ?></p>
                        
                        <div class="mt-auto flex justify-between items-center pt-4 border-t border-gray-100">
                            <span class="text-gray-500 font-bold bg-gray-100 px-4 py-1.5 rounded-full text-xs">Your Creation</span>
                        </div>
                        <a href="/class/edit?id=<?= $my_row['id'] ?>" class="bg-[#b829e3]/60 hover:bg-[#a020c0] text-white font-bold py-2 px-4 mt-5 rounded-xl text-sm transition-all shadow-sm">
                        ✏️ Edit Class
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-span-full bg-gray-50 rounded-3xl p-10 text-center border-2 border-dashed border-gray-200">
                    <span class="text-5xl mb-4 block">✍️</span>
                    <h3 class="text-xl text-gray-800 font-bold mb-2">You haven't created any games yet.</h3>
                </div>
            <?php endif; ?>
        </div>
        
    </div>

    <div id="dynamicModal" class="fixed inset-0 z-[9999] flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 w-screen h-screen">
        
        <div class="fixed inset-0 backdrop-blur-sm w-screen h-screen" onclick="closeModal()"></div>
        
        <div id="modalBox" class="bg-white rounded-[2rem] p-8 shadow-2xl max-w-sm w-full mx-4 relative z-10 transform scale-95 transition-transform duration-300 text-center border-4 border-red-50">
            <div class="w-20 h-20 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 id="modalTitle" class="text-2xl font-black text-gray-800 mb-2">Confirm</h3>
            <p id="modalDesc" class="text-gray-500 font-medium mb-8 text-sm px-2">Are you sure you want to delete this?</p>
            <div class="flex gap-3">
                <button onclick="closeModal()" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3.5 rounded-full hover:bg-gray-200 transition-colors">Cancel</button>
                <a id="confirmActionBtn" href="#" class="flex-1 bg-red-500 text-white font-bold py-3.5 rounded-full hover:bg-red-600 transition-colors shadow-lg shadow-red-200 flex items-center justify-center gap-2">Delete</a>
            </div>
        </div>
    </div>
    
    <script>
        function openModal(type, id) {
            const modal = document.getElementById('dynamicModal');
            const modalBox = document.getElementById('modalBox');
            const confirmBtn = document.getElementById('confirmActionBtn');
            const title = document.getElementById('modalTitle');
            const desc = document.getElementById('modalDesc');

           
            if (type === 'delete_class') {
                title.innerText = 'Delete Class?';
                desc.innerHTML = 'All questions within it will <strong class="text-red-500">permanently disappear</strong> for all players.';
                confirmBtn.href = `/delete_class?id=${id}`;
            } else if (type === 'delete_history') {
                title.innerText = 'Delete History?';
                desc.innerHTML = 'This game history will be removed from your account.';
                confirmBtn.href = `/delete_history?id=${id}`;
            } else if (type === 'clear_all_history') {
                title.innerText = 'Clear All History?';
                desc.innerHTML = 'All your game history will be <strong class="text-red-500">permanently deleted</strong>.';
                confirmBtn.href = `/clear_history`;
            }

            
            const currentScroll = window.scrollY || window.pageYOffset || document.documentElement.scrollTop;
            const screenCenter = currentScroll + (window.innerHeight / 2);

           
            modal.style.top = `${screenCenter}px`;
            modal.style.transform = 'translateY(-50%)';

           
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modalBox.classList.remove('scale-95');
            modalBox.classList.add('scale-100');

           
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('dynamicModal');
            const modalBox = document.getElementById('modalBox');

           
            modal.classList.add('opacity-0', 'pointer-events-none');
            modalBox.classList.remove('scale-100');
            modalBox.classList.add('scale-95');

            
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        }
    </script>

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
                        }, 250); 
                    }
                });
            });
        });
    </script>
</body>
</html>