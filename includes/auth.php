<?php
/**
 * Authentication Utilities
 * 
 * Bu dosya kullanıcı kimlik doğrulama işlemlerini içerir:
 * - Kullanıcı kaydı
 * - Giriş işlemi
 * - Şifre doğrulama
 * - Oturum yönetimi
 */

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/jwt_utils.php';

class Auth {
    // Veritabanı bağlantısı
    private $db;
    
    // JWT yardımcı sınıfı
    private $jwt_utils;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Veritabanı bağlantısını oluştur
        $database = new Database();
        $this->db = $database->getConnection();
        
        // JWT yardımcı sınıfını başlat
        $this->jwt_utils = new JwtUtils();
    }
    
    /**
     * Yeni kullanıcı kaydı oluşturur
     * 
     * @param string $name Kullanıcı adı
     * @param string $email E-posta adresi
     * @param string $password Şifre (açık metin)
     * @return array Kayıt sonucu (success, message, user_id)
     */
    public function register($name, $email, $password) {
        try {
            // E-posta adresinin geçerli olup olmadığını kontrol et
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'message' => 'Geçersiz e-posta adresi.'
                ];
            }
            
            // E-posta adresinin daha önce kullanılıp kullanılmadığını kontrol et
            $check_query = "SELECT id FROM users WHERE email = :email";
            $check_stmt = $this->db->prepare($check_query);
            $check_stmt->bindParam(':email', $email);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'Bu e-posta adresi zaten kullanılıyor.'
                ];
            }
            
            // Şifreyi güvenli bir şekilde hash'le
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Kullanıcıyı veritabanına ekle
            $query = "INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)";
            $stmt = $this->db->prepare($query);
            
            // Parametreleri bağla
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $password_hash);
            
            // Sorguyu çalıştır
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Kayıt başarıyla tamamlandı.',
                    'user_id' => $this->db->lastInsertId()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Kayıt sırasında bir hata oluştu.'
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Veritabanı hatası: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Kullanıcı girişi yapar
     * 
     * @param string $email E-posta adresi
     * @param string $password Şifre (açık metin)
     * @return array Giriş sonucu (success, message, user, token)
     */
    public function login($email, $password) {
        try {
            // Kullanıcıyı e-posta adresine göre bul
            $query = "SELECT id, name, email, password_hash FROM users WHERE email = :email";
            $stmt  = $this->db->prepare($query);
            
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            // Kullanıcı bulunamadıysa
            if ($stmt->rowCount() == 0) {
                return [
                    'success' => false,
                    'message' => 'Geçersiz e-posta adresi veya şifre.'
                ];
            }
            
            // Kullanıcı bilgilerini al
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Şifreyi doğrula
            if (!password_verify($password, $user['password_hash'])) {
                return [
                    'success' => false,
                    'message' => 'Geçersiz e-posta adresi veya şifre.'
                ];
            }
            
            // Kullanıcı bilgilerinden şifre hash'ini çıkar
            unset($user['password_hash']);
            
            // JWT token oluştur
            $token = $this->jwt_utils->generateToken([
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email']
            ]);
            
            // Oturum başlat
            $this->startSession($user);
            
            return [
                'success' => true,
                'message' => 'Giriş başarılı.',
                'user'    => $user,
                'token'   => $token
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Veritabanı hatası: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Kullanıcı oturumunu başlatır
     * 
     * @param array $user Kullanıcı bilgileri
     */
    private function startSession($user) {
        // Oturum başlatılmamışsa başlat
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kullanıcı bilgilerini oturuma kaydet
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['user_name']     = $user['name'];
        $_SESSION['user_email']    = $user['email'];
        $_SESSION['logged_in']     = true;
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Kullanıcı oturumunu sonlandırır
     */
    public function logout() {
        // Oturum başlatılmamışsa başlat
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Oturum değişkenlerini temizle
        $_SESSION = [];
        
        // Oturum çerezini sil
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Oturumu sonlandır
        session_destroy();
    }
    
    /**
     * Kullanıcının giriş yapmış olup olmadığını kontrol eder
     * 
     * @return bool Kullanıcı giriş yapmışsa true, yapmamışsa false
     */
    public function isLoggedIn() {
        // Oturum başlatılmamışsa başlat
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Oturum kontrolü
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Giriş yapmış kullanıcının bilgilerini döndürür
     * 
     * @return array|null Kullanıcı giriş yapmışsa kullanıcı bilgileri, yapmamışsa null
     */
    public function getCurrentUser() {
        // Oturum başlatılmamışsa başlat
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kullanıcı giriş yapmamışsa null döndür
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        // Kullanıcı bilgilerini döndür
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email']
        ];
    }
    
    /**
     * Kullanıcı ID'sine göre kullanıcı bilgilerini getirir
     * 
     * @param int $user_id Kullanıcı ID
     * @return array|null Kullanıcı bulunursa bilgileri, bulunamazsa null
     */
    public function getUserById($user_id) {
        try {
            // Kullanıcıyı ID'ye göre bul
            $query = "SELECT id, name, email, created_at FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            // Kullanıcı bulunamadıysa null döndür
            if ($stmt->rowCount() == 0) {
                return null;
            }
            
            // Kullanıcı bilgilerini döndür
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * JWT token'ından kullanıcı bilgilerini getirir
     * 
     * @param string $token JWT token
     * @return array|null Token geçerliyse kullanıcı bilgileri, geçersizse null
     */
    public function getUserFromToken($token) {
        // Token'ı doğrula
        $decoded = $this->jwt_utils->validateToken($token);
        
        // Token geçersizse null döndür
        if (!$decoded) {
            return null;
        }
        
        // Kullanıcı bilgilerini döndür
        return isset($decoded['data']) ? $decoded['data'] : null;
    }
    
    /**
     * JWT token'ın geçerliliğini doğrular
     * 
     * @param string $token JWT token
     * @return array Doğrulama sonucu (success, message, user_id varsa)
     */
    public function verifyToken($token) {
        try {
            // Token'ı doğrula
            $decoded = $this->jwt_utils->validateToken($token);
            
            // Token geçersizse hata döndür
            if (!$decoded) {
                return [
                    'success' => false,
                    'message' => 'Geçersiz veya süresi dolmuş token.'
                ];
            }
            
            // Token geçerli, kullanıcı ID'sini döndür
            if (isset($decoded['data']) && isset($decoded['data']['id'])) {
                return [
                    'success' => true,
                    'message' => 'Token geçerli.',
                    'user_id' => $decoded['data']['id']
                ];
            }
            
            // Token yapısı doğru değil
            return [
                'success' => false,
                'message' => 'Token yapısı geçersiz.'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Token doğrulama hatası: ' . $e->getMessage()
            ];
        }
    }
}
?>
