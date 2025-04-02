<?php
/**
 * Logout Page
 * 
 * Kullanıcı oturumunu sonlandırır ve giriş sayfasına yönlendirir
 * Hem oturum çerezlerini hem de JWT token'ı temizler
 */

// Auth sınıfını dahil et
require_once __DIR__ . '/includes/auth.php';

// Auth sınıfını başlat
$auth = new Auth();

// Logout metodunu çağır - bu metod içinde:
// - Oturum değişkenlerini temizler
// - Oturum çerezini siler
// - Oturumu sonlandırır
$auth->logout();

// Remember me çerezini temizle
setcookie('remember_me', '', time() - 3600, '/');

// Token ve kullanıcı verilerini localStorage'dan temizlemek için JavaScript ile yönlendir
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çıkış Yapılıyor... - JWT Auth System</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto bg-white p-8 rounded-lg shadow-md text-center">
        <h1 class="text-2xl font-bold text-indigo-600 mb-4">Çıkış Yapılıyor</h1>
        <p class="text-gray-600 mb-8">Oturumunuz güvenli bir şekilde sonlandırılıyor...</p>
        <div class="animate-spin mx-auto h-10 w-10 border-4 border-indigo-500 rounded-full border-t-transparent"></div>
    </div>
    
    <script>
    $(document).ready(function() {
        // LocalStorage'dan token ve kullanıcı verilerini temizle
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('user_data');
        
        // Kısa bir gecikme sonrasında ana sayfaya yönlendir
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 1500); // 1.5 saniye bekleyip yönlendir
    });
    </script>
</body>
</html>
