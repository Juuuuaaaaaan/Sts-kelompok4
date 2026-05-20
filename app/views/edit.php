<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class - Fun Streak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-[#f9f9f9] min-h-screen p-8">

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800">Edit Class & Questions</h1>
            <a href="/class" class="text-gray-500 hover:text-[#b829e3] font-bold">Cancel & Go Back</a>
        </div>

 <form action="/class/update" method="POST" class="space-y-6">
    <input type="hidden" name="class_id" value="<?= $class['id'] ?>">

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-4 text-[#b829e3]">1. Class Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="font-bold text-gray-600 block mb-2">Class Name:</label>
                        <input type="text" name="nama_kelas" value="<?= htmlspecialchars($class['nama_kelas'] ?? '') ?>" required class="w-full bg-gray-50 border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-[#b829e3]">
                    </div>
                    <div>
                        <label class="font-bold text-gray-600 block mb-2">Description:</label>
                        <textarea name="deskripsi" rows="3" required class="w-full bg-gray-50 border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-[#b829e3]"><?= htmlspecialchars($class['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-[#b829e3]">2. Quiz Questions</h2>
                    <button type="button" onclick="addQuestion()" class="bg-[#b829e3] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#a221c9] transition-all shadow-sm">+ Add More Question</button>
                </div>

                <div id="questions-container" class="space-y-6">
                    <?php if (!empty($questions)): ?>
                        <?php foreach ($questions as $index => $q): ?>
                            <div class="question-block bg-gray-50 p-6 rounded-2xl border border-gray-200 relative">
                                <button type="button" onclick="removeQuestion(this)" class="absolute top-4 right-4 text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-all">Remove</button>
                                <p class="font-extrabold text-gray-700 mb-4 question-number">Question #<?= $index + 1 ?></p>
                                
                                <div class="mb-4">
                                    <label class="font-bold text-gray-600 block mb-2">Question Text:</label>
                                    <input type="text" name="question[]" value="<?= htmlspecialchars($q['question_text'] ?? '') ?>" required class="w-full bg-white border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                                </div>

                                <div class="mb-4">
                                    <label class="font-bold text-gray-600 block mb-2">Question Type:</label>
                                    <select name="type[]" onchange="toggleQuestionType(this)" class="type-select bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3] font-medium cursor-pointer">
                                        <option value="pg" <?= ($q['question_type'] ?? '') === 'pg' ? 'selected' : '' ?>>Pilihan Ganda (Multiple Choice)</option>
                                        <option value="isian" <?= ($q['question_type'] ?? '') === 'isian' ? 'selected' : '' ?>>Isian Singkat (Text Fill-in)</option>
                                    </select>
                                </div>

                                <div class="pg-section space-y-3 mb-4" style="<?= ($q['question_type'] ?? '') === 'isian' ? 'display:none;' : '' ?>">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <input type="text" name="option_a[]" value="<?= htmlspecialchars($q['option_a'] ?? '') ?>" placeholder="Option A" class="bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3]">
                                        <input type="text" name="option_b[]" value="<?= htmlspecialchars($q['option_b'] ?? '') ?>" placeholder="Option B" class="bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3]">
                                        <input type="text" name="option_c[]" value="<?= htmlspecialchars($q['option_c'] ?? '') ?>" placeholder="Option C" class="bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3]">
                                        <input type="text" name="option_d[]" value="<?= htmlspecialchars($q['option_d'] ?? '') ?>" placeholder="Option D" class="bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3]">
                                    </div>
                                    <label class="font-bold text-gray-600 block mb-2 mt-3">Correct Option Key:</label>
                                    <select onchange="updateCorrectAnswer(this)" class="correct-pg-select bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3] cursor-pointer">
                                        <option value="A" <?= ($q['correct_option'] ?? '') === 'A' ? 'selected' : '' ?>>Opsi A</option>
                                        <option value="B" <?= ($q['correct_option'] ?? '') === 'B' ? 'selected' : '' ?>>Opsi B</option>
                                        <option value="C" <?= ($q['correct_option'] ?? '') === 'C' ? 'selected' : '' ?>>Opsi C</option>
                                        <option value="D" <?= ($q['correct_option'] ?? '') === 'D' ? 'selected' : '' ?>>Opsi D</option>
                                    </select>
                                </div>

                                <div class="isian-section mb-4" style="<?= ($q['question_type'] ?? '') !== 'isian' ? 'display:none;' : '' ?>">
                                    <label class="font-bold text-gray-600 block mb-2">Kunci Jawaban Benar:</label>
                                    <input type="text" value="<?= htmlspecialchars($q['correct_option'] ?? '') ?>" placeholder="Ketik jawaban benar di sini..." onkeyup="updateCorrectAnswer(this)" class="isian-input w-full bg-green-50 border border-green-200 p-4 rounded-xl focus:outline-none focus:border-green-500 font-bold text-green-700">
                                </div>

                                <input type="hidden" name="correct[]" value="<?= htmlspecialchars($q['correct_option'] ?? 'A') ?>" class="final-correct-option">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-400 text-center py-4 font-medium" id="empty-msg">Belum ada pertanyaan di kelas ini. Klik tombol tambah di atas.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-gradient-to-r from-[#b829e3] to-[#c946f2] text-white font-black text-xl py-4 px-12 rounded-2xl shadow-lg hover:opacity-90 transition-all transform active:scale-95">Update Class & Save</button>
            </div>
        </form>
    </div>

    <script>
        function toggleQuestionType(select) {
            const block = select.closest('.question-block');
            const pgSection = block.querySelector('.pg-section');
            const isianSection = block.querySelector('.isian-section');
            const finalInput = block.querySelector('.final-correct-option');

            if (select.value === 'isian') {
                pgSection.style.display = 'none';
                isianSection.style.display = 'block';
                finalInput.value = block.querySelector('.isian-input').value;
            } else {
                pgSection.style.display = 'block';
                isianSection.style.display = 'none';
                finalInput.value = block.querySelector('.correct-pg-select').value;
            }
        }

        function updateCorrectAnswer(element) {
            const block = element.closest('.question-block');
            const finalInput = block.querySelector('.final-correct-option');
            finalInput.value = element.value;
        }

        function updateQuestionNumbers() {
            const blocks = document.querySelectorAll('.question-block');
            blocks.forEach((block, idx) => {
                block.querySelector('.question-number').innerText = `Question #${idx + 1}`;
            });
        }

        function addQuestion() {
            const emptyMsg = document.getElementById('empty-msg');
            if(emptyMsg) emptyMsg.remove();

            const container = document.getElementById('questions-container');
            const nextIdx = document.querySelectorAll('.question-block').length + 1;
            
            const newHtml = `
                <div class="question-block bg-gray-50 p-6 rounded-2xl border border-gray-200 relative">
                    <button type="button" onclick="removeQuestion(this)" class="absolute top-4 right-4 text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-all">Remove</button>
                    <p class="font-extrabold text-gray-700 mb-4 question-number">Question #${nextIdx}</p>
                    
                    <div class="mb-4">
                        <label class="font-bold text-gray-600 block mb-2">Question Text:</label>
                        <input type="text" name="question[]" required class="w-full bg-white border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                    </div>

                    <div class="mb-4">
                        <label class="font-bold text-gray-600 block mb-2">Question Type:</label>
                        <select name="type[]" onchange="toggleQuestionType(this)" class="type-select bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3] font-medium cursor-pointer">
                            <option value="pg">Pilihan Ganda (Multiple Choice)</option>
                            <option value="isian">Isian Singkat (Text Fill-in)</option>
                        </select>
                    </div>

                    <div class="pg-section space-y-3 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="option_a[]" placeholder="Option A" class="bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3]">
                            <input type="text" name="option_b[]" placeholder="Option B" class="bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3]">
                            <input type="text" name="option_c[]" placeholder="Option C" class="bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3]">
                            <input type="text" name="option_d[]" placeholder="Option D" class="bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3]">
                        </div>
                        <label class="font-bold text-gray-600 block mb-2 mt-3">Correct Option Key:</label>
                        <select onchange="updateCorrectAnswer(this)" class="correct-pg-select bg-white border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3] cursor-pointer">
                            <option value="A">Opsi A</option>
                            <option value="B">Opsi B</option>
                            <option value="C">Opsi C</option>
                            <option value="D">Opsi D</option>
                        </select>
                    </div>

                    <div class="isian-section mb-4" style="display:none;">
                        <label class="font-bold text-gray-600 block mb-2">Kunci Jawaban Benar:</label>
                        <input type="text" placeholder="Ketik jawaban benar di sini..." onkeyup="updateCorrectAnswer(this)" class="isian-input w-full bg-green-50 border border-green-200 p-4 rounded-xl focus:outline-none focus:border-green-500 font-bold text-green-700">
                    </div>

                    <input type="hidden" name="correct[]" value="A" class="final-correct-option">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newHtml);
            updateQuestionNumbers();
        }

        function removeQuestion(button) {
            button.closest('.question-block').remove();
            updateQuestionNumbers();
        }
    </script>
</body>
</html>