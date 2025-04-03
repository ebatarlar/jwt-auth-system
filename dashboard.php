<?php
/**
 * Dashboard Page
 * 
 * Kullanıcı profil bilgilerini görüntüler
 * Sadece giriş yapmış kullanıcılar erişebilir
 */

// Oturum başlat
session_start();

// Oturum kontrolü - giriş yapılmadıysa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JWT Auth System</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/dashboard.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-indigo-600 text-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="font-bold text-xl">JWT Auth System</div>
            <div class="flex items-center space-x-4">
                <span id="user-name-nav" class="hidden sm:inline-block">
                    <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>
                </span>
                <button id="logout-button" class="bg-indigo-700 hover:bg-indigo-800 px-4 py-2 rounded-md transition">
                    Çıkış Yap
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-indigo-600 text-white p-4 sm:p-6">
                <h1 class="text-2xl font-bold">Kullanıcı Bilgileri</h1>
                <p class="text-indigo-100">Profil ve API erişim bilgilerinizi görüntüleyin</p>
            </div>

            <!-- User Info Cards -->
            <div class="p-4 sm:p-6 grid gap-6 md:grid-cols-2">
                <!-- Session Info Card -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Oturum Bilgileri</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500 block">Kullanıcı ID:</span>
                            <span id="user-id" class="font-medium"><?php echo htmlspecialchars($_SESSION['user_id'] ?? 'N/A'); ?></span>
                        </div>
                        
                        <div>
                            <span class="text-sm text-gray-500 block">Ad Soyad:</span>
                            <span id="user-name" class="font-medium"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'N/A'); ?></span>
                        </div>
                        
                        <div>
                            <span class="text-sm text-gray-500 block">E-posta:</span>
                            <span id="user-email" class="font-medium"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'N/A'); ?></span>
                        </div>
                        
                        <div>
                            <span class="text-sm text-gray-500 block">Oturum Durumu:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                Aktif
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- API Integration Card -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">API Erişimi</h2>
                    
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600">
                            API uç noktalarına erişmek için JWT token kullanabilirsiniz. Profil bilgilerinizi API üzerinden almak için aşağıdaki butona tıklayın:
                        </p>
                        
                        <button id="fetch-profile" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition">
                            Profil Bilgilerini Al
                        </button>
                        
                        <div id="profile-status" class="hidden px-4 py-3 rounded text-yellow-700 bg-yellow-100 text-sm">
                            Profil bilgileri alınıyor, lütfen bekleyin...
                        </div>
                        
                        <!-- API Profile Data Display -->
                        <div id="api-profile-data" class="hidden mt-4 border border-indigo-200 rounded-lg overflow-hidden">
                            <div class="bg-indigo-50 px-4 py-2 border-b border-indigo-200">
                                <h3 class="font-medium text-indigo-700">API'den Alınan Profil Bilgileri</h3>
                            </div>
                            <div class="p-4 space-y-3">
                                <div>
                                    <span class="text-sm text-gray-500 block">Kullanıcı ID:</span>
                                    <span id="api-user-id" class="font-medium">-</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm text-gray-500 block">Ad Soyad:</span>
                                    <span id="api-user-name" class="font-medium">-</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm text-gray-500 block">E-posta:</span>
                                    <span id="api-user-email" class="font-medium">-</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm text-gray-500 block">Son Güncelleme:</span>
                                    <span id="api-last-updated" class="font-medium">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    &copy; <?php echo date('Y'); ?> JWT Auth System
                </p>
            </div>
        </div>
    </main>
</body>
</html>
