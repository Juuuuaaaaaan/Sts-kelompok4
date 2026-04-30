<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php?error=Silakan login terlebih dahulu");
    exit();
}
// Kita asumsikan ID kelas ini adalah 1 (nanti bisa dinamis dari $_GET['id'])
$class_id = 1; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Quiz - Fun Streak</title>
    <link rel="stylesheet" href="../../public/css/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* Background kurva ala Kahoot/Screenshot */
        .curved-bg {
            background-color: #b829e3;
            background-image: radial-gradient(150% 100% at 50% 100%, #d970fa 0%, #d970fa 40%, #b829e3 40.1%, #b829e3 100%);
        }
        .fade-in { animation: fadeIn 0.5s ease-in-out forwards; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-['Outfit'] curved-bg overflow-x-hidden">

    <header class="p-6 flex justify-between items-center text-white sticky top-0 z-40">
        <div class="leading-none">
            <span class="text-3xl font-extrabold tracking-tight block">Fun</span>
            <span class="text-3xl font-extrabold tracking-tight block">Streak</span>
        </div>
        
        <div class="flex items-center space-x-6">
            <a href="class.php" class="flex items-center text-lg font-medium hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back
            </a>
            
            <div id="quiz-info" class="hidden flex items-center space-x-4">
                <div id="question-number" class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-2xl font-black text-[#b829e3] shadow-md">1</div>
                <div id="timer" class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-2xl font-black text-[#b829e3] shadow-md relative">
                    30
                    <svg class="w-4 h-4 text-[#b829e3] absolute -bottom-1 -right-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </header>

    <main id="intro-screen" class="flex-grow flex flex-col items-center mt-20 relative z-10 fade-in w-full px-6">
        <div class="bg-white py-16 px-10 rounded-md shadow-sm w-full max-w-4xl text-center mb-10">
            <h1 class="text-5xl font-medium text-gray-700 tracking-wide">Guess The Painting</h1>
        </div>
        <button onclick="startQuiz()" class="bg-[#9c27b0] text-white px-24 py-5 rounded-full font-medium text-4xl shadow-md hover:bg-[#8e24aa] transition-transform hover:scale-105">
            Start
        </button>
    </main>

    <main id="quiz-container" class="hidden flex-grow flex flex-col items-center justify-center p-8 mt-4 relative z-10 w-full">
        <div class="bg-white p-8 rounded-md shadow-sm w-full max-w-5xl border border-gray-100 flex items-center mb-10">
            <div class="w-2/3 pr-8">
                <h1 id="question-text" class="text-4xl font-medium text-gray-700 leading-snug">Mencari soal...</h1>
            </div>
            <div class="w-1/3 flex justify-center">
                <img id="question-image" src="" alt="Soal" class="w-full max-w-xs h-auto rounded-md shadow-md hidden">
            </div>
        </div>
        <div id="answer-options" class="grid grid-cols-2 lg:grid-cols-4 gap-4 w-full max-w-5xl">
            </div>
    </main>

    <div id="feedback-popup" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-opacity-90 backdrop-blur-sm transition-opacity">
        <div id="feedback-content" class="bg-white px-20 py-12 rounded-3xl shadow-2xl text-center transform scale-50 opacity-0 transition-all duration-300">
            <div id="feedback-icon" class="text-8xl mb-6">✅</div>
            <h2 id="feedback-text" class="text-6xl font-black mb-2 tracking-tight">Benar!</h2>
            <p id="feedback-points" class="text-2xl font-bold text-gray-500 mt-2 bg-gray-100 px-6 py-2 rounded-full hidden">+100 Points 🔥</p>
        </div>
    </div>

    <script>
        const quizData = [
            { number: 1, question: "Who Is The Painter Of This Painting?", image: "https://upload.wikimedia.org/wikipedia/commons/d/d7/Meisje_met_de_parel.jpg", answers: ["Johannes Vermeer (Johan Vermeer)", "Vincent Willem Van Gogh", "Rembrandt Harmenszoon Van Rijn", "Leonardo Di Ser Piero Da Vinci"], correct: 0 },
            { number: 2, question: "Who painted the Mona Lisa?", image: "", answers: ["Vincent Van Gogh", "Pablo Picasso", "Leonardo Da Vinci", "Claude Monet"], correct: 2 }
        ];

        let currentQuestionIndex = 0;
        let scorePoints = 0; // Menggunakan poin (streak)
        let timeLeft = 30;
        let timerInterval;

        const introScreen = document.getElementById('intro-screen');
        const quizContainer = document.getElementById('quiz-container');
        const quizInfo = document.getElementById('quiz-info');
        
        const questionNumberDisplay = document.getElementById('question-number');
        const timerDisplay = document.getElementById('timer');
        const questionTextDisplay = document.getElementById('question-text');
        const questionImageDisplay = document.getElementById('question-image');
        const answerOptionsDisplay = document.getElementById('answer-options');
        
        const feedbackPopup = document.getElementById('feedback-popup');
        const feedbackContent = document.getElementById('feedback-content');

        // Fungsi Mulai dari Layar Start
        function startQuiz() {
            introScreen.classList.add('hidden');
            quizInfo.classList.remove('hidden');
            quizContainer.classList.remove('hidden');
            quizContainer.classList.add('fade-in');
            loadQuestion(0);
        }

        function loadQuestion(index) {
            clearInterval(timerInterval);
            timeLeft = 30; updateTimerDisplay();
            const currentQ = quizData[index];
            
            questionNumberDisplay.textContent = currentQ.number;
            questionTextDisplay.textContent = currentQ.question;
            
            if (currentQ.image) {
                questionImageDisplay.src = currentQ.image;
                questionImageDisplay.classList.remove('hidden');
            } else {
                questionImageDisplay.classList.add('hidden');
            }

            answerOptionsDisplay.innerHTML = ''; 
            currentQ.answers.forEach((answer, i) => {
                const btn = document.createElement('button');
                btn.className = "bg-[#9c27b0] text-white p-6 rounded-2xl shadow-sm text-center text-lg font-medium hover:bg-[#8e24aa] transition-transform hover:-translate-y-1 active:scale-95 min-h-[120px] flex items-center justify-center";
                btn.innerHTML = `<span>${answer}</span>`;
                btn.onclick = () => selectAnswer(i, btn);
                answerOptionsDisplay.appendChild(btn);
            });

            timerInterval = setInterval(updateTimer, 1000);
        }

        function selectAnswer(selectedIndex, clickedBtn) {
            clearInterval(timerInterval); 
            const correctIndex = quizData[currentQuestionIndex].correct;
            const buttons = answerOptionsDisplay.getElementsByTagName('button');

            for (let btn of buttons) { btn.disabled = true; btn.classList.add('opacity-80'); }

            if (selectedIndex === correctIndex) {
                clickedBtn.classList.replace('bg-[#9c27b0]', 'bg-green-500');
                scorePoints += 100; // Tiap benar dapat 100 Poin (Streak)
                showFeedback(true);
            } else {
                clickedBtn.classList.replace('bg-[#9c27b0]', 'bg-red-500');
                buttons[correctIndex].classList.replace('bg-[#9c27b0]', 'bg-green-500');
                showFeedback(false);
            }

            setTimeout(() => { hideFeedback(); setTimeout(nextQuestion, 300); }, 2000);
        }

        function showFeedback(isCorrect) {
            feedbackPopup.classList.remove('hidden');
            setTimeout(() => { feedbackContent.classList.replace('scale-50', 'scale-100'); feedbackContent.classList.replace('opacity-0', 'opacity-100'); }, 10);
            
            const icon = document.getElementById('feedback-icon');
            const txt = document.getElementById('feedback-text');
            const pts = document.getElementById('feedback-points');

            if (isCorrect) {
                feedbackPopup.className = "fixed inset-0 z-50 flex items-center justify-center bg-green-500 bg-opacity-90 backdrop-blur-sm";
                icon.innerHTML = "🎉"; txt.textContent = "Benar!"; txt.className = "text-6xl font-black text-green-500 mb-2";
                pts.classList.remove('hidden');
            } else {
                feedbackPopup.className = "fixed inset-0 z-50 flex items-center justify-center bg-red-500 bg-opacity-90 backdrop-blur-sm";
                icon.innerHTML = "❌"; txt.textContent = "Salah!"; txt.className = "text-6xl font-black text-red-500 mb-2";
                pts.classList.add('hidden');
            }
        }

        function hideFeedback() {
            feedbackContent.classList.replace('scale-100', 'scale-50'); feedbackContent.classList.replace('opacity-100', 'opacity-0');
            setTimeout(() => { feedbackPopup.classList.add('hidden'); }, 300);
        }

        function nextQuestion() {
            currentQuestionIndex++;
            if (currentQuestionIndex < quizData.length) {
                quizContainer.classList.remove('fade-in'); void quizContainer.offsetWidth; quizContainer.classList.add('fade-in');
                loadQuestion(currentQuestionIndex);
            } else { finishQuiz(); }
        }

        function updateTimer() {
            timeLeft--; updateTimerDisplay();
            if (timeLeft <= 0) { clearInterval(timerInterval); showFeedback(false); setTimeout(() => { hideFeedback(); setTimeout(nextQuestion, 300); }, 2000); }
        }

        function updateTimerDisplay() {
            timerDisplay.textContent = timeLeft;
            timeLeft <= 5 ? timerDisplay.classList.add('text-red-500') : timerDisplay.classList.remove('text-red-500');
        }

        function finishQuiz() {
            quizInfo.classList.add('hidden');
            // Form untuk mengirim Poin (Streak) dan status selesai ke database
            quizContainer.innerHTML = `
                <div class="bg-white p-12 rounded-xl shadow-md text-center w-full max-w-2xl fade-in">
                    <h1 class="text-4xl font-black text-[#b829e3] mb-4">Kelas Selesai!</h1>
                    <p class="text-xl text-gray-500 mb-6">Streak (Poin) yang kamu dapatkan:</p>
                    <div class="text-8xl font-black text-gray-800 mb-8">+${scorePoints} <span class="text-3xl text-gray-400 font-medium">pts</span></div>
                    
                    <form action="../controllers/complete_class.php" method="POST">
                        <input type="hidden" name="class_id" value="<?= $class_id ?>">
                        <input type="hidden" name="points" value="${scorePoints}">
                        <button type="submit" class="bg-[#b829e3] text-white px-10 py-4 rounded-full font-bold text-xl hover:bg-[#a01ebd] transition shadow-md w-full">
                            Klaim Streak & Kembali
                        </button>
                    </form>
                </div>
            `;
        }
    </script>
</body>
</html>