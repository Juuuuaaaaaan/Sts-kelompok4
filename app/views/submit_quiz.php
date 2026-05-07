<?php
session_start();
require_once '../core/Database.php';

if (!isset($_SESSION['username']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: class.php");
    exit();
}

$username = $_SESSION['username'];
$class_id = $_POST['class_id'] ?? null;
$user_answers = $_POST['answer'] ?? [];

if (!$class_id) {
    die("Data kelas tidak valid.");
}

$correct_count = 0;
$total_questions = 0;
$points_earned = 0;

try {
    $stmt = $pdo->prepare("SELECT id, correct_option FROM questions WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_questions = count($questions);

    foreach ($questions as $q) {
        $q_id = $q['id'];
        if (isset($user_answers[$q_id]) && $user_answers[$q_id] === $q['correct_option']) {
            $correct_count++;
        }
    }

    $points_earned = $correct_count * 100;

    $stmt_check = $pdo->prepare("SELECT id FROM completed_classes WHERE username = ? AND class_id = ?");
    $stmt_check->execute([$username, $class_id]);
    
    if (!$stmt_check->fetch()) {
        $stmt_insert = $pdo->prepare("INSERT INTO completed_classes (username, class_id, points_earned) VALUES (?, ?, ?)");
        $stmt_insert->execute([$username, $class_id, $points_earned]);

        $stmt_update = $pdo->prepare("UPDATE users SET points = points + ? WHERE username = ?");
        $stmt_update->execute([$points_earned, $username]);
    }

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result - Fun Streak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: #b829e3; 
        }
        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pop { animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    </style>
    <style>
        body {
            animation: fadeInPage 0.05s ease-out forwards;
        }
        body.page-exit {
            animation: fadeOutPage 0.05s ease-in forwards;
        }
        @keyframes fadeInPage {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        @keyframes fadeOutPage {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-[#d254f5] rounded-full mix-blend-screen filter blur-[100px] opacity-70"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-[#9b1ebf] rounded-full mix-blend-screen filter blur-[100px] opacity-70"></div>
    </div>

    <div class="bg-white rounded-[3rem] p-10 md:p-16 shadow-2xl text-center max-w-2xl w-full relative z-10 animate-pop border-8 border-purple-200">
        
        <?php if ($correct_count === $total_questions && $total_questions > 0) : ?>
            <div class="text-7xl mb-4">🏆</div>
            <h1 class="text-4xl md:text-5xl font-black text-gray-800 mb-2">Perfect Score!</h1>
            <p class="text-gray-500 font-medium mb-8">Flawless victory! You answered everything correctly.</p>
        <?php elseif ($correct_count > 0) : ?>
            <div class="text-7xl mb-4">🔥</div>
            <h1 class="text-4xl md:text-5xl font-black text-gray-800 mb-2">Great Job!</h1>
            <p class="text-gray-500 font-medium mb-8">You're doing great, keep up the momentum.</p>
        <?php else : ?>
            <div class="text-7xl mb-4">💪</div>
            <h1 class="text-4xl md:text-5xl font-black text-gray-800 mb-2">Keep Trying!</h1>
            <p class="text-gray-500 font-medium mb-8">Don't give up, you can do better next time.</p>
        <?php endif; ?>

        <div class="bg-purple-50 rounded-3xl p-8 mb-10 flex flex-col md:flex-row items-center justify-around gap-6">
            
            <div class="text-center">
                <p class="text-purple-400 font-bold uppercase tracking-widest text-sm mb-1">Correct</p>
                <div class="text-4xl font-black text-gray-800">
                    <?= $correct_count ?> <span class="text-2xl text-gray-400">/ <?= $total_questions ?></span>
                </div>
            </div>

            <div class="w-full md:w-px h-px md:h-16 bg-purple-200"></div>

            <div class="text-center">
                <p class="text-purple-400 font-bold uppercase tracking-widest text-sm mb-1">Points Earned</p>
                <div class="text-5xl font-black text-[#b829e3]">
                    +<?= number_format($points_earned) ?>
                </div>
            </div>

        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="streak.php" class="bg-[#b829e3] text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-purple-800 transition-colors shadow-lg shadow-purple-200 w-full sm:w-auto flex-1">
                Check My Rank ⭐
            </a>
            <a href="class.php" class="bg-gray-100 text-gray-700 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-200 transition-colors w-full sm:w-auto flex-1">
                Play Another Class
            </a>
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
                        document.body.classList.add('page-exit');
                        setTimeout(() => {
                            window.location.href = destination;
                        }, 400);
                    }
                });
            });
        });
    </script>
</body>
</html>