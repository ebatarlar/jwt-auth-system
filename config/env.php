<?php
/**
 * Environment Configuration
 * 
 * Bu dosya .env dosyasından çevre değişkenlerini yükler
 */

// Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Dotenv kütüphanesini yükle
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Gerekli çevre değişkenlerini kontrol et
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'JWT_SECRET']);

/**
 * Çevre değişkenini güvenli bir şekilde alır
 * 
 * @param string $key Çevre değişkeni anahtarı
 * @param mixed $default Değişken bulunamazsa kullanılacak varsayılan değer
 * @return mixed Çevre değişkeni değeri veya varsayılan değer
 */
function env($key, $default = null) {
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}
?>
