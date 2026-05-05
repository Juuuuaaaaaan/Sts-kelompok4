<?php
session_start();
// Pastikan path ke db.php benar
require_once dirname(__DIR__) . '/db.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$total_points = 0;

try {
    // Ingat: ganti 'streak' jika nama kolom di databasemu adalah 'points'
    $stmt = $pdo->prepare("SELECT points FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user) {
        $total_points = $user['points'];
    }
} catch(PDOException $e) {
    die("Error mengambil data poin: " . $e->getMessage());
}

// Fitur UI Level & Progress
$level = floor($total_points / 100) + 1;
$next_level_points = $level * 100;
$progress_percent = ($total_points % 100); 
if ($progress_percent == 0 && $total_points > 0) $progress_percent = 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streak - Fun Streak</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
        body {
            /* Saat halaman baru dimuat: muncul dari bawah meluncur ke posisi asli */
            animation: slideInPage 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        
        body.page-exit {
            /* Saat link diklik: meluncur ke atas sambil menghilang */
            animation: slideOutPage 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes slideInPage {
            0% { 
                opacity: 0; 
                transform: translateY(20px); /* Mulai dari 20px di bawah */
            }
            100% { 
                opacity: 1; 
                transform: translateY(0); /* Kembali ke posisi semula */
            }
        }

        @keyframes slideOutPage {
            0% { 
                opacity: 1; 
                transform: translateY(0); 
            }
            100% { 
                opacity: 0; 
                transform: translateY(-20px); /* Meluncur 20px ke atas */
            }
        }
    </style>
</head>
<body class="bg-[#f9f9f9] min-h-screen flex flex-col font-['Outfit']">

<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <nav class="flex justify-between items-center p-6 bg-white shadow-sm z-10 sticky top-0">
        <a href="../../index.php" class="text-3xl font-bold text-[#b829e3] tracking-wide cursor-pointer hover:scale-105 transition-transform duration-300">Fun Streak</a>
        
        <div class="flex gap-10 text-gray-500 font-semibold items-center text-lg hidden md:flex">
            <a href="../../index.php" class="relative group <?= $current_page == 'index.php' ? 'text-[#b829e3]' : 'hover:text-[#b829e3]' ?> transition-colors duration-300">
                Home
                <span class="absolute -bottom-1 left-0 h-[3px] rounded-full bg-[#b829e3] transition-all duration-300 <?= $current_page == 'index.php' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>

            <a href="class.php" class="relative group <?= $current_page == 'class.php' ? 'text-[#b829e3]' : 'hover:text-[#b829e3]' ?> transition-colors duration-300">
                Class
                <span class="absolute -bottom-1 left-0 h-[3px] rounded-full bg-[#b829e3] transition-all duration-300 <?= $current_page == 'class.php' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>

            <a href="streak.php" class="relative group <?= $current_page == 'streak.php' ? 'text-[#b829e3]' : 'hover:text-[#b829e3]' ?> transition-colors duration-300">
                Streak
                <span class="absolute -bottom-1 left-0 h-[3px] rounded-full bg-[#b829e3] transition-all duration-300 <?= $current_page == 'streak.php' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>
        </div>

        <div class="flex gap-5 items-center">
            <div class="h-10 w-10 rounded-full bg-purple-200 flex items-center justify-center text-[#b829e3] font-bold text-xl uppercase">
                <?= substr($_SESSION['username'], 0, 1) ?>
            </div>
            <a href="../controllers/logout.php" class="border-2 border-red-400 text-red-500 px-6 py-1.5 rounded-full font-semibold shadow-sm hover:bg-red-50 transition-all duration-300">Log Out</a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-5xl flex-grow">
        
        <div class="mb-10 text-center mt-4">
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight mb-2">
                Your Journey, <span class="capitalize"><?= htmlspecialchars($username) ?></span>!
            </h1>
            <p class="text-gray-500 font-medium text-lg">Keep completing quizzes to earn points and level up.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="md:col-span-2 bg-white rounded-3xl p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-transparent hover:border-purple-100 hover:-translate-y-2 hover:shadow-[0_15px_40px_rgba(184,41,227,0.1)] transition-all duration-300 flex flex-col justify-center items-center text-center group">
                
                <div class="text-6xl mb-4 group-hover:scale-110 transition-transform duration-300">🏆</div>
                <p class="text-gray-400 font-bold tracking-widest uppercase text-sm mb-2">Total Score</p>
                
                <h2 class="text-7xl md:text-8xl font-black text-[#b829e3] mb-6 tracking-tighter">
                    <?= number_format($total_points) ?> <span class="text-3xl text-purple-300 font-bold tracking-normal">PTS</span>
                </h2>
                
                <div class="inline-flex items-center bg-purple-50 text-[#b829e3] px-6 py-2.5 rounded-full text-sm font-bold border border-purple-100">
                    <span class="mr-2 text-lg">🔥</span> Level <?= $level ?> Explorer
                </div>

            </div>

            <div class="flex flex-col gap-8">
                
                <div class="bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-transparent hover:border-purple-100 transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-[#b829e3] text-2xl">🚀</div>
                        <h3 class="font-bold text-gray-800 text-lg">Next Target</h3>
                    </div>
                    
                    <div class="flex justify-between text-sm font-bold text-gray-400 mb-3">
                        <span>Lvl <?= $level ?></span>
                        <span class="text-[#b829e3]">Lvl <?= $level + 1 ?></span>
                    </div>
                    
                    <div class="w-full bg-gray-100 rounded-full h-4 mb-5 overflow-hidden">
                        <div class="bg-[#b829e3] h-4 rounded-full transition-all duration-1000" style="width: <?= $progress_percent ?>%"></div>
                    </div>
                    
                    <p class="text-sm text-gray-500 font-medium text-center">
                        <strong class="text-[#b829e3]"><?= $next_level_points - $total_points ?> PTS</strong> more to rank up!
                    </p>
                </div>

                <div class="bg-purple-50 rounded-3xl p-8 border border-purple-100 flex-1 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="absolute -right-4 -bottom-4 text-7xl opacity-10 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500">💡</div>
                    <h3 class="font-bold text-purple-900 text-lg mb-3 relative z-10">Pro Tip!</h3>
                    <p class="text-purple-700 text-sm leading-relaxed font-medium relative z-10">
                        Answer questions quickly and accurately in the quiz section to maximize your points and climb the ranks faster.
                    </p>
                </div>

            </div>
        </div>
    </div>

<script>
        document.addEventListener('DOMContentLoaded', () => {
            const links = document.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.hostname === window.location.hostname && this.target !== '_blank' && !this.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        const destination = this.href;
                        
                        // Tambahkan animasi keluar
                        document.body.classList.add('page-exit');
                        
                        // Tunggu animasi hampir selesai, lalu pindah halaman
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