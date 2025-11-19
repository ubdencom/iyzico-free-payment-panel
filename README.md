# İyzico Bayi Ödeme Paneli

> **Profesyonel, güvenli ve kullanıcı dostu bayi ödeme yönetim sistemi**

Modern ve profesyonel bir bayi ödeme paneli. İyzico altyapısı kullanılarak müşterilerinizden serbest tutar ile güvenli ödeme almanızı sağlar. Otomatik veritabanı kurulumu, detaylı raporlama ve modern UI/UX tasarımı ile tam kapsamlı bir çözüm sunar.

![Ödeme Paneli](msedge_jtcq1slvTR.png)

## ✨ Özellikler

### 🎯 Temel Özellikler
- ✅ **Serbest Tutar Ödeme**: Müşterilerinizden istediğiniz tutarda ödeme alın
- ✅ **İyzico Entegrasyonu**: Güvenli ve lisanslı ödeme altyapısı
- ✅ **Otomatik Veritabanı**: İlk çalıştırmada otomatik migration sistemi
- ✅ **Kullanıcı Yönetimi**: Güvenli kayıt ve giriş sistemi
- ✅ **Ödeme Geçmişi**: Detaylı işlem listeleme ve filtreleme
- ✅ **Dashboard**: Gerçek zamanlı istatistikler ve grafikler

### 🎨 Modern UI/UX
- 📱 **Responsive Tasarım**: Mobil, tablet ve desktop uyumlu
- 🎨 **Modern Arayüz**: Inter font ailesi ve profesyonel renk paleti
- ⚡ **Hızlı Yükleme**: Optimize edilmiş CSS ve JavaScript
- 🌙 **Kolay Kullanım**: Sezgisel ve kullanıcı dostu arayüz

### 🔒 Güvenlik
- 🔐 **Güvenli Şifreleme**: Password hash ile şifre koruması
- 🛡️ **SQL Injection Koruması**: Prepared statements kullanımı
- 📝 **İşlem Loglama**: Tüm önemli işlemlerin kaydı
- 🔑 **Session Yönetimi**: Güvenli oturum kontrolü

### 📊 Raporlama
- 💰 **Gelir Raporları**: Günlük, haftalık, aylık gelir takibi
- 📈 **İşlem İstatistikleri**: Başarılı/başarısız işlem oranları
- 🔍 **Arama ve Filtreleme**: Gelişmiş arama özellikleri
- 📄 **Dökümantasyon**: Detaylı yasal bilgilendirme

## 🚀 Kurulum

### Gereksinimler

```
- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Composer
- İyzico API anahtarları
```

### Adım 1: Projeyi İndirin

```bash
git clone https://github.com/yourusername/iyzico-free-payment-panel.git
cd iyzico-free-payment-panel
```

### Adım 2: Composer Bağımlılıklarını Yükleyin

```bash
cd odeme-sayfasi
composer install
cd ..
```

### Adım 3: Yapılandırma Dosyasını Düzenleyin

`config.php` dosyasını açın ve aşağıdaki bilgileri güncelleyin:

```php
// Veritabanı Bilgileri
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'your_database_name');

// İyzico API Bilgileri
define('IYZICO_API_KEY', 'your_iyzico_api_key');
define('IYZICO_SECRET_KEY', 'your_iyzico_secret_key');
define('IYZICO_BASE_URL', 'https://api.iyzipay.com'); // Sandbox için: https://sandbox-api.iyzipay.com

// Site Yapılandırması
define('SITE_URL', 'http://yourdomain.com');
```

### Adım 4: Web Sunucusunu Yapılandırın

**Apache için (.htaccess zaten mevcut)**

Projeyi Apache root dizinine kopyalayın ve tarayıcınızda açın.

**Nginx için**

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
}
```

### Adım 5: İlk Kurulum

1. Tarayıcınızda `http://yourdomain.com/signup.php` adresine gidin
2. İlk kullanıcı hesabınızı oluşturun
3. **Otomatik veritabanı migration sistemi** tüm tabloları oluşturacaktır
4. Giriş yapın ve paneli kullanmaya başlayın!

## 📁 Proje Yapısı

```
iyzico-free-payment-panel/
├── assets/
│   ├── css/
│   │   ├── auth.css          # Giriş/Kayıt sayfası stilleri
│   │   ├── callback.css      # Ödeme sonuç sayfası stilleri
│   │   └── dashboard.css     # Panel stilleri
│   ├── js/
│   └── images/
├── odeme-sayfasi/
│   └── vendor/               # İyzico PHP SDK
├── config.php                # Yapılandırma ve migration sistemi
├── login.php                 # Giriş sayfası
├── signup.php                # Kayıt sayfası
├── logout.php                # Çıkış işlemi
├── dashboard.php             # Ana panel
├── new-payment.php           # Yeni ödeme formu
├── payments.php              # Ödeme geçmişi
├── checkout.php              # Ödeme işleme
├── callback.php              # Ödeme sonuç
├── legal.php                 # Yasal bilgilendirme
└── README.md                 # Bu dosya
```

