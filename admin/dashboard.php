<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$stmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalProperties = count($properties);
$saleCount = 0;
$rentCount = 0;

foreach ($properties as $property) {
    if (($property['status'] ?? '') === 'Satılık') {
        $saleCount++;
    }
    if (($property['status'] ?? '') === 'Kiralık') {
        $rentCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - MD Gayrimenkul</title>
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
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="add-property.php">Yeni İlan Ekle</a>
            <a href="messages.php">Mesajlar</a>
            <a href="../properties.php" target="_blank">Siteyi Gör</a>
            <a href="logout.php" class="danger">Çıkış Yap</a>
            
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar-modern">
            <div>
                <h1>Admin Paneli</h1>
                <p>Hoş geldin, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>.</p>
            </div>

            <div class="admin-topbar-actions">
                <a href="add-property.php" class="admin-btn admin-btn-primary">+ Yeni İlan Ekle</a>
                <a href="logout.php" class="admin-btn admin-btn-dark">Çıkış Yap</a>
            </div>
        </div>

        <section class="admin-stats">
            <div class="admin-stat-card">
                <span>Toplam İlan</span>
                <strong><?php echo $totalProperties; ?></strong>
            </div>

            <div class="admin-stat-card">
                <span>Satılık</span>
                <strong><?php echo $saleCount; ?></strong>
            </div>

            <div class="admin-stat-card">
                <span>Kiralık</span>
                <strong><?php echo $rentCount; ?></strong>
            </div>
        </section>

        <section class="admin-table-card">
            <div class="admin-section-head">
                <div>
                    <h2>İlan Listesi</h2>
                    <p>Tüm ilanları buradan düzenleyebilir veya silebilirsin.</p>
                </div>
            </div>

            <?php if (count($properties) > 0): ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İlan</th>
                                <th>Tür</th>
                                <th>Durum</th>
                                <th>Fiyat</th>
                                <th>Konum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties as $property): ?>
                                <tr>
                                    <td>#<?php echo $property['id']; ?></td>
                                    <td>
                                        <div class="admin-property-cell">
                                            <div class="admin-property-thumb">
                                                <?php if (!empty($property['image'])): ?>
                                                    <img src="../uploads/<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                                                <?php else: ?>
                                                    <div class="admin-no-image">Görsel Yok</div>
                                                <?php endif; ?>
                                            </div>

                                            <div>
                                                <strong><?php echo htmlspecialchars($property['title']); ?></strong>
                                                <small>
                                                    <?php echo !empty($property['created_at']) ? date('d.m.Y', strtotime($property['created_at'])) : 'Tarih yok'; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($property['type']); ?></td>
                                    <td>
                                        <span class="admin-status-badge <?php echo ($property['status'] ?? '') === 'Kiralık' ? 'rent' : 'sale'; ?>">
                                            <?php echo htmlspecialchars($property['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($property['price'], 0, ',', '.'); ?> TL</td>
                                    <td><?php echo htmlspecialchars($property['location']); ?></td>
                                    <td>
                                        <div class="admin-action-group">
                                            <a class="admin-btn admin-btn-soft" href="edit-property.php?id=<?php echo $property['id']; ?>">Düzenle</a>
                                            <a class="admin-btn admin-btn-danger" href="delete-property.php?id=<?php echo $property['id']; ?>" onclick="return confirm('Silmek istediğine emin misin?')">Sil</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="admin-empty-state">
                    <h3>Henüz ilan yok</h3>
                    <p>İlk ilanı eklemek için aşağıdaki butonu kullan.</p>
                    <a href="add-property.php" class="admin-btn admin-btn-primary">İlk İlanı Ekle</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>