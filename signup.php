<?php
require_once 'config.php';

session_start();

// Eğer kullanıcı zaten giriş yapmışsa dashboard'a yönlendir
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['username']) && !empty($_POST['password'])) {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email'] ?? '');
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validasyon
    if (strlen($username) < 3) {
        $error_message = "Kullanıcı adı en az 3 karakter olmalıdır!";
    } elseif (strlen($password) < 6) {
        $error_message = "Şifre en az 6 karakter olmalıdır!";
    } elseif ($password !== $confirmPassword) {
        $error_message = "Şifreler eşleşmiyor!";
    } else {
        try {
            $db = getDatabaseConnection();
            
            // Kullanıcı adı kontrolü
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                $error_message = "Bu kullanıcı adı zaten kullanılıyor!";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $query = $db->prepare("INSERT INTO users (username, password, email, full_name) VALUES (?, ?, ?, ?)");
                $result = $query->execute([$username, $hashedPassword, $email, $fullName]);
                
                if ($result) {
                    $success_message = "Kayıt başarıyla tamamlandı! Giriş yapabilirsiniz.";
                    
                    // Log kaydet
                    $userId = $db->lastInsertId();
                    logAction($userId, 'user_registered', 'Yeni kullanıcı kaydı: ' . $username);
                    
                    // 2 saniye bekle ve login'e yönlendir
                    header("refresh:2;url=login.php");
                } else {
                    $error_message = "Kayıt sırasında bir hata meydana geldi!";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Veritabanı hatası: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üye Ol - <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h1><?php echo SITE_NAME; ?></h1>
                <p class="subtitle">Yeni Hesap Oluştur</p>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $success_message; ?></span>
                </div>
            <?php endif; ?>

            <form id="signupForm" method="post" class="auth-form">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        Kullanıcı Adı
                    </label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Kullanıcı adınızı girin" autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="full_name">
                        <i class="fas fa-id-card"></i>
                        Ad Soyad
                    </label>
                    <input type="text" id="full_name" name="full_name" 
                           placeholder="Ad ve soyadınızı girin" autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        E-posta
                    </label>
                    <input type="email" id="email" name="email" 
                           placeholder="E-posta adresinizi girin" autocomplete="email">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Şifre
                    </label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required 
                               placeholder="Şifrenizi girin" autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i>
                        Şifre Tekrar
                    </label>
                    <div class="password-input">
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               placeholder="Şifrenizi tekrar girin" autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" required>
                        <span><a href="legal.php" target="_blank">Kullanım koşullarını</a> ve <a href="legal.php" target="_blank">yasal bilgilendirmeyi</a> okudum, kabul ediyorum.</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i>
                    Hesap Oluştur
                </button>

                <div class="auth-footer">
                    <p>Zaten hesabınız var mı? <a href="login.php">Giriş Yapın</a></p>
                </div>
            </form>
        </div>

        <div class="auth-info">
            <div class="info-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Güvenli Ödeme</h3>
                <p>İyzico altyapısı ile güvenli ödeme alın</p>
            </div>
            <div class="info-card">
                <i class="fas fa-chart-line"></i>
                <h3>Raporlama</h3>
                <p>Detaylı ödeme raporları ve analizler</p>
            </div>
            <div class="info-card">
                <i class="fas fa-mobile-alt"></i>
                <h3>Kolay Kullanım</h3>
                <p>Mobil uyumlu ve kullanıcı dostu arayüz</p>
            </div>
        </div>
    </div>

    <footer style="text-align: center; padding: 20px; color: rgba(255,255,255,0.7); font-size: 13px;">
        <p>Made and Powered by - <a href="https://ubden.com/" target="_blank" style="color: rgba(255,255,255,0.9); text-decoration: none; font-weight: 600;">Ubden® Community Platform</a></p>
        <p style="margin-top: 5px; font-size: 12px;">© <?php echo date('Y'); ?> Tüm Hakları Saklıdır.</p>
    </footer>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Şifre gücü göstergesi
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.querySelector('.strength-bar');
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'strength-bar strength-' + strength;
        });
    </script>
</body>
</html>
