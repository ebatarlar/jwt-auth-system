# Güvenli Giriş Sistemi PRD (Product Requirements Document)

## Görev: Oturum ve JWT ile Güvenli Giriş Sistemi

### Amaç
PHP, MySQL ve tercihen JavaScript kullanarak güvenli bir giriş sistemi oluşturmak. Sistem, web için oturum tabanlı giriş ve API kullanımı için JWT tabanlı giriş desteklemelidir.

### Gereksinimler

#### Temel Özellikler
1. Kullanıcı Kaydı
   - Girdi: ad, e-posta, şifre
   - Şifreler güvenli şekilde `password_hash` ile saklanmalıdır.

2. Kullanıcı Girişi
   - `password_verify` kullanarak giriş doğrulaması yapılmalı
   - Giriş başarılıysa PHP oturumu başlatılmalı

3. JWT ile Giriş (API için)
   - `/api/login.php` gibi bir endpoint geçerli bilgilerle JWT döndürmelidir
   - `/api/profile.php` gibi korunan bir endpoint, sadece geçerli token varsa kullanıcının ad ve e-posta bilgilerini döner

4. Çıkış
   - Oturumu güvensiz hale getir

5. Korunan Sayfa
   - `/dashboard.php` sayfası sadece giriş yapmış kullanıcı tarafından erişilebilir olmalı
   - `/dashboard.php` içerisinde bir butona tıklandığında `/api/profile.php` endpointinden kullanıcı bilgileri gösterir

### Teknoloji Beklentileri
- Kullanıcılar için MySQL tablosu (id, ad, e-posta, şifre_hash, oluşturulma_tarihi)
- Düz PHP kullanımı (herhangi bir framework olmadan)
- Frontend'de tercihen JS ya da CSS kütüphanesi/framework kullanılabilir
- JWT oluşturma/doğrulama için basit bir PHP JWT kütüphanesi kullanımı (örneğin, `firebase/php-jwt`)

### Proje Yapısı
```
└── jwt-auth-system/
    ├── api/
    │   ├── login.php
    │   ├── profile.php
    │   ├── register.php
    │   └── verify-token.php
    ├── api_test.html
    ├── assets/
    │   └── js/
    │       ├── dashboard.js
    │       ├── login.js
    │       └── registration.js
    ├── composer.json
    ├── config/
    │   ├── database.php
    │   └── env.php
    ├── includes/
    │   ├── auth.php
    │   └── jwt_utils.php
    ├── dashboard.php
    ├── index.php (login sayfası)
    ├── logout.php
    ├── registration.php
    ├── swagger.json
    ├── swagger-ui.html
    ├── README.md
    └── PRD.md
```

### Veritabanı Yapısı
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Adım Adım Geliştirme Planı

#### 1. Veritabanı Bağlantısı (config/database.php)
- Veritabanı bağlantı bilgilerini ayarlama
- PDO kullanarak güvenli bağlantı oluşturma

#### 2. Kullanıcı Kaydı (register.php)
- Kayıt formu oluşturma (ad, e-posta, şifre)
- Girdi doğrulama ve sanitizasyon işlemleri ekleme
- Şifreyi `password_hash()` fonksiyonu ile güvenli şekilde saklama
- Kullanıcıyı veritabanına kaydetme

#### 3. Kullanıcı Girişi (index.php)
- Giriş formu oluşturma
- Kullanıcıyı veritabanında kontrol etme
- `password_verify()` ile şifre doğrulaması yapma
- Başarılı girişte oturum başlatma ve dashboard'a yönlendirme

#### 4. JWT Entegrasyonu (includes/jwt_utils.php)
- `firebase/php-jwt` kütüphanesini Composer ile projeye ekleme
- JWT oluşturma, doğrulama ve çözme fonksiyonlarını yazma

#### 5. API Endpointleri
- **api/login.php**
  - Kullanıcı bilgilerini doğrulama
  - Geçerli bilgilerle JWT token oluşturma ve döndürme

- **api/profile.php**
  - JWT token'ını Authorization header'dan alma
  - Token'ı doğrulama
  - Geçerli token varsa kullanıcı bilgilerini JSON formatında döndürme

#### 6. Korumalı Sayfa (dashboard.php)
- Oturum kontrolü yapma, giriş yapmamış kullanıcıları login sayfasına yönlendirme
- Kullanıcı bilgilerini görüntüleme
- API endpointinden veri almak için bir buton ekleme
- JavaScript ile `/api/profile.php` endpointine istek gönderme ve sonucu görüntüleme

#### 7. Çıkış İşlemi (logout.php)
- Oturumu sonlandırma
- Kullanıcıyı giriş sayfasına yönlendirme

### Güvenlik Önlemleri
- Cross-Site Scripting (XSS) koruması için girdi doğrulama ve çıktı filtreleme
- CSRF koruması için token kullanımı
- SQL Injection'a karşı prepared statement kullanımı
- JWT için güçlü ve gizli bir anahtar kullanımı

### Frontend Geliştirme
- Temel JS ve CSS (Bootstrap gibi bir framework kullanılabilir)
- API istekleri için Fetch API veya Axios kullanımı
- JWT token'ı localStorage'da saklama ve API isteklerinde header'a ekleme

### Teslim
- GitHub repo bağlantısı veya ZIP dosyası
- Kurulum talimatları metin olarak iletilebilir
