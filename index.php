<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fun Streak</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../public/css/output.css">
</head>
<body style="overflow:hidden;">

    <!-- Navbar -->
    <header>
        <div class="logo">Fun Streak</div>

        <nav>
            <a href="#" class="active">Home</a>
            <a href="app/views/class.php">Class</a>
            <a href="#">Streak</a>
        </nav>

        <div class="auth-btn">
            <a href="/app/views/register.php">
                <button class="btn primary">Register</button>
            </a>
            <a href="/app/views/login.php">
                <button class="btn">Log In</button>
            </a>
        </div>
    </header>

    <!-- Hero -->
    <section class="hero">
        <h1>Fun Streak</h1>
        <p>hard work by yourself</p>
    </section>

    <!-- Bottom -->
    <section class="bottom">
        <button class="start-btn" onclick="startNow()">Start Now</button>
    </section>

    <!-- JS -->
    <script src="../public/js/js.js"></script>
</body>
</html>
