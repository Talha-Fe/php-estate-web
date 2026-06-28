<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$pdo->exec("
    CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(150) NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim Mesajları - Admin</title>
    <link rel="icon" type="image/png" href="../logo.png">
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
</head>
<body class="admin-body">

<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-brand">
            <img class="admin-sidebar-badge" src="../logo.png" alt="Melek Demir Gayrimenkul logo">
            <div>
                <strong>Melek Demir Gayrimenkul</strong>
                <small>Yönetim Paneli</small>
            </div>
        </div>

        <nav class="admin-sidebar-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="add-property.php">Yeni İlan Ekle</a>
            <a href="messages.php">İlan Mesajları</a>
            <a href="contact-messages.php" class="active">İletişim Mesajları</a>
            <a href="../properties.php" target="_blank">Siteyi Gör</a>
            <a href="logout.php" class="danger">Çıkış Yap</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar-modern">
            <div>
                <h1>İletişim Mesajları</h1>
                <p>İletişim sayfasındaki formdan gönderilen mesajları buradan okuyabilirsin.</p>
            </div>
        </div>

        <section class="admin-table-card">
            <?php if (count($messages) > 0): ?>
                <div class="admin-messages-grid">
                    <?php foreach ($messages as $msg): ?>
                        <div class="admin-message-card">
                            <div class="admin-message-top">
                                <div>
                                    <h3><?php echo htmlspecialchars($msg['name']); ?></h3>
                                    <span class="admin-message-date">
                                        <?php echo date('d.m.Y H:i', strtotime($msg['created_at'])); ?>
                                    </span>
                                </div>

                                <a
                                    href="delete-contact-message.php?id=<?php echo $msg['id']; ?>"
                                    class="admin-btn admin-btn-danger admin-message-delete"
                                    onclick="return confirm('Bu mesajı silmek istediğine emin misin?')"
                                >
                                    Sil
                                </a>
                            </div>

                            <p><strong>Telefon:</strong> <?php echo htmlspecialchars($msg['phone']); ?></p>
                            <?php if (!empty($msg['email'])): ?>
                                <p><strong>E-posta:</strong> <?php echo htmlspecialchars($msg['email']); ?></p>
                            <?php endif; ?>

                            <div class="admin-message-box">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="admin-empty-state">
                    <h3>Henüz mesaj yok</h3>
                    <p>İletişim sayfasından gelen mesajlar burada görünecek.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>
