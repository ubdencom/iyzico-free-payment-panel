<?php
/**
 * İyzico Bayi Ödeme Paneli - Yapılandırma Örnek Dosyası
 * 
 * Bu dosyayı config.php olarak kaydedin ve kendi bilgilerinizle doldurun.
 * UYARI: config.php dosyasını asla version control sistemine (git) eklemeyin!
 */

// Hata Raporlama (Production ortamında kapatın)
ini_set('display_errors', 0);  // Production'da 0 yapın
ini_set('display_startup_errors', 0);
error_reporting(0);  // Production'da 0 yapın

// Veritabanı Bağlantı Bilgileri
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Veritabanı şifrenizi girin
define('DB_NAME', 'iyzico_panel'); // Veritabanı adınızı girin
define('DB_CHARSET', 'utf8mb4');

// İyzico API Yapılandırması
// Test için sandbox, canlı için production API bilgilerini kullanın
define('IYZICO_API_KEY', 'sandbox-XXXXXXXXXXXXXXXXXXXXXXXX'); // İyzico API Key
define('IYZICO_SECRET_KEY', 'sandbox-XXXXXXXXXXXXXXXXXXXXXXXX'); // İyzico Secret Key
define('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'); // Canlı için: https://api.iyzipay.com

// Site Yapılandırması
define('SITE_URL', 'http://localhost'); // Sitenizin URL'si
define('SITE_NAME', 'İyzico Bayi Ödeme Paneli');
define('SITE_OWNER', 'Ubden® Community Platform');
define('SITE_OWNER_URL', 'https://ubden.com/');
define('CALLBACK_URL', SITE_URL . '/callback.php');

// Timezone Ayarı
date_default_timezone_set('Europe/Istanbul');

// Session Yapılandırması
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // HTTPS kullanıyorsanız 1 yapın

/**
 * Veritabanı Bağlantısı Oluştur
 */
function getDatabaseConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        return new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
    } catch (PDOException $e) {
        // Önce veritabanını oluşturmayı dene
        try {
            $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Tekrar bağlan
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            return new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        } catch (PDOException $e2) {
            die("Veritabanı bağlantısı kurulamadı: " . $e2->getMessage());
        }
    }
}

/**
 * Veritabanı Migration Sistemi
 */
function runMigrations() {
    $db = getDatabaseConnection();
    
    try {
        // Migration tablosunu oluştur
        $db->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration_name VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Tüm migration'ları tanımla
        $migrations = [
            'create_users_table' => "
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NULL,
                    full_name VARCHAR(255) NULL,
                    is_active TINYINT(1) DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    last_login TIMESTAMP NULL,
                    INDEX idx_username (username),
                    INDEX idx_email (email)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ",
            
            'create_payments_table' => "
                CREATE TABLE IF NOT EXISTS payments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    payment_id VARCHAR(255) NULL,
                    conversation_id VARCHAR(255) NULL,
                    amount DECIMAL(10, 2) NOT NULL,
                    paid_amount DECIMAL(10, 2) NULL,
                    currency VARCHAR(3) DEFAULT 'TRY',
                    status VARCHAR(50) NOT NULL,
                    error_message TEXT NULL,
                    buyer_name VARCHAR(255) NULL,
                    buyer_surname VARCHAR(255) NULL,
                    buyer_phone VARCHAR(20) NULL,
                    buyer_email VARCHAR(255) NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    INDEX idx_user_id (user_id),
                    INDEX idx_payment_id (payment_id),
                    INDEX idx_status (status),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ",
            
            'create_settings_table' => "
                CREATE TABLE IF NOT EXISTS settings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    setting_key VARCHAR(255) NOT NULL UNIQUE,
                    setting_value TEXT NULL,
                    setting_type VARCHAR(50) DEFAULT 'string',
                    description TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_setting_key (setting_key)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ",
            
            'create_logs_table' => "
                CREATE TABLE IF NOT EXISTS logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NULL,
                    action VARCHAR(100) NOT NULL,
                    description TEXT NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                    INDEX idx_user_id (user_id),
                    INDEX idx_action (action),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            "
        ];
        
        // Her migration'ı çalıştır
        foreach ($migrations as $name => $sql) {
            // Migration daha önce çalıştırılmış mı kontrol et
            $stmt = $db->prepare("SELECT id FROM migrations WHERE migration_name = ?");
            $stmt->execute([$name]);
            
            if ($stmt->rowCount() == 0) {
                // Migration'ı çalıştır
                $db->exec($sql);
                
                // Migration kaydını ekle
                $stmt = $db->prepare("INSERT INTO migrations (migration_name) VALUES (?)");
                $stmt->execute([$name]);
                
                error_log("Migration çalıştırıldı: " . $name);
            }
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Migration hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Log Fonksiyonu
 */
function logAction($userId, $action, $description = null) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("INSERT INTO logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (PDOException $e) {
        error_log("Log kaydetme hatası: " . $e->getMessage());
    }
}

/**
 * Güvenlik Fonksiyonları
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

/**
 * İyzico API Helper Fonksiyonları
 */
function getIyzicoOptions() {
    require_once 'odeme-sayfasi/vendor/autoload.php';
    
    $options = new \Iyzipay\Options();
    $options->setApiKey(IYZICO_API_KEY);
    $options->setSecretKey(IYZICO_SECRET_KEY);
    $options->setBaseUrl(IYZICO_BASE_URL);
    
    return $options;
}

// Migration'ları otomatik çalıştır
runMigrations();

