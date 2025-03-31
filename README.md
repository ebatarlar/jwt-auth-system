# JWT ile Güvenli Giriş Sistemi

Bu proje, PHP ve MySQL kullanarak oturum tabanlı giriş sistemi ve JWT (JSON Web Token) tabanlı API erişimi sağlayan güvenli bir kullanıcı kimlik doğrulama sistemidir.

## Özellikler

- Kullanıcı kaydı ve girişi
- Şifrelerin güvenli bir şekilde hash'lenmesi
- Oturum tabanlı kimlik doğrulama (web arayüzü için)
- JWT tabanlı kimlik doğrulama (API erişimi için)
- Korumalı sayfa ve API endpoint'leri
- REST API ile kullanıcı profil bilgilerine erişim

## Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Composer (PHP paket yöneticisi)

## Kurulum

### 1. XAMPP veya MAMP Kurulumu

#### Windows için XAMPP Kurulumu
1. [XAMPP indirme sayfasından](https://www.apachefriends.org/) en son sürümü indirin
2. İndirilen dosyayı çalıştırın ve kurulum talimatlarını izleyin
3. Kurulum tamamlandıktan sonra XAMPP Kontrol Paneli'ni açın
4. Apache ve MySQL modüllerini başlatın

#### macOS için MAMP Kurulumu
1. [MAMP indirme sayfasından](https://www.mamp.info/) en son sürümü indirin
2. İndirilen dosyayı çalıştırın ve uygulamayı Applications klasörüne sürükleyin
3. MAMP uygulamasını açın ve "Start Servers" düğmesine tıklayın

### 2. Proje Dosyalarını Kopyalama

#### Windows (XAMPP)
1. Bu projeyi ZIP olarak indirin veya git ile klonlayın
2. Proje dosyalarını `C:\xampp\htdocs\jwt-auth-system` klasörüne kopyalayın/çıkarın

#### macOS (MAMP)
1. Bu projeyi ZIP olarak indirin veya git ile klonlayın
2. Proje dosyalarını `/Applications/MAMP/htdocs/jwt-auth-system` klasörüne kopyalayın/çıkarın

### 3. Veritabanı Oluşturma

1. Tarayıcınızda phpMyAdmin'i açın:
   - XAMPP: http://localhost/phpmyadmin
   - MAMP: http://localhost:8888/phpMyAdmin (veya MAMP kontrol panelindeki phpMyAdmin düğmesine tıklayın)

2. Yeni bir veritabanı oluşturun:
   - Veritabanı adı: `jwt_auth`
   - Karakter seti: `utf8mb4_general_ci`

3. Yeni oluşturulan `jwt_auth` veritabanını seçin ve SQL sekmesine tıklayın

4. Aşağıdaki SQL kodunu yapıştırın ve çalıştırın:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4. Bağımlılıkları Yükleme

1. Komut satırını (terminal veya command prompt) açın
2. Proje dizinine gidin:
   - Windows: `cd C:\xampp\htdocs\jwt-auth-system`
   - macOS: `cd /Applications/MAMP/htdocs/jwt-auth-system`
3. Composer kurulu değilse, aşağıdaki adımları izleyin:
   - [Composer'in resmi web sitesinden](https://getcomposer.org/download/) kurulum talimatlarını takip edin
   - Veya MAMP için aşağıdaki komutları çalıştırın:
     ```
     /Applications/MAMP/bin/php/php[version]/bin/php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
     /Applications/MAMP/bin/php/php[version]/bin/php composer-setup.php --install-dir=. --filename=composer
     /Applications/MAMP/bin/php/php[version]/bin/php -r "unlink('composer-setup.php');"
     ```
     (Not: `[version]` kısmını MAMP'teki PHP sürümünüzle değiştirin, örn: `php8.3.14`)

4. Composer ile bağımlılıkları yükleyin:
   ```
   # Global Composer kuruluysa:
   composer install
   
   # Yerel Composer kurulumu yaptıysanız:
   /Applications/MAMP/bin/php/php[version]/bin/php composer install
   ```
   
5. Gerekli paketler:
   - firebase/php-jwt: JWT token işlemleri için
   - vlucas/phpdotenv: Çevre değişkenleri yönetimi için

### 5. Çevre Değişkenleri Yapılandırması

1. Proje kök dizininde `.env.example` dosyasını `.env` olarak kopyalayın:
   ```
   cp .env.example .env
   ```

2. `.env` dosyasını bir metin editörü ile açın ve aşağıdaki ayarları güncelleyin:
   ```
   # Veritabanı Yapılandırması
   DB_HOST=localhost
   DB_NAME=jwt_auth
   DB_USER=root
   DB_PASS=root  # XAMPP için genellikle boş, MAMP için 'root'
   
   # JWT Yapılandırması
   JWT_SECRET=your_secure_secret_key_change_this  # Güvenlik için bu anahtarı değiştirin!
   JWT_EXPIRE=3600
   JWT_ISSUER=jwt_auth_system
   ```
   
   **Önemli:** JWT_SECRET değerini güvenli, karmaşık ve benzersiz bir değerle değiştirin. 
   Bu anahtar, token'larınızın güvenliği için kritik öneme sahiptir.

## Projeyi Çalıştırma

1. XAMPP veya MAMP üzerinden Apache ve MySQL servislerinin çalıştığından emin olun
2. Tarayıcınızda projeyi açın:
   - XAMPP: http://localhost/jwt-auth-system
   - MAMP: http://localhost:8888/jwt-auth-system

## Kullanım

1. Anasayfada (`index.php`) giriş formunu göreceksiniz
2. Hesabınız yoksa "Kayıt Ol" bağlantısına tıklayarak yeni bir hesap oluşturun
3. Giriş yaptıktan sonra dashboard sayfasına yönlendirileceksiniz
4. Dashboard üzerindeki "Profil Bilgilerini Getir" düğmesine tıklayarak API'den JWT doğrulamalı bilgileri alabilirsiniz

## API Kullanımı

API'yi test etmek için Postman veya cURL kullanabilirsiniz:

### Giriş (JWT Token Alma)

```
POST /jwt-auth-system/api/login.php
Content-Type: application/json

{
  "email": "kullanici@ornek.com",
  "password": "sifreniz"
}
```

### Profil Bilgilerini Alma

```
GET /jwt-auth-system/api/profile.php
Authorization: Bearer [jwt_token]
```
