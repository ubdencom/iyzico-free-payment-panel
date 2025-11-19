<?php
require_once 'config.php';

session_start();

// Eğer kullanıcı zaten giriş yapmışsa dashboard'a yönlendir
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['username']) && !empty($_POST['password'])) {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    try {
        $db = getDatabaseConnection();
        
        $query = $db->prepare("SELECT id, username, password, is_active FROM users WHERE username = ?");
        $query->execute([$username]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active'] == 1) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Son giriş zamanını güncelle
                $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                // Log kaydet
                logAction($user['id'], 'user_login', 'Kullanıcı giriş yaptı');
                
                // Dashboard'a yönlendir
                header("Location: dashboard.php");
                exit;
            } else {
                $error_message = "Hesabınız aktif değil! Lütfen yönetici ile iletişime geçin.";
            }
        } else {
            $error_message = "Kullanıcı adı veya şifre hatalı!";
            
            // Başarısız giriş denemesini logla
            if ($user) {
                logAction($user['id'], 'failed_login', 'Başarısız giriş denemesi');
            }
        }
    } catch (PDOException $e) {
        $error_message = "Veritabanı hatası: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - <?php echo SITE_NAME; ?></title>
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
                <p class="subtitle">Hesabınıza Giriş Yapın</p>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="post" class="auth-form">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        Kullanıcı Adı
                    </label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Kullanıcı adınızı girin" autocomplete="username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Şifre
                    </label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required 
                               placeholder="Şifrenizi girin" autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span>Beni hatırla</span>
                    </label>
                    <a href="#" class="forgot-password">Şifremi unuttum?</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i>
                    Giriş Yap
                </button>

                <div class="auth-footer">
                    <p>Hesabınız yok mu? <a href="signup.php">Üye Olun</a></p>
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
    </script>
</body>
</html>
