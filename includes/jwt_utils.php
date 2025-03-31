<?php
/**
 * JWT Utilities
 * 
 * Bu dosya JWT token oluşturma, doğrulama ve çözme işlemlerini içerir.
 * Firebase/PHP-JWT kütüphanesini kullanır.
 */

// Firebase JWT kütüphanesini yüklemek için Composer gereklidir
// composer require firebase/php-jwt

// JWT kütüphanesini ve çevre değişkenlerini dahil et
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/env.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtUtils {
    // JWT için gizli anahtar, token süresi ve yayıncı bilgileri
    private $secret_key;
    private $token_expire;
    private $token_issuer;
    
    /**
     * Constructor
     * Çevre değişkenlerinden JWT ayarlarını yükler
     */
    public function __construct() {
        $this->secret_key = env('JWT_SECRET', 'your_secret_key_change_this_in_production');
        $this->token_expire = (int)env('JWT_EXPIRE', 3600);
        $this->token_issuer = env('JWT_ISSUER', 'jwt_auth_system');
    }
    
    /**
     * Yeni bir JWT token oluşturur
     * 
     * @param array $data Token içine eklenecek veriler
     * @return string Oluşturulan JWT token
     */
    public function generateToken($data) {
        $issuedAt = time();
        $expire = $issuedAt + $this->token_expire;
        
        // Token payload
        $payload = [
            'iat' => $issuedAt,      // Token oluşturulma zamanı
            'exp' => $expire,        // Token son kullanma zamanı
            'iss' => $this->token_issuer, // Token yayıncısı
            'data' => $data          // Kullanıcı verileri
        ];
        
        // Token oluştur
        return JWT::encode($payload, $this->secret_key, 'HS256');
    }
    
    /**
     * JWT token'ı doğrular ve içeriğini döndürür
     * 
     * @param string $token Doğrulanacak JWT token
     * @return array|bool Başarılı ise token verilerini içeren dizi, başarısız ise false
     */
    public function validateToken($token) {
        try {
            // Token'ı çöz
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            
            // Çözülen token'ı diziye dönüştür
            $decoded_array = (array) $decoded;
            
            return $decoded_array;
        } catch (Exception $e) {
            // Token doğrulama hatası
            return false;
        }
    }
    
    /**
     * Token'dan kullanıcı verilerini alır
     * 
     * @param string $token JWT token
     * @return array|bool Başarılı ise kullanıcı verilerini içeren dizi, başarısız ise false
     */
    public function getDataFromToken($token) {
        $decoded = $this->validateToken($token);
        
        if ($decoded && isset($decoded['data'])) {
            return (array) $decoded['data'];
        }
        
        return false;
    }
    
    /**
     * Authorization header'dan Bearer token'ı alır
     * 
     * @return string|bool Başarılı ise token, başarısız ise false
     */
    public function getBearerToken() {
        $headers = null;
        
        // Authorization header'ı al
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        // Header yoksa false döndür
        if (!$headers) {
            return false;
        }
        
        // Bearer token'ı ayıkla
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
        
        return false;
    }
}
?>
