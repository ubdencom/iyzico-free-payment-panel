<?php
require_once 'config.php';

session_start();
requireLogin();

$db = getDatabaseConnection();
$userId = $_SESSION['user_id'];

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Filtering
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Build query
$whereConditions = ["user_id = ?"];
$params = [$userId];

if ($statusFilter != 'all') {
    $whereConditions[] = "status = ?";
    $params[] = $statusFilter;
}

if (!empty($searchQuery)) {
    $whereConditions[] = "(buyer_name LIKE ? OR buyer_surname LIKE ? OR buyer_phone LIKE ? OR payment_id LIKE ?)";
    $searchTerm = "%$searchQuery%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

$whereClause = implode(" AND ", $whereConditions);

// Get total count
$countStmt = $db->prepare("SELECT COUNT(*) as total FROM payments WHERE $whereClause");
$countStmt->execute($params);
$totalRecords = $countStmt->fetch()['total'];
$totalPages = ceil($totalRecords / $perPage);

// Get payments
$stmt = $db->prepare("SELECT * FROM payments WHERE $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?");
$params[] = $perPage;
$params[] = $offset;
$stmt->execute($params);
$payments = $stmt->fetchAll();

// Get user info
$userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Geçmişi - <?php echo SITE_NAME; ?></title>
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
                <a href="new-payment.php" class="menu-item">
                    <i class="fas fa-plus-circle"></i>
                    <span>Yeni Ödeme</span>
                </a>
                <a href="payments.php" class="menu-item active">
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
                    <h1>Ödeme Geçmişi</h1>
                    <p>Toplam <?php echo $totalRecords; ?> işlem</p>
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
                <!-- Filters -->
                <div class="card" style="margin-bottom: 20px;">
                    <form method="get" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
                        <div class="form-group" style="margin: 0; flex: 1; min-width: 200px;">
                            <label for="search" style="margin-bottom: 5px; font-size: 13px;">
                                <i class="fas fa-search"></i> Ara
                            </label>
                            <input type="text" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($searchQuery); ?>"
                                   placeholder="İsim, telefon, ödeme ID..."
                                   style="margin: 0;">
                        </div>

                        <div class="form-group" style="margin: 0; min-width: 150px;">
                            <label for="status" style="margin-bottom: 5px; font-size: 13px;">
                                <i class="fas fa-filter"></i> Durum
                            </label>
                            <select id="status" name="status" style="margin: 0;">
                                <option value="all" <?php echo $statusFilter == 'all' ? 'selected' : ''; ?>>Tümü</option>
                                <option value="success" <?php echo $statusFilter == 'success' ? 'selected' : ''; ?>>Başarılı</option>
                                <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Bekliyor</option>
                                <option value="failed" <?php echo $statusFilter == 'failed' ? 'selected' : ''; ?>>Başarısız</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary" style="margin: 0;">
                            <i class="fas fa-search"></i>
                            Filtrele
                        </button>

                        <?php if ($statusFilter != 'all' || !empty($searchQuery)): ?>
                            <a href="payments.php" class="btn" style="background: #6b7280; color: white; margin: 0;">
                                <i class="fas fa-times"></i>
                                Temizle
                            </a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Payments Table -->
                <div class="card">
                    <?php if (count($payments) > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Müşteri</th>
                                        <th>İletişim</th>
                                        <th>Tutar</th>
                                        <th>Durum</th>
                                        <th>İşlem ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600;">
                                                    <?php echo date('d.m.Y', strtotime($payment['created_at'])); ?>
                                                </div>
                                                <small style="color: var(--text-secondary);">
                                                    <?php echo date('H:i', strtotime($payment['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <strong>
                                                    <?php echo htmlspecialchars($payment['buyer_name'] . ' ' . $payment['buyer_surname']); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <div style="font-size: 13px;">
                                                    <div><i class="fas fa-phone" style="width: 15px;"></i> <?php echo htmlspecialchars($payment['buyer_phone']); ?></div>
                                                    <?php if ($payment['buyer_email']): ?>
                                                        <div style="color: var(--text-secondary); margin-top: 3px;">
                                                            <i class="fas fa-envelope" style="width: 15px;"></i> 
                                                            <?php echo htmlspecialchars($payment['buyer_email']); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <strong style="font-size: 16px;">
                                                    ₺<?php echo number_format($payment['amount'], 2, ',', '.'); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php if ($payment['status'] == 'success'): ?>
                                                    <span class="status-badge status-success">
                                                        <i class="fas fa-check-circle"></i> Başarılı
                                                    </span>
                                                <?php elseif ($payment['status'] == 'pending'): ?>
                                                    <span class="status-badge status-pending">
                                                        <i class="fas fa-clock"></i> Bekliyor
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-badge status-failed">
                                                        <i class="fas fa-times-circle"></i> Başarısız
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small style="font-family: monospace; font-size: 11px;">
                                                    <?php echo htmlspecialchars(substr($payment['payment_id'] ?? $payment['conversation_id'], 0, 20)); ?>
                                                    <?php if (strlen($payment['payment_id'] ?? $payment['conversation_id']) > 20): ?>...<?php endif; ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $statusFilter; ?>&search=<?php echo urlencode($searchQuery); ?>" 
                                       class="btn" style="background: var(--bg-secondary); color: var(--text-primary); padding: 8px 16px;">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <span style="color: var(--text-secondary); font-size: 14px;">
                                    Sayfa <?php echo $page; ?> / <?php echo $totalPages; ?>
                                </span>

                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $statusFilter; ?>&search=<?php echo urlencode($searchQuery); ?>" 
                                       class="btn" style="background: var(--bg-secondary); color: var(--text-primary); padding: 8px 16px;">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-search"></i>
                            <h3>Sonuç Bulunamadı</h3>
                            <p>Arama kriterlerinize uygun ödeme bulunamadı.</p>
                            <?php if ($statusFilter != 'all' || !empty($searchQuery)): ?>
                                <br>
                                <a href="payments.php" class="btn btn-primary">
                                    <i class="fas fa-list"></i>
                                    Tüm Ödemeleri Göster
                                </a>
                            <?php endif; ?>
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
    </script>

    <footer style="text-align: center; padding: 20px; background: white; border-top: 1px solid var(--border-color); margin-top: auto;">
        <p style="font-size: 13px; color: var(--text-secondary);">
            Made and Powered by - <a href="https://ubden.com/" target="_blank" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Ubden® Community Platform</a>
        </p>
        <p style="margin-top: 5px; font-size: 12px; color: var(--text-secondary);">© <?php echo date('Y'); ?> Tüm Hakları Saklıdır.</p>
    </footer>
</body>
</html>

