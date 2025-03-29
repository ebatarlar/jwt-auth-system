<?php
/**
 * Veritabanı bağlantı ayarları
 * JWT Auth System için veritabanı bağlantı sınıfı
 */

class Database {
    // Veritabanı bağlantı bilgileri
    private $host = 'localhost';
    private $db_name = 'jwt_auth';
    private $username = 'root';
    private $password = 'root'; // MAMP için varsayılan şifre 'root'
    private $conn;

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
