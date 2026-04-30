<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php?error=Silakan login terlebih dahulu");
    exit();
}
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
        /* Desain Latar Belakang Kahoot dengan Gradasi Melengkung */
        .curved-bg {
            background-color: #a855f7;
            background-image: linear-gradient(to bottom, #a855f7, #e9d5ff);
            clip-path: ellipse(150% 100% at 50% 100%);
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-['Outfit'] curved-bg overflow-x-hidden">

    <header class="p-6 flex justify-between items-center text-white sticky top-0 z-40">
        <a href="../../index.php" class="text-3xl font-extrabold tracking-tight hover:scale-105 transition-transform duration-300">Fun Streak</a>
        <div class="flex items-center space-x-6">
            <a href="class.php" class="flex items-center text-lg font-semibold hover:text-gray-200 transition-colors">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back
            </a>
            <div id="question-number" class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-3xl font-black text-[#a855f7] shadow-lg">1</div>
            <div id="timer" class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-3xl font-black text-[#a855f7] shadow-lg relative transition-transform duration-300">
                30
                <svg class="w-4 h-4 text-[#a855f7] absolute -bottom-1 -right-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </header>

    <main id="quiz-container" class="flex-grow flex flex-col items-center justify-center p-8 mt-12 fade-in relative z-10">
        <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-6xl border-4 border-blue-100 flex items-center mb-16">
            <div class="w-2/3 pr-12">
                <h1 id="question-text" class="text-5xl font-extrabold text-gray-800 leading-tight">Mencari soal...</h1>
            </div>
            <div class="w-1/3 flex justify-center">
                <img id="question-image" src="" alt="Soal Gambar" class="w-full h-auto rounded-3xl shadow-xl border border-gray-100 hidden">
            </div>
        </div>

        <div id="answer-options" class="grid grid-cols-2 gap-8 w-full max-w-6xl">
            </div>
    </main>

    <div id="feedback-popup" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-opacity-90 transition-colors duration-300 backdrop-blur-sm">
        <div id="feedback-content" class="bg-white px-20 py-12 rounded-3xl shadow-2xl transform scale-50 opacity-0 transition-all duration-300 text-center flex flex-col items-center">
            <div id="feedback-icon" class="text-8xl mb-6 drop-shadow-md">✅</div>
            <h2 id="feedback-text" class="text-6xl font-black mb-2 tracking-tight">Benar!</h2>
            <p id="feedback-points" class="text-2xl font-bold text-gray-500 mt-2 bg-gray-100 px-6 py-2 rounded-full hidden">+100 Points 🔥</p>
        </div>
    </div>

    <script>
        const quizData = [
            {
                number: 1,
                question: "Siapa pelukis dari lukisan terkenal ini?",
                image: "https://upload.wikimedia.org/wikipedia/commons/d/d7/Meisje_met_de_parel.jpg", 
                answers: ["Johannes Vermeer", "Vincent van Gogh", "Rembrandt", "Leonardo da Vinci"],
                correct: 0 
            },
            {
                number: 2,
                question: "Apa warna dasar bendera Indonesia?",
                image: "https://upload.wikimedia.org/wikipedia/commons/9/9f/Flag_of_Indonesia.svg", 
                answers: ["Merah Kuning", "Biru Putih", "Merah Putih", "Hijau Putih"],
                correct: 2
            }
        ];

        let currentQuestionIndex = 0;
        let score = 0;
        let timeLeft = 30;
        let timerInterval;

        const questionNumberDisplay = document.getElementById('question-number');
        const timerDisplay = document.getElementById('timer');
        const questionTextDisplay = document.getElementById('question-text');
        const questionImageDisplay = document.getElementById('question-image');
        const answerOptionsDisplay = document.getElementById('answer-options');
        const quizContainer = document.getElementById('quiz-container');

        // Elemen Pop-up
        const feedbackPopup = document.getElementById('feedback-popup');
        const feedbackContent = document.getElementById('feedback-content');
        const feedbackIcon = document.getElementById('feedback-icon');
        const feedbackText = document.getElementById('feedback-text');
        const feedbackPoints = document.getElementById('feedback-points');

        function loadQuestion(index) {
            clearInterval(timerInterval);
            timeLeft = 30; 
            updateTimerDisplay();
            
            const currentQuestion = quizData[index];
            
            questionNumberDisplay.textContent = currentQuestion.number;
            questionTextDisplay.textContent = currentQuestion.question;
            
            if (currentQuestion.image) {
                questionImageDisplay.src = currentQuestion.image;
                questionImageDisplay.classList.remove('hidden');
            } else {
                questionImageDisplay.classList.add('hidden');
            }

            answerOptionsDisplay.innerHTML = ''; 
            currentQuestion.answers.forEach((answer, i) => {
                const button = document.createElement('button');
                button.className = "bg-purple-900 text-white p-8 rounded-3xl shadow-xl text-center text-2xl font-bold hover:bg-purple-800 transition transform hover:-translate-y-2 active:scale-95";
                button.innerHTML = `<span class="text-3xl font-black">${answer}</span>`;
                button.onclick = () => selectAnswer(i, button);
                answerOptionsDisplay.appendChild(button);
            });

            timerInterval = setInterval(updateTimer, 1000);
        }

        function selectAnswer(selectedIndex, clickedButton) {
            clearInterval(timerInterval); 

            const currentQuestion = quizData[currentQuestionIndex];
            const correctIndex = currentQuestion.correct;
            const buttons = answerOptionsDisplay.getElementsByTagName('button');

            // Nonaktifkan semua tombol agar tidak bisa diklik 2 kali
            for (let button of buttons) {
                button.disabled = true;
                button.classList.add('cursor-not-allowed', 'opacity-80');
            }

            let isCorrect = (selectedIndex === correctIndex);

            if (isCorrect) {
                clickedButton.classList.remove('bg-purple-900');
                clickedButton.classList.add('bg-green-600', 'shadow-inner');
                score++;
                showFeedback(true); // Panggil pop-up BENAR
            } else {
                clickedButton.classList.remove('bg-purple-900');
                clickedButton.classList.add('bg-red-600', 'shadow-inner');
                
                // Tunjukkan jawaban yang benar dengan warna hijau
                buttons[correctIndex].classList.remove('bg-purple-900');
                buttons[correctIndex].classList.add('bg-green-600', 'shadow-inner');
                showFeedback(false); // Panggil pop-up SALAH
            }

            // Tunggu 2.5 detik untuk melihat pop-up, lalu lanjut ke soal berikutnya
            setTimeout(() => {
                hideFeedback();
                setTimeout(nextQuestion, 300); // Lanjut setelah pop-up hilang
            }, 2500);
        }

        // --- FUNGSI MENGATUR POP-UP ---
        function showFeedback(isCorrect) {
            feedbackPopup.classList.remove('hidden'); // Munculkan overlay
            
            // Animasi zoom in
            setTimeout(() => {
                feedbackContent.classList.remove('scale-50', 'opacity-0');
                feedbackContent.classList.add('scale-100', 'opacity-100');
            }, 10);

            if (isCorrect) {
                feedbackPopup.classList.add('bg-green-500');
                feedbackPopup.classList.remove('bg-red-500');
                feedbackIcon.innerHTML = "🎉";
                feedbackText.textContent = "Benar!";
                feedbackText.className = "text-6xl font-black text-green-500 mb-2 tracking-tight";
                feedbackPoints.classList.remove('hidden'); // Munculkan poin
            } else {
                feedbackPopup.classList.add('bg-red-500');
                feedbackPopup.classList.remove('bg-green-500');
                feedbackIcon.innerHTML = "❌";
                feedbackText.textContent = "Salah!";
                feedbackText.className = "text-6xl font-black text-red-500 mb-2 tracking-tight";
                feedbackPoints.classList.add('hidden'); // Sembunyikan poin
            }
        }

        function hideFeedback() {
            // Animasi zoom out
            feedbackContent.classList.remove('scale-100', 'opacity-100');
            feedbackContent.classList.add('scale-50', 'opacity-0');
            
            // Sembunyikan overlay setelah animasi selesai
            setTimeout(() => {
                feedbackPopup.classList.add('hidden');
            }, 300);
        }
        // -----------------------------

        function nextQuestion() {
            currentQuestionIndex++;
            if (currentQuestionIndex < quizData.length) {
                quizContainer.classList.add('fade-in');
                loadQuestion(currentQuestionIndex);
                setTimeout(() => quizContainer.classList.remove('fade-in'), 500); 
            } else {
                finishQuiz();
            }
        }

        function updateTimer() {
            timeLeft--;
            updateTimerDisplay();
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                showFeedback(false); // Kalau waktu habis, anggap salah
                
                setTimeout(() => {
                    hideFeedback();
                    setTimeout(nextQuestion, 300);
                }, 2500);
            }
        }

        function updateTimerDisplay() {
            timerDisplay.textContent = timeLeft;
            if (timeLeft <= 5) {
                timerDisplay.classList.add('text-red-600', 'scale-110');
            } else {
                timerDisplay.classList.remove('text-red-600', 'scale-110');
            }
        }

        function finishQuiz() {
            clearInterval(timerInterval);
            quizContainer.innerHTML = `
                <div class="bg-white p-12 rounded-3xl shadow-2xl text-center w-full max-w-2xl fade-in">
                    <h1 class="text-5xl font-black text-[#a855f7] mb-6">Quiz Selesai!</h1>
                    <p class="text-3xl font-semibold mb-8 text-gray-600">Skor Akhir Kamu:</p>
                    <div class="text-9xl font-black text-gray-800 mb-10">${score} <span class="text-5xl text-gray-400">/ ${quizData.length}</span></div>
                    <a href="class.php" class="bg-purple-900 text-white px-10 py-4 rounded-full font-bold text-xl hover:bg-purple-800 transition shadow-lg">Kembali ke Kelas</a>
                </div>
            `;
        }

        // Mulai kuis
        loadQuestion(currentQuestionIndex);
    </script>
</body>
</html>