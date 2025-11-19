<?php
require_once 'config.php';

session_start();

// Kullanıcı çıkış işlemi
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Çıkış işlemini logla
    logAction($userId, 'user_logout', 'Kullanıcı çıkış yaptı');
}

// Session'ı temizle
session_unset();
session_destroy();

// Yeni session başlat ve mesaj ekle
session_start();
$_SESSION['logout_message'] = 'Başarıyla çıkış yaptınız.';

// Login sayfasına yönlendir
header("Location: login.php");
exit;

