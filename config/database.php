<?php
/**
 * Veritabanı bağlantı ayarları
 * JWT Auth System için veritabanı bağlantı sınıfı
 */

// Çevre değişkenlerini yükle
require_once __DIR__ . '/env.php';

class Database {
    // Veritabanı bağlantı bilgileri
    private $host      = null;
    private $db_name   = null;
    private $username  = null;
    private $password  = null;
    private $conn;
    
    /**
     * Constructor
     * Çevre değişkenlerinden veritabanı bağlantı bilgilerini yükler
     */
    public function __construct() {
        $this->host     = env('DB_HOST', 'localhost');
        $this->db_name  = env('DB_NAME', 'jwt_auth');
        $this->username = env('DB_USER', 'root');
        $this->password = env('DB_PASS', 'root');
    }

    /**
     * Veritabanı bağlantısını oluşturur
     * @return PDO bağlantı nesnesi
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Veritabanı bağlantı hatası: " . $e->getMessage();
            //In production must use some type of logging
        }

        return $this->conn;
    }
    

}
?>
