<?php
/**
 * API Profile Endpoint
 * 
 * Kullanıcı profil bilgilerini döndüren korumalı API endpoint
 * Authorization header'da geçerli bir JWT token gerektirir
 */

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// OPTIONS request için CORS pre-flight response
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Sadece GET isteklerine izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Sadece GET metodu desteklenmektedir."]);
    exit;
}

// Gerekli dosyaları dahil et
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/jwt_utils.php';

// JWT yardımcı sınıfını başlat
$jwt_utils = new JwtUtils();

// Bearer token'ı al
$token = $jwt_utils->getBearerToken();

if (!$token) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Bu endpoint için kimlik doğrulama gereklidir."]);
    exit;
}

// Token'ı doğrula
$decoded = $jwt_utils->validateToken($token);

if (!$decoded) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Geçersiz veya süresi dolmuş token."]);
    exit;
}

// Token'dan kullanıcı verilerini al
$user_data = $jwt_utils->getDataFromToken($token);

if (!$user_data || !isset($user_data['id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Token'da geçerli kullanıcı verisi bulunamadı."]);
    exit;
}

// Auth sınıfını başlat
$auth = new Auth();

// Kullanıcı bilgilerini veritabanından al
$user = $auth->getUserById($user_data['id']);

if (!$user) {
    http_response_code(404); // Not Found
    echo json_encode(["error" => "Kullanıcı bulunamadı."]);
    exit;
}

// Başarılı yanıt
http_response_code(200); // OK
echo json_encode([
    "message" => "Profil bilgileri başarıyla alındı.",
    "user" => $user
]);
?>
