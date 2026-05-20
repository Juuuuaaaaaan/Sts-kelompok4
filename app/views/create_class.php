<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Class - Fun Streak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }
        ::-webkit-scrollbar {
        display: none;
    }
    </style>
</head>
<body class="bg-[#f9f9f9] min-h-screen p-8">

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800">Create New Class</h1>
            <a href="/class" class="text-gray-500 hover:text-[#b829e3] font-bold">Cancel & Go Back</a>
        </div>

        <form action="/class" method="POST" class="space-y-6">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-4 text-[#b829e3]">1. Class Information</h2>
                <input type="text" name="nama_kelas" placeholder="Class Title (e.g. Basic Math)" required class="w-full bg-gray-50 border border-gray-200 p-4 rounded-xl mb-4 font-bold text-lg focus:outline-none focus:border-[#b829e3]">
                <textarea name="deskripsi" placeholder="Class Description..." required class="w-full bg-gray-50 border border-gray-200 p-4 rounded-xl focus:outline-none focus:border-[#b829e3]"></textarea>
            </div>

            <div id="questions-container" class="space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 question-block transition-all duration-300">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Question 1</h2>
                    </div>

                    <div class="mb-4">
                        <label class="font-bold text-gray-600 block mb-2">Question Type:</label>
                        <select name="type[]" onchange="toggleType(this)" class="bg-purple-50 text-[#b829e3] font-bold border border-purple-200 p-3 rounded-xl focus:outline-none w-full max-w-xs cursor-pointer">
                            <option value="pg">Multiple Choice (A, B, C, D)</option>
                            <option value="isian">Short Answer</option>
                        </select>
                    </div>
                    
                    <input type="text" name="question[]" placeholder="Type your question here..." required class="w-full bg-gray-50 border border-gray-200 p-4 rounded-xl mb-4 focus:outline-none focus:border-[#b829e3]">
                    
                    <input type="hidden" name="correct[]" class="real-correct-answer" value="A">
                    
                    <div class="pg-section">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <input type="text" name="option_a[]" placeholder="Opsi A" class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                            <input type="text" name="option_b[]" placeholder="Opsi B" class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                            <input type="text" name="option_c[]" placeholder="Opsi C" class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                            <input type="text" name="option_d[]" placeholder="Opsi D" class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                        </div>
                        <label class="font-bold text-gray-600 block mb-2">Select Correct Answer</label>
                        <select onchange="updateCorrectAnswer(this)" class="pg-select bg-gray-50 border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3] cursor-pointer">
                            <option value="A">Option A</option>
                            <option value="B">Option B</option>
                            <option value="C">Option C</option>
                            <option value="D">Option D</option>
                        </select>
                    </div>

                    <div class="isian-section" style="display:none;">
                        <label class="font-bold text-gray-600 block mb-2">Correct Answer Key:</label>
                        <input type="text" placeholder="Type the correct answer here..." onkeyup="updateCorrectAnswer(this)" onchange="updateCorrectAnswer(this)" class="isian-input w-full bg-green-50 border border-green-200 p-4 rounded-xl focus:outline-none focus:border-green-500 font-bold text-green-700">
                        <p class="text-sm text-gray-400 mt-2">*Students must type the answer exactly as shown to be considered correct.</p>
                    </div>
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
        function toggleType(selectElement) {
            const block = selectElement.closest('.question-block');
            const pgSection = block.querySelector('.pg-section');
            const isianSection = block.querySelector('.isian-section');
            const hiddenCorrect = block.querySelector('.real-correct-answer');

            if (selectElement.value === 'pg') {
                pgSection.style.display = 'block';
                isianSection.style.display = 'none';
                hiddenCorrect.value = pgSection.querySelector('.pg-select').value;
            } else {
                pgSection.style.display = 'none';
                isianSection.style.display = 'block';
                hiddenCorrect.value = isianSection.querySelector('.isian-input').value;
            }
        }

        function updateCorrectAnswer(inputElement) {
            const block = inputElement.closest('.question-block');
            const hiddenCorrect = block.querySelector('.real-correct-answer');
            hiddenCorrect.value = inputElement.value;
        }

        function updateQuestionNumbers() {
            const blocks = document.querySelectorAll('.question-block');
            blocks.forEach((block, index) => {
                const title = block.querySelector('h2');
                if (title) title.innerText = `Question ${index + 1}`;
            });
        }


        function addQuestion() {
            const container = document.getElementById('questions-container');
            const newQuestion = `
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mt-6 question-block transition-all duration-300">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Question</h2>
                        <button type="button" onclick="removeQuestion(this)" class="text-red-500 hover:text-red-700 font-bold bg-red-50 hover:bg-red-100 px-4 py-2 rounded-full transition-colors flex items-center gap-2">
                            <span>Remove Question</span> 🗑️
                        </button>
                    </div>

                    <div class="mb-4">
                        <label class="font-bold text-gray-600 block mb-2">Question Type:</label>
                        <select name="type[]" onchange="toggleType(this)" class="bg-purple-50 text-[#b829e3] font-bold border border-purple-200 p-3 rounded-xl focus:outline-none w-full max-w-xs cursor-pointer">
                            <option value="pg">Multiple Choice (A, B, C, D)</option>
                            <option value="isian">Short Answer</option>
                        </select>
                    </div>
                    
                    <input type="text" name="question[]" placeholder="Type your question here..." required class="w-full bg-gray-50 border border-gray-200 p-4 rounded-xl mb-4 focus:outline-none focus:border-[#b829e3]">
                    
                    <input type="hidden" name="correct[]" class="real-correct-answer" value="A">
                    
                    <div class="pg-section">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <input type="text" name="option_a[]" placeholder="Option A" class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                            <input type="text" name="option_b[]" placeholder="Option B" class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                            <input type="text" name="option_c[]" placeholder="Option C" class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                            <input type="text" name="option_d[]" placeholder="Option D" class="bg-gray-50 border border-gray-200 p-3 rounded-xl focus:outline-none focus:border-[#b829e3]">
                        </div>
                        <label class="font-bold text-gray-600 block mb-2">Pilih Jawaban Benar:</label>
                        <select onchange="updateCorrectAnswer(this)" class="pg-select bg-gray-50 border border-gray-200 p-3 rounded-xl w-full focus:outline-none focus:border-[#b829e3] cursor-pointer">
                            <option value="A">Opsi A</option>
                            <option value="B">Opsi B</option>
                            <option value="C">Opsi C</option>
                            <option value="D">Opsi D</option>
                        </select>
                    </div>

                    <div class="isian-section" style="display:none;">
                        <label class="font-bold text-gray-600 block mb-2">Correct Answer Key:</label>
                        <input type="text" placeholder="Type the correct answer here..." onkeyup="updateCorrectAnswer(this)" onchange="updateCorrectAnswer(this)" class="isian-input w-full bg-green-50 border border-green-200 p-4 rounded-xl focus:outline-none focus:border-green-500 font-bold text-green-700">
                        <p class="text-sm text-gray-400 mt-2">*Students must type the answer exactly as shown to be considered correct.</p>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newQuestion);
            updateQuestionNumbers();
        }

        function removeQuestion(button) {
            const questionBlock = button.closest('.question-block');
            if (questionBlock) {
                questionBlock.remove();
                updateQuestionNumbers();
            }
        }
    </script>
</body>
</html>