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

// İstatistikleri hesapla
$stats = [
    'total_payments' => 0,
    'successful_payments' => 0,
    'failed_payments' => 0,
    'total_amount' => 0,
    'today_amount' => 0,
    'today_count' => 0,
];

// Toplam ödeme sayısı
$stmt = $db->prepare("SELECT COUNT(*) as count FROM payments WHERE user_id = ?");
$stmt->execute([$userId]);
$stats['total_payments'] = $stmt->fetch()['count'];

// Başarılı ödemeler
$stmt = $db->prepare("SELECT COUNT(*) as count, COALESCE(SUM(paid_amount), 0) as total FROM payments WHERE user_id = ? AND status = 'success'");
$stmt->execute([$userId]);
$result = $stmt->fetch();
$stats['successful_payments'] = $result['count'];
$stats['total_amount'] = $result['total'];

// Başarısız ödemeler
$stmt = $db->prepare("SELECT COUNT(*) as count FROM payments WHERE user_id = ? AND status = 'failed'");
$stmt->execute([$userId]);
$stats['failed_payments'] = $stmt->fetch()['count'];

// Bugünkü ödemeler
$stmt = $db->prepare("SELECT COUNT(*) as count, COALESCE(SUM(paid_amount), 0) as total FROM payments WHERE user_id = ? AND status = 'success' AND DATE(created_at) = CURDATE()");
$stmt->execute([$userId]);
$result = $stmt->fetch();
$stats['today_count'] = $result['count'];
$stats['today_amount'] = $result['total'];

// Son ödemeleri al
$stmt = $db->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$userId]);
$recentPayments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - <?php echo SITE_NAME; ?></title>
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
                <a href="dashboard.php" class="menu-item active">
                    <i class="fas fa-home"></i>
                    <span>Ana Sayfa</span>
                </a>
                <a href="new-payment.php" class="menu-item">
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
                    <h1>Hoş Geldiniz, <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>!</h1>
                    <p><?php echo date('d F Y, l'); ?></p>
                </div>
                <div class="topbar-right">
                    <a href="new-payment.php" class="topbar-btn">
                        <i class="fas fa-plus"></i>
                        Yeni Ödeme
                    </a>
                    <a href="logout.php" class="topbar-btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        Çıkış
                    </a>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Toplam Gelir</div>
                            <div class="stat-value">₺<?php echo number_format($stats['total_amount'], 2, ',', '.'); ?></div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> Tüm zamanlar
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Başarılı Ödemeler</div>
                            <div class="stat-value"><?php echo $stats['successful_payments']; ?></div>
                            <div class="stat-change positive">
                                <i class="fas fa-check"></i> Toplam işlem
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Bugünkü Gelir</div>
                            <div class="stat-value">₺<?php echo number_format($stats['today_amount'], 2, ',', '.'); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-info-circle"></i> <?php echo $stats['today_count']; ?> işlem
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Başarısız İşlemler</div>
                            <div class="stat-value"><?php echo $stats['failed_payments']; ?></div>
                            <div class="stat-change negative">
                                <i class="fas fa-exclamation-triangle"></i> Toplam
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i>
                            Son İşlemler
                        </h3>
                    </div>

                    <?php if (count($recentPayments) > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Müşteri</th>
                                        <th>Tutar</th>
                                        <th>Durum</th>
                                        <th>İşlem ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentPayments as $payment): ?>
                                        <tr>
                                            <td><?php echo date('d.m.Y H:i', strtotime($payment['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($payment['buyer_name'] . ' ' . $payment['buyer_surname']); ?></td>
                                            <td><strong>₺<?php echo number_format($payment['amount'], 2, ',', '.'); ?></strong></td>
                                            <td>
                                                <?php if ($payment['status'] == 'success'): ?>
                                                    <span class="status-badge status-success">Başarılı</span>
                                                <?php elseif ($payment['status'] == 'pending'): ?>
                                                    <span class="status-badge status-pending">Bekliyor</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-failed">Başarısız</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?php echo htmlspecialchars($payment['payment_id'] ?? $payment['conversation_id']); ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="payments.php" class="btn btn-primary">
                                <i class="fas fa-list"></i>
                                Tüm İşlemleri Görüntüle
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>Henüz İşlem Yok</h3>
                            <p>Ödeme almaya başlamak için yeni bir ödeme oluşturun.</p>
                            <br>
                            <a href="new-payment.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Yeni Ödeme Oluştur
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Close sidebar on outside click (mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
</body>
</html>

