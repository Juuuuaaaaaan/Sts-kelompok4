<?php
session_start();
require_once '../core/Database.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$class_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$class_id) {
    header("Location: class.php");
    exit();
}

try {
    $stmt_class = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt_class->execute([$class_id]);
    $class = $stmt_class->fetch(PDO::FETCH_ASSOC);

    if (!$class) {
        die("Kelas tidak ditemukan.");
    }

    $stmt_questions = $pdo->prepare("SELECT * FROM questions WHERE class_id = ?");
    $stmt_questions->execute([$class_id]);
    $questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playing: <?= htmlspecialchars($class['nama_kelas']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: #b829e3; 
            overflow: hidden;
        }
        
        .bottom-curve {
            position: fixed;
            bottom: -10vh;
            left: -20vw;
            right: -20vw;
            height: 40vh;
            background-color: #c946f2;
            border-radius: 50% 50% 0 0;
            z-index: -1;
        }

        .hidden-question { display: none !important; }
        
        .option-btn { transition: transform 0.1s ease-in-out; }
        .option-btn:active { transform: scale(0.95); }
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
<body class="min-h-screen relative flex flex-col">

    <div class="bottom-curve"></div>

    <nav class="flex justify-between items-start p-8 text-white relative z-10">
        <div class="text-2xl font-black leading-tight tracking-wide">
            Fun<br>Streak
        </div>
        
        <a href="class.php" class="text-lg font-medium flex items-center hover:opacity-80 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
    </nav>

    <main class="flex-grow flex flex-col items-center justify-center p-4 relative z-10 w-full max-w-6xl mx-auto -mt-10">
        
        <?php if (count($questions) > 0) : ?>
        <form id="quiz-form" action="submit_quiz.php" method="POST" class="w-full relative">
            <input type="hidden" name="class_id" value="<?= $class_id ?>">

            <?php foreach ($questions as $index => $q) : ?>
                <div id="q-block-<?= $index ?>" class="w-full flex flex-col items-center <?= $index === 0 ? '' : 'hidden-question' ?>">
                    
                    <div class="flex w-full justify-center items-center relative mb-12">
                        
                        <div class="bg-white border-4 border-[#00d2ff] rounded-xl p-8 md:p-12 w-full max-w-4xl min-h-[250px] flex items-center justify-center shadow-lg mx-auto z-10">
                            <h2 class="text-3xl md:text-5xl font-bold text-gray-700 text-center leading-snug">
                                <?= htmlspecialchars($q['question_text']) ?>
                            </h2>
                        </div>

                        <div class="absolute right-0 md:-right-16 top-1/2 transform -translate-y-1/2 flex flex-col items-center gap-4 hidden md:flex">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-4xl font-black text-[#b829e3] shadow-md">
                                <?= $index + 1 ?>
                            </div>
                            <div class="relative w-16 h-16 rounded-full border-4 border-white flex items-center justify-center text-white text-2xl font-bold shadow-md">
                                <span id="timer-display-<?= $index ?>">30</span>
                                <div class="absolute -bottom-2 -right-2 bg-[#b829e3] rounded-full p-1 border-2 border-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="absolute -top-10 right-0 flex gap-4 md:hidden">
                            <div class="bg-white text-[#b829e3] px-3 py-1 rounded-full font-bold">#<?= $index + 1 ?></div>
                            <div class="border-2 border-white text-white px-3 py-1 rounded-full font-bold timer-mobile-<?= $index ?>">30s</div>
                        </div>

                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 w-full max-w-5xl">
                        
                        <?php 
                        $options = [
                            'A' => $q['option_a'],
                            'B' => $q['option_b'],
                            'C' => $q['option_c'],
                            'D' => $q['option_d']
                        ];
                        foreach ($options as $key => $val) : 
                        ?>
                            <label class="option-btn relative cursor-pointer block h-32 md:h-48 group">
                                <input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= $key ?>" class="hidden peer" required onchange="nextQuestion(<?= $index ?>)">
                                
                                <div class="w-full h-full bg-[#9b1ebf] group-hover:bg-[#8519a3] rounded-2xl p-4 flex flex-col items-center justify-center text-center shadow-lg border-2 border-transparent peer-checked:border-white peer-checked:bg-[#72158c] transition-all">
                                    <span class="text-white text-lg md:text-2xl font-semibold leading-tight px-2">
                                        <?= htmlspecialchars($val) ?>
                                    </span>
                                </div>
                            </label>
                        <?php endforeach; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </form>
        <?php else : ?>
            <div class="bg-white p-10 rounded-2xl text-center">
                <p class="text-gray-500 text-xl font-bold mb-4">No questions available.</p>
                <a href="class.php" class="bg-[#b829e3] text-white px-6 py-2 rounded-full font-bold">Go Back</a>
            </div>
        <?php endif; ?>

    </main>

    <script>
        let currentQuestion = 0;
        const totalQuestions = <?= count($questions) ?>;
        let timeLeft = 30; 
        let timerInterval;

        function startTimer(index) {
            timeLeft = 30;
            const timerDesktop = document.getElementById(`timer-display-${index}`);
            const timerMobile = document.querySelector(`.timer-mobile-${index}`);
            
            clearInterval(timerInterval);

            timerInterval = setInterval(() => {
                timeLeft--;
                
                if(timerDesktop) timerDesktop.innerText = timeLeft;
                if(timerMobile) timerMobile.innerText = timeLeft + 's';

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    autoSkip(index);
                }
            }, 1000);
        }

        function nextQuestion(index) {
            clearInterval(timerInterval);
            
            setTimeout(() => {
                const currentBlock = document.getElementById(`q-block-${index}`);
                const nextBlock = document.getElementById(`q-block-${index + 1}`);

                if (nextBlock) {
                    currentBlock.classList.add('hidden-question');
                    nextBlock.classList.remove('hidden-question');
                    currentQuestion++;
                    startTimer(currentQuestion);
                } else {
                    document.getElementById('quiz-form').submit();
                }
            }, 300); // delay 0.3 detik
        }

        function autoSkip(index) {
            nextQuestion(index);
        }

        if (totalQuestions > 0) {
            startTimer(0);
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