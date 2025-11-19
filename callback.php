<?php
require_once 'config.php';

session_start();

try {
    // İyzico Options
    $options = getIyzicoOptions();
    
    $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
    $request->setToken($_POST['token']);
    
    $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, $options);
    $status = $checkoutForm->getStatus();
    $paymentId = $checkoutForm->getPaymentId();
    $errorMessage = $checkoutForm->getErrorMessage();
    $amount = $checkoutForm->getPrice();
    $conversationId = $checkoutForm->getConversationId();
    
    // Veritabanını güncelle
    $db = getDatabaseConnection();
    
    if ($status == 'success') {
        $stmt = $db->prepare("UPDATE payments SET status = 'success', payment_id = ?, paid_amount = ?, updated_at = NOW() WHERE conversation_id = ?");
        $stmt->execute([$paymentId, $amount, $conversationId]);
        
        // Başarılı ödemeyi logla
        $paymentStmt = $db->prepare("SELECT user_id FROM payments WHERE conversation_id = ?");
        $paymentStmt->execute([$conversationId]);
        $payment = $paymentStmt->fetch();
        
        if ($payment) {
            logAction($payment['user_id'], 'payment_success', 'Ödeme başarılı: ' . $amount . ' TL - Payment ID: ' . $paymentId);
        }
    } else {
        $stmt = $db->prepare("UPDATE payments SET status = 'failed', error_message = ?, updated_at = NOW() WHERE conversation_id = ?");
        $stmt->execute([$errorMessage, $conversationId]);
        
        // Başarısız ödemeyi logla
        $paymentStmt = $db->prepare("SELECT user_id FROM payments WHERE conversation_id = ?");
        $paymentStmt->execute([$conversationId]);
        $payment = $paymentStmt->fetch();
        
        if ($payment) {
            logAction($payment['user_id'], 'payment_failed', 'Ödeme başarısız: ' . $errorMessage);
        }
    }
} catch (Exception $e) {
    $status = 'error';
    $errorMessage = 'Beklenmeyen bir hata oluştu.';
    error_log('Callback hatası: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Sonucu - <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/callback.css">
</head>
<body>
    <div class="result-container">
        <?php if ($status == 'success'): ?>
            <div class="result-card success">
                <div class="icon-wrapper">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>Ödeme Başarılı!</h1>
                <p class="message">İşleminiz başarıyla tamamlandı.</p>
                
                <div class="payment-details">
                    <div class="detail-row">
                        <span class="label">Ödeme Tutarı:</span>
                        <span class="value"><?php echo number_format($amount, 2, ',', '.'); ?> TL</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Ödeme ID:</span>
                        <span class="value"><?php echo htmlspecialchars($paymentId); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">İşlem Zamanı:</span>
                        <span class="value"><?php echo date('d.m.Y H:i:s'); ?></span>
                    </div>
                </div>

                <div class="actions">
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print"></i>
                        Yazdır
                    </button>
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-home"></i>
                        Panele Dön
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="result-card error">
                <div class="icon-wrapper">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h1>Ödeme Başarısız!</h1>
                <p class="message">İşleminiz tamamlanamadı.</p>
                
                <div class="error-details">
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($errorMessage ?? 'Bilinmeyen hata'); ?></span>
                    </div>
                </div>

                <div class="actions">
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-redo"></i>
                        Tekrar Dene
                    </a>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-home"></i>
                        Panele Dön
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto redirect after 10 seconds for successful payments
        <?php if ($status == 'success'): ?>
        setTimeout(function() {
            window.location.href = 'dashboard.php';
        }, 10000);
        <?php endif; ?>
    </script>

    <footer style="text-align: center; padding: 20px; color: rgba(255,255,255,0.7); font-size: 13px;">
        <p>Made and Powered by - <a href="https://ubden.com/" target="_blank" style="color: rgba(255,255,255,0.9); text-decoration: none; font-weight: 600;">Ubden® Community Platform</a></p>
        <p style="margin-top: 5px; font-size: 12px;">© <?php echo date('Y'); ?> Tüm Hakları Saklıdır.</p>
    </footer>
</body>
</html>
