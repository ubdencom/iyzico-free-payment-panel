# İyzico Bayi Ödeme Paneli - Kurulum Rehberi

## Hızlı Başlangıç

### 1. Sistemi İndirin
```bash
git clone https://github.com/yourusername/iyzico-free-payment-panel.git
cd iyzico-free-payment-panel
```

### 2. Composer Bağımlılıklarını Yükleyin
```bash
cd odeme-sayfasi
composer install
cd ..
```

### 3. Yapılandırma Dosyasını Oluşturun
```bash
cp config.example.php config.php
```

Ardından `config.php` dosyasını düzenleyin:

```php
// Veritabanı Bilgileri
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'your_db_password');
define('DB_NAME', 'iyzico_panel');

// İyzico API Bilgileri
define('IYZICO_API_KEY', 'your_api_key');
define('IYZICO_SECRET_KEY', 'your_secret_key');
define('IYZICO_BASE_URL', 'https://api.iyzipay.com');

// Site URL
define('SITE_URL', 'http://yourdomain.com');
```

### 4. Veritabanını Hazırlayın

**Otomatik Kurulum (Önerilen):**
- Sadece tarayıcınızda projeyi açın
- Migration sistemi otomatik olarak veritabanını oluşturacaktır

**Manuel Kurulum:**
```sql
CREATE DATABASE iyzico_panel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Web Sunucusu Yapılandırması

**Apache:**
- `.htaccess` dosyası zaten mevcut
- `mod_rewrite` modülünün aktif olduğundan emin olun

**Nginx:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/iyzico-free-payment-panel;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 6. İzinleri Ayarlayın
```bash
# Linux/Mac
chmod 755 -R .
chmod 644 config.php

# Dosya sahipliği (web sunucusu kullanıcısına göre)
chown -R www-data:www-data .
```

### 7. İlk Kullanıcıyı Oluşturun
1. Tarayıcıda `http://yourdomain.com/signup.php` adresine gidin
2. Kullanıcı bilgilerinizi girin
3. Kayıt olduktan sonra giriş yapın

## İyzico API Anahtarları Nasıl Alınır?

1. [İyzico](https://www.iyzico.com/) hesabınıza giriş yapın
2. **Ayarlar** > **API Bilgileri** bölümüne gidin
3. API Key ve Secret Key bilgilerinizi kopyalayın
4. Test için Sandbox, canlı için Production anahtarlarını kullanın

### Test Ortamı (Sandbox)
```php
define('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com');
```

Test kartları için: https://dev.iyzipay.com/tr/test-kartlari

### Canlı Ortam (Production)
```php
define('IYZICO_BASE_URL', 'https://api.iyzipay.com');
```

## Güvenlik Kontrol Listesi

- [ ] `config.php` dosyası `.gitignore`'a eklendi
- [ ] Production ortamında `display_errors` kapatıldı
- [ ] HTTPS sertifikası yüklendi
- [ ] Veritabanı şifresi güçlü
- [ ] Dosya izinleri doğru ayarlandı
- [ ] İyzico canlı API anahtarları kullanılıyor
- [ ] Session güvenliği aktif
- [ ] Backup sistemi kuruldu

## Sorun Giderme

### Veritabanı Bağlantı Hatası
```
Çözüm:
1. MySQL/MariaDB çalışıyor mu kontrol edin
2. config.php'deki bilgileri doğrulayın
3. Veritabanı kullanıcısının yetkileri var mı kontrol edin
```

### 500 Internal Server Error
```
Çözüm:
1. PHP error log'larını kontrol edin
2. Dosya izinlerini kontrol edin
3. .htaccess dosyası doğru mu kontrol edin
```

### İyzico API Hatası
```
Çözüm:
1. API anahtarlarını doğrulayın
2. Sandbox/Production URL'sini kontrol edin
3. İyzico hesabınız aktif mi kontrol edin
```

## Destek

Sorunlarınız için:
- 📧 Email: support@yourdomain.com
- 🐛 GitHub Issues: https://github.com/yourusername/iyzico-free-payment-panel/issues
- 📚 Dokümantasyon: README.md

## Başarılı Kurulum!

Artık sisteminiz kullanıma hazır! 

- 🎯 Dashboard: http://yourdomain.com/dashboard.php
- 💳 Yeni Ödeme: http://yourdomain.com/new-payment.php
- 📊 Raporlar: http://yourdomain.com/payments.php

İyi çalışmalar! 🚀

