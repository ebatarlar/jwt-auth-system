<?php
/**
 * Login Page
 * 
 * Kullanıcı girişi sayfası
 * Giriş yapmış kullanıcılar doğrudan dashboard'a yönlendirilir
 */

// Oturum başlat
session_start();

// Oturum kontrolü - zaten giriş yapılmışsa dashboard'a yönlendir
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Hata mesajı için değişkenler
$error = '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - JWT Auth System</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/login.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center text-indigo-600 mb-6">Giriş Yap</h1>
        
        <!-- Error alert -->
        <div id="error-alert" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>
        
        <!-- Login Form -->
        <form id="login-form" class="space-y-4">
            <div>
                <label for="email" class="block text-gray-700 text-sm font-medium mb-1">E-posta</label>
                <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            
            <div>
                <label for="password" class="block text-gray-700 text-sm font-medium mb-1">Şifre</label>
                <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="remember-me" name="remember-me" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="remember-me" class="ml-2 block text-sm text-gray-700">Beni hatırla</label>
            </div>
            
            <div>
                <button id="login-button" type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Giriş Yap
                </button>
            </div>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Hesabınız yok mu? <a href="registration.php" class="text-indigo-600 hover:text-indigo-500">Kayıt Ol</a></p>
        </div>
        
        <div class="mt-6 pt-4 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-500">&copy; <?php echo date('Y'); ?> JWT Auth System</p>
        </div>
    </div>
</body>
</html>
