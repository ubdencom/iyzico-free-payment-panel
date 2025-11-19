<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yasal Bilgilendirme - İyzico Bayi Ödeme Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-secondary);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        header {
            text-align: center;
            margin-bottom: 50px;
            padding-bottom: 30px;
            border-bottom: 2px solid var(--border-color);
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .logo i {
            font-size: 40px;
            color: white;
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 16px;
            color: var(--text-secondary);
        }

        .content-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        h2 i {
            color: var(--primary-color);
        }

        h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 25px 0 15px;
        }

        p {
            margin-bottom: 15px;
            color: var(--text-secondary);
            font-size: 15px;
        }

        ul, ol {
            margin-left: 25px;
            margin-bottom: 15px;
        }

        li {
            margin-bottom: 10px;
            color: var(--text-secondary);
            font-size: 15px;
        }

        .warning-box {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }

        .warning-box h3 {
            color: #92400e;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .warning-box p {
            color: #92400e;
            margin-bottom: 0;
        }

        .info-box {
            background: #dbeafe;
            border: 2px solid #3b82f6;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }

        .info-box h3 {
            color: #1e40af;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-box p, .info-box li {
            color: #1e40af;
        }

        .success-box {
            background: #d1fae5;
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }

        .success-box h3 {
            color: #065f46;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .success-box p, .success-box li {
            color: #065f46;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        footer {
            text-align: center;
            padding: 30px 0;
            color: var(--text-secondary);
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .content-card {
                padding: 25px;
            }

            h1 {
                font-size: 26px;
            }

            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <i class="fas fa-file-contract"></i>
            </div>
            <h1>Yasal Bilgilendirme ve Kullanım Koşulları</h1>
            <p class="subtitle">İyzico Bayi Ödeme Paneli</p>
            <p class="subtitle">Son Güncelleme: <?php echo date('d.m.Y'); ?></p>
        </header>

        <div class="content-card">
            <h2><i class="fas fa-info-circle"></i> Genel Bilgilendirme</h2>
            <p>
                Bu İyzico Bayi Ödeme Paneli, bayilerin müşterilerinden serbest tutar ile ödeme alabilmelerini 
                sağlayan bir yazılım çözümüdür. Panel kullanımı ve sunulan hizmetler aşağıdaki şartlara tabidir.
            </p>
        </div>

        <div class="content-card">
            <h2><i class="fas fa-handshake"></i> Sözleşme ve Taraflar</h2>
            
            <h3>1. Taraflar ve İlişki</h3>
            <p>
                İyzico Bayi Ödeme Platformu, <strong>bayi</strong> ile <strong>panel sağlayıcısı</strong> 
                arasında yapılacak sözleşmeye tabidir. Bu platform üzerinden gerçekleştirilecek tüm 
                işlemler için geçerli bir sözleşme akdedilmesi zorunludur.
            </p>

            <div class="info-box">
                <h3><i class="fas fa-users"></i> Taraflar</h3>
                <ul>
                    <li><strong>Panel Sağlayıcısı:</strong> Yazılım ve altyapı hizmeti sunan taraf</li>
                    <li><strong>Bayi:</strong> Paneli kullanarak son kullanıcılardan ödeme alan taraf</li>
                    <li><strong>Son Kullanıcı:</strong> Bayiye ödeme yapan müşteri</li>
                </ul>
            </div>

            <h3>2. Sorumluluklar</h3>
            <ul>
                <li>Son kullanıcı ile yapılan tüm işlemler <strong>bayiye aittir</strong></li>
                <li>Bayi, panel sağlayıcısına <strong>doğru bilgi verme zorunluluğu</strong> vardır</li>
                <li>Bayi, müşteri bilgilerinin gizliliğinden sorumludur</li>
                <li>Panel sağlayıcısı, bayinin yaptığı işlemlerden sorumlu tutulamaz</li>
            </ul>
        </div>

        <div class="content-card">
            <h2><i class="fas fa-exchange-alt"></i> Virman İşlemleri</h2>
            
            <h3>3. Hesaplar Arası Virman</h3>
            <p>
                Sözleşme dahilinde olmak kaydıyla, aşağıdaki virman işlemleri yapılabilir:
            </p>

            <div class="success-box">
                <h3><i class="fas fa-arrow-right"></i> Standart Virman (Bayi Alacağı Karşılığı)</h3>
                <p>
                    Panel sağlayıcısı, bayi alacağı karşılığında, bayinin müşterisinin POS ödemesi 
                    ile hesaplar arası virman yapabilir. Bu işlem, bayi ile panel sağlayıcısı 
                    arasındaki mali ilişkiyi düzenler.
                </p>
            </div>

            <div class="info-box">
                <h3><i class="fas fa-arrow-left"></i> Ters Virman (Hizmet Bedeli Karşılığı)</h3>
                <p>
                    Panel sağlayıcı, harici serbest ödeme yazılımı kullanımı için bedel karşılığında 
                    bayiye ters virman yapabilir. Bu işlem, yazılım kullanım hakkı veya diğer hizmet 
                    bedelleri için gerçekleştirilir.
                </p>
            </div>

            <h3>4. Virman Koşulları</h3>
            <ul>
                <li>Tüm virman işlemleri yazılı sözleşmeye dayanmalıdır</li>
                <li>İşlemler kayıt altında tutulmalıdır</li>
                <li>Finansal raporlama yapılmalıdır</li>
                <li>Yasal mevzuata uygun olmalıdır</li>
            </ul>
        </div>

        <div class="content-card">
            <h2><i class="fas fa-shield-alt"></i> Yasal Sorumluluk</h2>

            <div class="warning-box">
                <h3><i class="fas fa-exclamation-triangle"></i> Önemli Uyarı</h3>
                <p>
                    <strong>Yasal sorumluluk paneli kullanan bayiye aittir.</strong> 
                    Bayi, platformu kullanarak gerçekleştirdiği tüm işlemlerden ve bu işlemlerin 
                    yasal sonuçlarından tamamen sorumludur.
                </p>
            </div>

            <h3>5. Sorumluluk Reddi</h3>
            <p>
                Ödeme Panel Yazılımı sağlayıcısı, yapılan işlemlerden doğacak hiçbir sonuçtan 
                sorumlu tutulamaz. Bu sorumluluk reddi şunları kapsar ancak bunlarla sınırlı değildir:
            </p>
            <ul>
                <li>Bayinin müşterileri ile yaptığı ticari işlemler</li>
                <li>Ödeme uyuşmazlıkları ve iade talepleri</li>
                <li>Müşteri şikayetleri ve hukuki süreçler</li>
                <li>Vergi yükümlülükleri ve mali raporlama</li>
                <li>Kişisel verilerin korunması (KVKK) sorumlulukları</li>
                <li>İyzico veya diğer ödeme sistemleri ile yaşanan sorunlar</li>
            </ul>

            <h3>6. Kullanıcı Kabulü</h3>
            <p>
                Panel kullanıcıları (bayiler), bu platformu kullanmaya başladıklarında 
                yukarıda belirtilen tüm şart ve sorumlulukları kabul etmiş sayılırlar.
            </p>
        </div>

        <div class="content-card">
            <h2><i class="fas fa-lock"></i> Güvenlik ve Gizlilik</h2>
            
            <h3>7. Veri Güvenliği</h3>
            <ul>
                <li>Tüm ödeme işlemleri İyzico güvenli altyapısı üzerinden gerçekleştirilir</li>
                <li>Kredi kartı bilgileri sistemimizde saklanmaz</li>
                <li>Kullanıcı şifreleri güvenli şekilde şifrelenerek saklanır</li>
                <li>İşlem logları güvenlik amaçlı kaydedilir</li>
            </ul>

            <h3>8. KVKK Uyumu</h3>
            <p>
                Bayi, topladığı müşteri bilgilerini 6698 sayılı Kişisel Verilerin Korunması 
                Kanunu'na uygun olarak işlemekle yükümlüdür.
            </p>
        </div>

        <div class="content-card">
            <h2><i class="fas fa-book"></i> Kullanım Koşulları</h2>
            
            <h3>9. İzin Verilen Kullanım</h3>
            <ul>
                <li>Panel sadece yasal ticari faaliyetler için kullanılabilir</li>
                <li>Doğru ve güncel bilgiler girilmelidir</li>
                <li>Hesap güvenliği kullanıcının sorumluluğundadır</li>
            </ul>

            <h3>10. Yasak Faaliyetler</h3>
            <ul>
                <li>Hileli veya yanıltıcı işlemler yapmak</li>
                <li>Yasadışı ürün veya hizmet ödemeleri almak</li>
                <li>Başkasının bilgilerini izinsiz kullanmak</li>
                <li>Sistem güvenliğini tehlikeye atmak</li>
            </ul>
        </div>

        <div class="content-card">
            <h2><i class="fas fa-phone"></i> İletişim ve Destek</h2>
            <p>
                Bu yasal bilgilendirme veya platform kullanımı hakkında sorularınız için 
                lütfen panel sağlayıcınız ile iletişime geçin.
            </p>
            
            <div class="info-box">
                <h3><i class="fas fa-info-circle"></i> Not</h3>
                <p>
                    Bu koşullar herhangi bir zamanda güncellenebilir. Önemli değişiklikler 
                    hakkında kullanıcılar bilgilendirilecektir.
                </p>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="dashboard.php" class="btn">
                <i class="fas fa-arrow-left"></i>
                Panele Dön
            </a>
        </div>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> İyzico Bayi Ödeme Paneli. Tüm hakları saklıdır.</p>
            <p style="font-size: 12px; margin-top: 10px;">
                Bu platform İyzico tarafından sağlanmamaktadır. İyzico ödeme altyapısı kullanılmaktadır.
            </p>
            <hr style="border: none; border-top: 1px solid var(--border-color); margin: 20px 0;">
            <p style="font-size: 13px; color: var(--text-secondary);">
                Made and Powered by - <a href="https://ubden.com/" target="_blank" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Ubden® Community Platform</a>
            </p>
        </footer>
    </div>
</body>
</html>

