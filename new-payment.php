<?php
require_once 'config.php';

session_start();
requireLogin();

$db = getDatabaseConnection();

// Kullanıcı bilgilerini al
$userId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Ödeme - <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-credit-card"></i>
                    <h2>İyzico Panel</h2>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span>Ana Sayfa</span>
                </a>
                <a href="new-payment.php" class="menu-item active">
                    <i class="fas fa-plus-circle"></i>
                    <span>Yeni Ödeme</span>
                </a>
                <a href="payments.php" class="menu-item">
                    <i class="fas fa-list"></i>
                    <span>Ödeme Geçmişi</span>
                </a>
                <a href="reports.php" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Raporlar</span>
                </a>
                <a href="settings.php" class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Ayarlar</span>
                </a>
                <a href="legal.php" class="menu-item">
                    <i class="fas fa-file-contract"></i>
                    <span>Yasal Bilgilendirme</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                        <span class="user-role">Bayi</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="topbar">
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="topbar-left">
                    <h1>Yeni Ödeme Oluştur</h1>
                    <p>Müşterinizden ödeme almak için formu doldurun</p>
                </div>
                <div class="topbar-right">
                    <a href="dashboard.php" class="topbar-btn">
                        <i class="fas fa-arrow-left"></i>
                        Geri Dön
                    </a>
                    <a href="logout.php" class="topbar-btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        Çıkış
                    </a>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <div class="payment-form-container">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-money-bill-wave"></i>
                                Ödeme Bilgileri
                            </h3>
                        </div>

                        <form id="paymentForm" action="checkout.php" method="post">
                            <div class="form-group">
                                <label for="amount">
                                    <i class="fas fa-lira-sign"></i>
                                    Ödeme Miktarı (TL) *
                                </label>
                                <input type="number" id="amount" name="amount" 
                                       min="1" step="0.01" 
                                       placeholder="Örn: 100.00" 
                                       required>
                                <small style="color: var(--text-secondary); font-size: 13px; display: block; margin-top: 5px;">
                                    Minimum: 1.00 TL
                                </small>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">
                                        <i class="fas fa-user"></i>
                                        Müşteri Adı *
                                    </label>
                                    <input type="text" id="name" name="name" 
                                           placeholder="Örn: Ahmet" 
                                           required>
                                </div>

                                <div class="form-group">
                                    <label for="surname">
                                        <i class="fas fa-user"></i>
                                        Müşteri Soyadı *
                                    </label>
                                    <input type="text" id="surname" name="surname" 
                                           placeholder="Örn: Yılmaz" 
                                           required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone">
                                    <i class="fas fa-phone"></i>
                                    Telefon Numarası *
                                </label>
                                <input type="tel" id="phone" name="phone" 
                                       placeholder="05XXXXXXXXXX" 
                                       pattern="05[0-9]{9}"
                                       maxlength="11"
                                       required>
                                <small style="color: var(--text-secondary); font-size: 13px; display: block; margin-top: 5px;">
                                    Örnek format: 05XXXXXXXXXX
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i>
                                    E-posta Adresi
                                </label>
                                <input type="email" id="email" name="email" 
                                       placeholder="ornek@email.com">
                                <small style="color: var(--text-secondary); font-size: 13px; display: block; margin-top: 5px;">
                                    Opsiyonel - Müşteriye dekont gönderilebilir
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="description">
                                    <i class="fas fa-comment-alt"></i>
                                    Açıklama / Not
                                </label>
                                <textarea id="description" name="description" 
                                          rows="3" 
                                          placeholder="Ödeme ile ilgili notlarınız (opsiyonel)"
                                          style="resize: vertical;"></textarea>
                            </div>

                            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 15px; margin: 20px 0;">
                                <div style="display: flex; align-items: start; gap: 12px;">
                                    <i class="fas fa-info-circle" style="color: #2563eb; font-size: 20px; margin-top: 2px;"></i>
                                    <div>
                                        <strong style="color: #1e40af; display: block; margin-bottom: 5px;">Önemli Bilgilendirme</strong>
                                        <p style="font-size: 13px; color: #1e40af; margin: 0; line-height: 1.5;">
                                            Ödeme İyzico güvenli altyapısı üzerinden alınacaktır. 
                                            Müşteri bilgilerinin doğru olduğundan emin olun.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-credit-card"></i>
                                Ödeme Sayfasına Git
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            e.target.value = value;
        });

        // Amount formatting
        document.getElementById('amount').addEventListener('blur', function(e) {
            if (e.target.value) {
                e.target.value = parseFloat(e.target.value).toFixed(2);
            }
        });

        // Form validation
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const amount = parseFloat(document.getElementById('amount').value);
            const phone = document.getElementById('phone').value;

            if (amount < 1) {
                e.preventDefault();
                alert('Ödeme miktarı en az 1.00 TL olmalıdır!');
                return false;
            }

            if (!/^05[0-9]{9}$/.test(phone)) {
                e.preventDefault();
                alert('Lütfen geçerli bir telefon numarası girin! (05XXXXXXXXXX)');
                return false;
            }

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> İşleniyor...';
            submitBtn.disabled = true;
        });
    </script>

    <footer style="text-align: center; padding: 20px; background: white; border-top: 1px solid var(--border-color); margin-top: auto;">
        <p style="font-size: 13px; color: var(--text-secondary);">
            Made and Powered by - <a href="https://ubden.com/" target="_blank" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Ubden® Community Platform</a>
        </p>
        <p style="margin-top: 5px; font-size: 12px; color: var(--text-secondary);">© <?php echo date('Y'); ?> Tüm Hakları Saklıdır.</p>
    </footer>
</body>
</html>

