<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melek Demir Gayrimenkul</title>
    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="stylesheet" href="/style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="topbar">
    <div class="container topbar-inner">
        <div class="topbar-contact">
            <span>📞 0538 414 79 05</span>
            <span>✉️ melekdemirgayrimenkul@gmail.com</span>
            <span>📷 @melek_demir_gayrimenkul</span>
        </div>
        <div class="topbar-note">Kurumsal Gayrimenkul Danışmanlığı</div>
    </div>
</div>

<header class="site-header">
    <div class="container navbar">
        <a href="/index.php" class="logo">
            <img class="logo-badge" src="/logo.png" alt="Melek Demir Gayrimenkul logo">
            <div class="logo-text">
                <strong>Melek Demir Gayrimenkul</strong>
                <small>Güvenilir Emlak Çözümleri</small>
            </div>
        </a>

        <nav class="nav-links">
            <a href="/index.php">Ana Sayfa</a>
            <a href="/properties.php">İlanlar</a>
            <a href="/contact.php">İletişim</a>
            <?php if(isset($_SESSION['admin_id'])): ?>
                <a href="/admin/dashboard.php" class="nav-admin">Admin</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="site-main">
    <div class="container">