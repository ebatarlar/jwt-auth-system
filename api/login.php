<?php
/**
 * API Login Endpoint
 * 
 * JWT tabanlı kimlik doğrulama için API endpoint
 * POST metodu ile e-posta ve şifre alır, başarılı ise JWT token döndürür
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

// Gerekli alanları kontrol et
if (!isset($data['email']) || !isset($data['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "error" => "E-posta ve şifre gereklidir."]);
    exit;
}

// Auth sınıfını başlat
$auth = new Auth();

// Giriş işlemini gerçekleştir
$result = $auth->login($data['email'], $data['password']);

// Sonucu kontrol et
if ($result['success']) {
    // Başarılı giriş
    http_response_code(200); // OK
    
    // Yanıt verisini hazırla
    $response = [
        "success" => true,
        "message" => $result['message'],
        "data" => [
            "token" => $result['token'],
            "user" => $result['user']
        ]
    ];
    
    // Remember Me özelliği için (web arayüzü bu bilgiyi kullanabilir)
    if (isset($data['remember_me']) && $data['remember_me']) {
        $response["data"]['remember_me'] = true;
    }
    
    echo json_encode($response);
} else {
    // Başarısız giriş
    http_response_code(401); // Unauthorized
    echo json_encode(["success" => false, "error" => $result['message']]);
}
?>
