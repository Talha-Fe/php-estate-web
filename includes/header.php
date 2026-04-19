<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MD Gayrimenkul</title>
    <link rel="stylesheet" href="/style.css?v=<?php echo time(); ?>">
</head>
<body>

<header class="site-header">
    <div class="container navbar">
        <a href="/index.php" class="logo">
            <span class="logo-badge">MD</span>
            <div class="logo-text">
                <strong>MD Gayrimenkul</strong>
                <small>Güvenilir Emlak Çözümleri</small>
            </div>
        </a>

        <nav class="nav-links">
            <a href="/index.php">Ana Sayfa</a>
            <a href="/properties.php">İlanlar</a>
            <a href="/admin/login.php" class="nav-admin">Admin</a>
        </nav>
    </div>
</header>

<main class="site-main">
    <div class="container">