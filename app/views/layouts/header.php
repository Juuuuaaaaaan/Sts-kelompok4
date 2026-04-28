<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fun Streak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandPurple: '#b324f5', // Warna ungu dasar
                        brandPurpleDark: '#991bcf',
                        brandPurpleLight: '#c952f8'
                    },
                    fontFamily: {
                        sans: ['Nunito', 'sans-serif'], // Font melengkung sesuai gambar
                    }
                }
            }
        }
    </script>
    <style>
        /* Lengkungan background custom */
        .curved-bg {
            background: theme('colors.brandPurple');
            border-bottom-left-radius: 50% 20%;
            border-bottom-right-radius: 50% 20%;
        }
    </style>
</head>
<body class="bg-gray-50 overflow-x-hidden">