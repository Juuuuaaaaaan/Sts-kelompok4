<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kelas = $_POST['nama_kelas'];
    $deskripsi = $_POST['deskripsi'];
    $username = $_SESSION['username'];

    try {
        $pdo->beginTransaction();

        // 1. Simpan data kelas baru ke tabel classes
        $stmt = $pdo->prepare("INSERT INTO classes (nama_kelas, deskripsi, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$nama_kelas, $deskripsi, $username]);
        
        $class_id = $pdo->lastInsertId(); // Ambil ID kelas yang baru saja dibuat

        // 2. Simpan semua soal ke tabel questions
        $stmt_q = $pdo->prepare("INSERT INTO questions (class_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        // Loop untuk menyimpan setiap soal yang dikirim dari form
        foreach ($_POST['question'] as $index => $question) {
            $stmt_q->execute([
                $class_id,
                $question,
                $_POST['option_a'][$index],
                $_POST['option_b'][$index],
                $_POST['option_c'][$index],
                $_POST['option_d'][$index],
                $_POST['correct'][$index]
            ]);
        }

        $pdo->commit();
        header("Location: class.php?success=Class created");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Gagal menyimpan data: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Class - Fun Streak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-[#f9f9f9] min-h-screen p-8">

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800">Create New Class</h1>
            <a href="class.php" class="text-gray-500 hover:text-[#b829e3] font-bold">Cancel & Go Back</a>
        </div>

        <form action="" method="POST" class="space-y-6">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-4 text-[#b829e3]">1. Class Information</h2>
                <input type="text" name="nama_kelas" placeholder="Class Title (e.g. Basic Math)" required class="w-full bg-gray-50 border border-gray-200 p-4 rounded-xl mb-4 font-bold text-lg focus:outline-none focus:border-[#b829e3]">
                <textarea name="deskripsi" placeholder="Class Description..." required class="w-full bg-gray-50 border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-[#b829e3]"></textarea>
            </div>

            <div id="questions-container" class="space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 question-block">
                    <h2 class="text-xl font-bold mb-4">Question 1</h2>
                    <input type="text" name="question[]" placeholder="Type your question here..." required class="w-full bg-gray-50 border border-gray-200 p-4 rounded-xl mb-4 focus:outline-none focus:border-[#b829e3]">
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <input type="text" name="option_a[]" placeholder="Option A" required class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                        <input type="text" name="option_b[]" placeholder="Option B" required class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                        <input type="text" name="option_c[]" placeholder="Option C" required class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                        <input type="text" name="option_d[]" placeholder="Option D" required class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                    </div>

                    <label class="font-bold text-gray-600 block mb-2">Select Correct Answer:</label>
                    <select name="correct[]" class="bg-gray-50 border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3]">
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="button" onclick="addQuestion()" class="flex-1 bg-purple-100 text-[#b829e3] py-4 rounded-full font-bold hover:bg-purple-200 transition-colors">
                    + Add Another Question
                </button>
                <button type="submit" class="flex-1 bg-[#b829e3] text-white py-4 rounded-full font-bold hover:bg-gray-800 transition-colors shadow-lg shadow-purple-200">
                    Save Class & Publish 🚀
                </button>
            </div>
        </form>
    </div>

    <script>
        let questionCount = 1;
        function addQuestion() {
            questionCount++;
            const container = document.getElementById('questions-container');
            const newQuestion = `
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mt-6 question-block">
                    <h2 class="text-xl font-bold mb-4">Question ${questionCount}</h2>
                    <input type="text" name="question[]" placeholder="Type your question here..." required class="w-full bg-gray-50 border border-gray-200 p-4 rounded-xl mb-4 focus:outline-none focus:border-[#b829e3]">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <input type="text" name="option_a[]" placeholder="Option A" required class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                        <input type="text" name="option_b[]" placeholder="Option B" required class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                        <input type="text" name="option_c[]" placeholder="Option C" required class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                        <input type="text" name="option_d[]" placeholder="Option D" required class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                    </div>
                    <label class="font-bold text-gray-600 block mb-2">Select Correct Answer:</label>
                    <select name="correct[]" class="bg-gray-50 border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3]">
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newQuestion);
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
                        }, 400); // 400ms menyesuaikan animasi CSS
                    }
                });
            });
        });
    </script>
</body>
</html>