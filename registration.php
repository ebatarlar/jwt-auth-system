<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - JWT Auth System</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/registration.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center text-indigo-600 mb-8">Kayıt Ol</h1>
        
        <!-- Alert messages -->
        <div id="error-alert" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>
        <div id="success-alert" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"></div>
        
        <!-- Registration Form -->
        <form id="registration-form" class="space-y-4">
            <div>
                <label for="name" class="block text-gray-700 text-sm font-medium mb-1">Ad Soyad</label>
                <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            
            <div>
                <label for="email" class="block text-gray-700 text-sm font-medium mb-1">E-posta</label>
                <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            
            <div>
                <label for="password" class="block text-gray-700 text-sm font-medium mb-1">Şifre</label>
                <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required minlength="6">
                <p class="text-xs text-gray-500 mt-1">Şifreniz en az 6 karakter olmalıdır.</p>
            </div>
            
            <div>
                <label for="confirm-password" class="block text-gray-700 text-sm font-medium mb-1">Şifre Tekrar</label>
                <input type="password" id="confirm-password" name="confirm-password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            
            <div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Kayıt Ol</button>
            </div>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Zaten bir hesabınız var mı? <a href="index.php" class="text-indigo-600 hover:text-indigo-500">Giriş Yap</a></p>
        </div>
    </div>

</body>
</html>
