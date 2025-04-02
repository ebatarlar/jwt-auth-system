<?php
/**
 * API Registration Endpoint
 * 
 * Kullanıcı kaydı için API endpoint
 * POST metodu ile kullanıcı bilgilerini alır, başarılı ise kullanıcı bilgilerini ve JWT token döndürür
 */

// CORS headers
header("Access-Control-Allow-Origin: http://localhost:8888"); // Allow only localhost:8000
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true"); // Often needed when restricting origin
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Vary: Origin"); // Add Vary header

// OPTIONS request için CORS pre-flight response
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Sadece POST isteklerine izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "error" => "Sadece POST metodu desteklenmektedir."]);
    exit;
}

// Gerekli dosyaları dahil et
require_once __DIR__ . '/../includes/auth.php';

// JSON verisini al
$data = json_decode(file_get_contents("php://input"), true);

// Giriş doğrulama
$errors = [];

// Gerekli alanları kontrol et
if (!isset($data['name']) || empty(trim($data['name']))) {
    $errors[] = "İsim alanı gereklidir.";
}

if (!isset($data['email']) || empty(trim($data['email']))) {
    $errors[] = "E-posta alanı gereklidir.";
} elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Geçerli bir e-posta adresi giriniz.";
}

if (!isset($data['password']) || empty($data['password'])) {
    $errors[] = "Şifre alanı gereklidir.";
} elseif (strlen($data['password']) < 6) {
    $errors[] = "Şifre en az 6 karakter olmalıdır.";
}

// Hata varsa yanıt döndür
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "error" => implode("\n", $errors)]);
    exit;
}

// Auth sınıfını başlat
$auth = new Auth();

// Kayıt işlemini gerçekleştir
$result = $auth->register($data['name'], $data['email'], $data['password']);

// Sonucu kontrol et
if ($result['success']) {
    // Başarılı kayıt
    http_response_code(201); // Created
    
    // Otomatik giriş yap
    $login_result = $auth->login($data['email'], $data['password']);
    
    if ($login_result['success']) {
        // Yanıt verisini hazırla
        $response = [
            "success" => true,
            "message" => "Kayıt başarılı ve otomatik giriş yapıldı.",
            "data" => [
                "token" => $login_result['token'],
                "user" => $login_result['user']
            ]
        ];
        
        echo json_encode($response);
    } else {
        // Kayıt başarılı ama giriş başarısız
        echo json_encode([
            "success" => true,
            "message" => $result['message'],
            "data" => [
                 "user_id" => $result['user_id']
            ]
        ]);
    }
} else {
    // Başarısız kayıt
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "error" => $result['message']]);
}
?>