## 🗄️ Veritabanı Yapısı

### Users Tablosu
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- username (VARCHAR(255), UNIQUE)
- password (VARCHAR(255))
- email (VARCHAR(255))
- full_name (VARCHAR(255))
- is_active (TINYINT(1))
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- last_login (TIMESTAMP)
```

### Payments Tablosu
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- user_id (INT, FOREIGN KEY)
- payment_id (VARCHAR(255))
- conversation_id (VARCHAR(255))
- amount (DECIMAL(10,2))
- paid_amount (DECIMAL(10,2))
- currency (VARCHAR(3))
- status (VARCHAR(50))
- error_message (TEXT)
- buyer_name (VARCHAR(255))
- buyer_surname (VARCHAR(255))
- buyer_phone (VARCHAR(20))
- buyer_email (VARCHAR(255))
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Logs Tablosu
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- user_id (INT, FOREIGN KEY)
- action (VARCHAR(100))
- description (TEXT)
- ip_address (VARCHAR(45))
- user_agent (TEXT)
- created_at (TIMESTAMP)
```

## ⚖️ Yasal Bilgilendirme

Bu panel, bayilerin müşterilerinden ödeme almasını sağlayan bir yazılımdır. Kullanım koşulları:

### Önemli Hususlar

1. **Sözleşme Gereksinimi**: Panel kullanımı bayi ile panel sağlayıcısı arasında sözleşmeye tabidir.

2. **Sorumluluk**: 
   - Son kullanıcı işlemleri **bayiye aittir**
   - Bayi, panel sağlayıcısına **doğru bilgi verme zorunluluğu** vardır
   - **Yasal sorumluluk paneli kullanan bayiye aittir**

3. **Virman İşlemleri** (Sözleşme dahilinde):
   - Panel sağlayıcısı, bayi alacağı karşılığında hesaplar arası virman yapabilir
   - Panel sağlayıcı, bedel karşılığında bayiye ters virman yapabilir

4. **Sorumluluk Reddi**: Ödeme Panel Yazılımı sağlayıcısı, yapılan işlemlerden doğacak hiçbir sonuçtan sorumlu tutulamaz.

**Detaylı bilgi için**: Paneldeki "Yasal Bilgilendirme" sayfasını inceleyin.

## 🔧 Geliştirme

### Geliştirici Modu

```php
// config.php dosyasında
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Production'da kapatın:
ini_set('display_errors', 0);
error_reporting(0);
```

### Test Ortamı (Sandbox)

İyzico sandbox ortamını kullanmak için:

```php
define('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com');
```

Test kartları: [İyzico Test Kartları](https://dev.iyzipay.com/tr/test-kartlari)

## 📝 Özelleştirme

### Renk Teması Değiştirme

`assets/css/dashboard.css` dosyasında CSS değişkenlerini düzenleyin:

```css
:root {
    --primary-color: #2563eb;      /* Ana renk */
    --primary-dark: #1e40af;       /* Ana renk (koyu) */
    --success-color: #10b981;      /* Başarı rengi */
    --danger-color: #ef4444;       /* Hata rengi */
    /* ... diğer renkler */
}
```

### Logo ve Branding

1. Logoyu `assets/images/` klasörüne ekleyin
2. Sidebar ve header bölümlerini düzenleyin
3. `SITE_NAME` sabitini config.php'de değiştirin

## 🤝 Katkıda Bulunma

1. Bu projeyi fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'inizi push edin (`git push origin feature/AmazingFeature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakın.

## 🆘 Destek

Sorularınız veya sorunlarınız için:

- 📧 Email: support@yourdomain.com
- 🐛 Issues: [GitHub Issues](https://github.com/yourusername/iyzico-free-payment-panel/issues)
- 📚 Dokümantasyon: [Wiki](https://github.com/yourusername/iyzico-free-payment-panel/wiki)

## 🙏 Teşekkürler

- [İyzico](https://www.iyzico.com/) - Ödeme altyapısı
- [Font Awesome](https://fontawesome.com/) - İkonlar
- [Inter Font](https://rsms.me/inter/) - Tipografi

## 📊 Özellikler Yol Haritası

- [ ] Toplu ödeme işlemleri
- [ ] Excel/PDF export
- [ ] E-posta bildirimleri
- [ ] SMS entegrasyonu
- [ ] API desteği
- [ ] Multi-language support
- [ ] Dark mode

---

**Not**: Bu platform İyzico tarafından resmi olarak sağlanmamaktadır. İyzico ödeme altyapısı kullanılmaktadır.

**⚠️ Üretim Ortamında Kullanım**: Bu yazılımı production ortamında kullanmadan önce güvenlik testlerini yapın ve yasal gereklilikleri kontrol edin.

---

Made with ❤️ by Developer Community
