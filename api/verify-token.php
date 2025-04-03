<?php
/**
 * API Token Verification Endpoint
 * 
 * JWT token geçerliliğini doğrular
 * Authorization header'ında Bearer token bekler
 */

// CORS headers
header("Access-Control-Allow-Origin: http://localhost:8888"); // Allow only localhost
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Vary: Origin");

// OPTIONS request için CORS pre-flight response
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Sadece GET isteklerine izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "error" => "Sadece GET metodu desteklenmektedir."]);
    exit;
}

// Gerekli dosyaları dahil et
require_once __DIR__ . '/../includes/auth.php';

// Authorization header'ını kontrol et
$authHeader = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';

if (!$authHeader) {
    http_response_code(401); // Unauthorized
    echo json_encode(["success" => false, "error" => "Yetkilendirme başlığı (Authorization) eksik."]);
    exit;
}

// Bearer token'ı çıkar
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
} else {
    http_response_code(401); // Unauthorized
    echo json_encode(["success" => false, "error" => "Geçersiz yetkilendirme formatı. 'Bearer TOKEN' bekleniyordu."]);
    exit;
}

// Auth sınıfını başlat
$auth = new Auth();

// Token'ı doğrula
$result = $auth->verifyToken($token);

// Sonucu kontrol et
if ($result['success']) {
    // Token geçerli
    http_response_code(200); // OK
    echo json_encode([
        "success" => true,
        "message" => "Token geçerli",
        "data" => [
            "user_id" => $result['user_id']
        ]
    ]);
} else {
    // Token geçersiz veya süresi dolmuş
    http_response_code(401); // Unauthorized
    echo json_encode(["success" => false, "error" => $result['message']]);
}
?>
