<?php
// Developed by Yisak A. Alemayehu (yisak.dev)
/**
 * Authopic Technologies PLC - Offline Page (PWA fallback)
 */
if (!defined('BASE_PATH')) exit;
$page_title = get_text('You\'re Offline', 'ከመስመር ውጭ ነዎት');
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?> - Authopic Technologies PLC</title>
    <link rel="stylesheet" href="/assets/css/tailwind.css">
    <style>
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .animate-float { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center">
    <div class="text-center px-4 max-w-md mx-auto">
        <div class="animate-float mb-8">
            <svg class="w-24 h-24 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M6 18L18 6"/>
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold mb-4">You're Offline</h1>
        <p class="text-lg text-slate-500 mb-8">It looks like you've lost your internet connection. Please check your network and try again.</p>
        <button onclick="window.location.reload()" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Try Again
        </button>
    </div>
</body>
</html>
