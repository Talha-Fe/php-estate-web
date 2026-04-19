<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$stmt = $pdo->query("
    SELECT 
        property_messages.*,
        properties.title AS property_title
    FROM property_messages
    INNER JOIN properties ON property_messages.property_id = properties.id
    ORDER BY property_messages.created_at DESC
");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesajlar - Admin</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
</head>
<body class="admin-body">

<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-brand">
            <div class="admin-sidebar-badge">MD</div>
            <div>
                <strong>MD Gayrimenkul</strong>
                <small>Yönetim Paneli</small>
            </div>
        </div>

        <nav class="admin-sidebar-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="add-property.php">Yeni İlan Ekle</a>
            <a href="messages.php" class="active">Mesajlar</a>
            <a href="../properties.php" target="_blank">Siteyi Gör</a>
            <a href="logout.php" class="danger">Çıkış Yap</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar-modern">
            <div>
                <h1>Gelen Mesajlar</h1>
                <p>Kullanıcıların ilanlar için gönderdiği mesajları buradan okuyabilirsin.</p>
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
                                    href="delete-message.php?id=<?php echo $msg['id']; ?>"
                                    class="admin-btn admin-btn-danger admin-message-delete"
                                    onclick="return confirm('Bu mesajı silmek istediğine emin misin?')"
                                >
                                    Sil
                                </a>
                            </div>

                            <p class="admin-message-property">
                                <strong>İlan:</strong>
                                <?php echo htmlspecialchars($msg['property_title']); ?>
                            </p>

                            <p><strong>Telefon:</strong> <?php echo htmlspecialchars($msg['phone']); ?></p>

                            <div class="admin-message-box">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="admin-empty-state">
                    <h3>Henüz mesaj yok</h3>
                    <p>Kullanıcılardan gelen mesajlar burada görünecek.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>