<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fun Streak</title>
    
    <link rel="stylesheet" href="../../public/css/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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

        @keyframes flyUp {
            0% { transform: translateY(0) rotate(0deg); opacity: 0; }
            10% { opacity: 0.2; }
            90% { opacity: 0.2; }
            100% { transform: translateY(-110vh) rotate(180deg); opacity: 0; }
        }

        .star-sparkle {
            position: absolute;
            background-color: #b829e3;
            clip-path: polygon(50% 0%, 61% 35%, 100% 50%, 61% 65%, 50% 100%, 39% 65%, 0% 50%, 39% 35%);
            pointer-events: none;
            animation: flyUp linear infinite;
            z-index: 1;
        }
    </style>
</head>

<body class="bg-white min-h-screen flex flex-col font-['Outfit'] relative overflow-x-hidden">

    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
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

    <main class="flex-grow flex flex-col items-center justify-center relative overflow-hidden">
        
        <div class="absolute inset-0 z-0 pointer-events-none">
            <?php 
                $jumlah_bintang = 30;
                for ($i = 0; $i < $jumlah_bintang; $i++) {
                    $ukuran = rand(10, 30); 
                    $posisi_kiri = rand(0, 100); 
                    $durasi = rand(15, 30); 
                    $delay = rand(1, 30); 
                    
                    echo "<div class='star-sparkle' style='
                        width: {$ukuran}px; 
                        height: {$ukuran}px; 
                        left: {$posisi_kiri}%; 
                        bottom: -50px; 
                        animation-delay: -{$delay}s; 
                        animation-duration: {$durasi}s;
                    '></div>";
                }
            ?>
        </div>

        <div class="text-center z-10 mb-32 hover:scale-105 transition-transform duration-500 cursor-default">
            <h1 class="text-[7rem] font-extrabold text-[#b829e3] tracking-tight leading-none mb-2 relative">
                Fun Streak
            </h1>
            <p class="text-[2.5rem] text-[#c765e8] font-medium tracking-wide">hard work by yourself</p>
        </div>
        
        <div class="absolute bottom-0 w-full h-[40%] bg-[#b829e3] rounded-t-[100%] flex justify-center shadow-[0_-15px_30px_rgba(184,41,227,0.15)] z-20">
            <a href="app/views/class.php" class="absolute -top-7 bg-white text-[#b829e3] text-2xl font-bold px-14 py-4 rounded-full shadow-[0_10px_25px_rgba(184,41,227,0.4)] hover:scale-110 transition-all duration-300">
                Start Now
            </a>
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