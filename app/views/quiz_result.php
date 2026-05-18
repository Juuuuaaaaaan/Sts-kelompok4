<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #b829e3; }
        .slide-up { animation: slideUp 0.5s ease-out forwards; }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8 text-gray-800 flex flex-col items-center justify-center">

    <div class="w-full max-w-3xl bg-white rounded-[2rem] p-8 shadow-2xl slide-up border-4 border-white">
        <div class="text-center mb-10 border-b-4 border-gray-100 pb-8">
            <h2 class="text-2xl font-bold text-gray-400 mb-2">SKOR AKHIR</h2>
            <h1 class="text-7xl font-black text-[#b829e3] mb-4"><?= round($result['skor']) ?></h1>
            <div class="inline-block bg-[#f3d9fa] text-[#9b1ebf] px-6 py-2 rounded-full font-bold text-lg">
                Benar <?= $result['benar'] ?> dari <?= $result['total_soal'] ?> Soal
            </div>
        </div>

        <h3 class="text-xl font-bold text-gray-600 mb-4">Pembahasan Soal:</h3>
        <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-2">
            
            <?php foreach ($result['detail'] as $index => $d): ?>
                <div class="p-5 rounded-2xl border-4 <?= $d['is_correct'] ? 'border-green-400 bg-green-50' : 'border-red-400 bg-red-50' ?>">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 shrink-0 flex items-center justify-center rounded-full text-white font-bold <?= $d['is_correct'] ? 'bg-green-400' : 'bg-red-400' ?>">
                            <?= $d['is_correct'] ? '✓' : '✗' ?>
                        </div>
                        <div class="w-full">
                            <p class="font-bold text-lg text-gray-800 mb-3"><?= $index + 1 ?>. <?= htmlspecialchars($d['pertanyaan']) ?></p>
                            
                            <p class="text-sm font-semibold text-gray-500 mb-1">Jawabanmu:</p>
                            <p class="font-bold text-lg <?= $d['is_correct'] ? 'text-green-600' : 'text-red-600' ?> mb-2">
                                <?= htmlspecialchars(strtoupper($d['jawaban_siswa'])) ?: '(Kosong / Kehabisan Waktu)' ?>
                            </p>

                            <?php if (!$d['is_correct']): ?>
                                <div class="mt-3 pt-3 border-t-2 border-red-200">
                                    <p class="text-sm font-semibold text-gray-500 mb-1">Kunci Jawaban Benar:</p>
                                    <p class="font-bold text-green-600"><?= htmlspecialchars(strtoupper($d['kunci_jawaban'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="mt-10 text-center">
            <a href="/class" class="inline-block bg-[#b829e3] text-white font-black text-xl py-4 px-12 rounded-full shadow-[0_6px_0_#9b1ebf] hover:translate-y-1 hover:shadow-[0_2px_0_#9b1ebf] transition-all">
                Selesai & Kembali
            </a>
        </div>
    </div>

</body>
</html>