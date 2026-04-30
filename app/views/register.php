<?php 
require_once '../db.php';
require_once 'layouts/header.php'; 
?>
<body class="flex h-screen overflow-hidden bg-[#f9f9f9]">
    <div class="w-[45%] bg-[#b829e3] flex flex-col justify-center items-end pr-40 relative z-20 shadow-[10px_0_30px_rgba(184,41,227,0.05)]" style="border-top-right-radius: 50% 50%; border-bottom-right-radius: 50% 50%;">
        
        <button onclick="if(document.referrer) { window.history.back(); } else { window.location.href = '../../index.php'; }" class="absolute top-10 left-10 text-white font-semibold text-xl flex items-center gap-2 hover:-translate-x-2 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </button>

        <div class="text-right text-white cursor-default hover:scale-105 transition-transform duration-500 relative z-10">
            <h1 class="text-[6rem] font-extrabold leading-none mb-4 tracking-tight">Fun<br>Streak</h1>
            <p class="text-4xl text-purple-200 font-medium">Hard work by<br>yourself</p>
        </div>
    </div>

    <div class="flex-1 flex justify-center items-center relative z-10 pl-4">
        <div class="bg-white p-10 rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] w-[480px] hover:shadow-[0_15px_50px_rgba(184,41,227,0.15)] transition-shadow duration-500">
            <div class="text-center mb-8">
                <h2 class="text-5xl font-bold text-[#b829e3]">Welcome</h2>
                <p class="text-gray-600 mt-2 font-semibold text-lg inline-block border-b-2 border-blue-500 pb-1">Please enter your details to enter</p>
            </div>
            
            <?php if(isset($_GET['error'])): ?>
                <p class="text-red-500 text-sm text-center mb-4 bg-red-50 py-2 rounded-lg font-medium"><?= htmlspecialchars($_GET['error']) ?></p>
            <?php endif; ?>

            <form action="../controllers/logincontroller.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="register">
                <div class="group">
                    <label class="block text-gray-400 text-sm mb-1 ml-2 font-medium group-focus-within:text-[#b829e3] transition-colors duration-300">Username</label>
                    <input type="text" name="username" placeholder="Input your Username" required class="w-full bg-gray-100/80 rounded-full px-5 py-3 outline-none focus:bg-white focus:ring-4 focus:ring-purple-200 focus:border-[#b829e3] border border-transparent transition-all duration-300 shadow-sm">
                </div>
                <div class="group">
                    <label class="block text-gray-400 text-sm mb-1 ml-2 font-medium group-focus-within:text-[#b829e3] transition-colors duration-300">Email Address</label>
                    <input type="email" name="email" placeholder="Input your E-mail address" required class="w-full bg-gray-100/80 rounded-full px-5 py-3 outline-none focus:bg-white focus:ring-4 focus:ring-purple-200 focus:border-[#b829e3] border border-transparent transition-all duration-300 shadow-sm">
                </div>
                <div class="group">
                    <label class="block text-gray-400 text-sm mb-1 ml-2 font-medium group-focus-within:text-[#b829e3] transition-colors duration-300">Password</label>
                    <input type="password" name="password" placeholder="Input your Password" required class="w-full bg-gray-100/80 rounded-full px-5 py-3 outline-none focus:bg-white focus:ring-4 focus:ring-purple-200 focus:border-[#b829e3] border border-transparent transition-all duration-300 shadow-sm">
                </div>
                <div class="group">
                    <label class="block text-gray-400 text-sm mb-1 ml-2 font-medium group-focus-within:text-[#b829e3] transition-colors duration-300">Confirm Password</label>
                    <input type="password" name="confirm" placeholder="Input your Confirm Password" required class="w-full bg-gray-100/80 rounded-full px-5 py-3 outline-none focus:bg-white focus:ring-4 focus:ring-purple-200 focus:border-[#b829e3] border border-transparent transition-all duration-300 shadow-sm">
                </div>
                <button type="submit" class="w-full bg-[#b829e3] text-white text-lg font-bold py-3 rounded-full mt-4 shadow-md hover:bg-[#a01ebd] hover:shadow-[0_8px_20px_rgba(184,41,227,0.4)] hover:-translate-y-1 transition-all duration-300">Register</button>
            </form>
            <p class="text-center mt-6 text-gray-400 font-medium">Have an account? <a href="login.php" class="text-gray-600 hover:text-[#b829e3] underline transition-colors duration-300">Login</a></p>
        </div>
    </div>
</body>
</html>