<?php
require_once 'config.php';

session_start();
requireLogin();

// Form verilerini al
$amount = $_POST['amount'] ?? 0;
$name = sanitizeInput($_POST['name'] ?? '');
$surname = sanitizeInput($_POST['surname'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$email = sanitizeInput($_POST['email'] ?? 'sales@ubden.com');

// Validasyon
if ($amount <= 0) {
    die("Geçersiz ödeme miktarı!");
}

try {
    // İyzico Options
    $options = getIyzicoOptions();
    
    $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
    $request->setLocale(\Iyzipay\Model\Locale::TR);
    $conversationId = uniqid('conv_');
    $request->setConversationId($conversationId);
    $request->setPrice($amount);
    $request->setPaidPrice($amount);
    $request->setCurrency(\Iyzipay\Model\Currency::TL);
    $request->setBasketId(uniqid('basket_'));
    $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
    $request->setCallbackUrl(CALLBACK_URL);
    
    // Buyer bilgilerini oluştur
    $buyer = new \Iyzipay\Model\Buyer();
    $buyer->setId("RESELLER_" . $_SESSION['user_id']);
    $buyer->setName($name);
    $buyer->setSurname($surname);
    $buyer->setGsmNumber("+9" . $phone);
    $buyer->setEmail($email);
    $buyer->setIdentityNumber("11111111111");
    $buyer->setLastLoginDate(date("Y-m-d H:i:s"));
    $buyer->setRegistrationDate(date("Y-m-d H:i:s"));
    $buyer->setRegistrationAddress("Adres Bilgisi");
    $buyer->setIp($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
    $buyer->setCity("Istanbul");
    $buyer->setCountry("Turkey");
    $buyer->setZipCode("34398");
    $request->setBuyer($buyer);
    
    // Shipping ve Billing Address
    $address = new \Iyzipay\Model\Address();
    $address->setContactName($name . " " . $surname);
    $address->setCity("Istanbul");
    $address->setCountry("Turkey");
    $address->setAddress("Adres Bilgisi");
    $address->setZipCode("34398");
    $request->setShippingAddress($address);
    $request->setBillingAddress($address);
    
    // Basket Items
    $basketItems = [];
    $firstBasketItem = new \Iyzipay\Model\BasketItem();
    $firstBasketItem->setId("ITEM_" . uniqid());
    $firstBasketItem->setName("Dijital Ürün/Hizmet");
    $firstBasketItem->setCategory1("Dijital");
    $firstBasketItem->setCategory2("Hizmet");
    $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
    $firstBasketItem->setPrice($amount);
    $basketItems[] = $firstBasketItem;
    
    $request->setBasketItems($basketItems);
    
    // Ödeme formunu oluştur
    $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);
    
    if ($checkoutFormInitialize->getStatus() === 'success') {
        // Ödeme kaydını veritabanına ekle
        $db = getDatabaseConnection();
        $stmt = $db->prepare("INSERT INTO payments (user_id, conversation_id, amount, status, buyer_name, buyer_surname, buyer_phone, buyer_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $conversationId,
            $amount,
            'pending',
            $name,
            $surname,
            $phone,
            $email
        ]);
        
        // Log kaydet
        logAction($_SESSION['user_id'], 'payment_initiated', 'Ödeme başlatıldı: ' . $amount . ' TL');
        
        // İyzico ödeme formunu göster
        echo $checkoutFormInitialize->getCheckoutFormContent();
    } else {
        $errorMessage = $checkoutFormInitialize->getErrorMessage();
        
        // Hatayı logla
        logAction($_SESSION['user_id'], 'payment_error', 'Ödeme formu hatası: ' . $errorMessage);
        
        echo '<!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Hata - ' . SITE_NAME . '</title>
            <link rel="stylesheet" href="assets/css/main.css">
        </head>
        <body>
            <div class="error-container">
                <div class="error-card">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h2>Ödeme Formu Oluşturulamadı</h2>
                    <p>' . htmlspecialchars($errorMessage) . '</p>
                    <a href="dashboard.php" class="btn btn-primary">Panele Dön</a>
                </div>
            </div>
        </body>
        </html>';
    }
} catch (Exception $e) {
    logAction($_SESSION['user_id'] ?? null, 'payment_exception', 'Ödeme işlemi exception: ' . $e->getMessage());
    
    echo '<!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hata - ' . SITE_NAME . '</title>
        <link rel="stylesheet" href="assets/css/main.css">
    </head>
    <body>
        <div class="error-container">
            <div class="error-card">
                <i class="fas fa-exclamation-triangle"></i>
                <h2>Beklenmeyen Bir Hata Oluştu</h2>
                <p>Lütfen daha sonra tekrar deneyin.</p>
                <a href="dashboard.php" class="btn btn-primary">Panele Dön</a>
            </div>
        </div>
    </body>
    </html>';
}
