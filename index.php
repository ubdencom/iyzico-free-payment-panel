<?php
require_once 'config.php';

session_start();

// Eğer kullanıcı zaten giriş yapmışsa dashboard'a yönlendir
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

// Login sayfasına yönlendir
header("Location: login.php");
exit;
