<?php
session_start();
if (!isset($_SESSION['terakhir_skor'])) {
    header("Location: class.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result - Fun Streak</title>
    <link rel="stylesheet" href="../../public/css/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#f9f9f9] min-h-screen flex items-center justify-center font-['Outfit']">

    <div class="bg-white p-10 rounded-3xl shadow-lg text-center max-w-md w-full">
        <h1 class="text-2xl font-bold mb-4">Quiz Finished!</h1>
        
        <div class="text-6xl font-black text-[#b829e3] mb-4">
            <?= $_SESSION['terakhir_skor'] ?>
        </div>
        
        <p class="text-xl font-semibold mb-6">
            Status: 
            <span class="<?= $_SESSION['terakhir_status'] == 'Lulus' ? 'text-green-500' : 'text-red-500' ?>">
                <?= $_SESSION['terakhir_status'] ?>
            </span>
        </p>

        <a href="streak.php" class="block bg-[#b829e3] text-white py-3 rounded-full font-bold hover:bg-[#a01ebd] transition-all">Check My Streak</a>
        <a href="class.php" class="block mt-4 text-gray-500 font-medium hover:underline">Back to Class</a>
    </div>

</body>
</html>