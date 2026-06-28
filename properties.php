<?php
require_once 'config/db.php';
include 'includes/header.php';

$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';
$location = $_GET['location'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

$sql = "SELECT * FROM properties WHERE 1=1";
$params = [];

if (!empty($type)) {
    $sql .= " AND type = :type";
    $params['type'] = $type;
}

if (!empty($status)) {
    $sql .= " AND status = :status";
    $params['status'] = $status;
}

if (!empty($location)) {
    $sql .= " AND location LIKE :location";
    $params['location'] = '%' . $location . '%';
}

if (!empty($min_price)) {
    $sql .= " AND price >= :min_price";
    $params['min_price'] = $min_price;
}

if (!empty($max_price)) {
    $sql .= " AND price <= :max_price";
    $params['max_price'] = $max_price;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="listing-page">
    <div class="container">

        <div class="section-head">
            <div>
                <h2>Tüm Emlak İlanları</h2>
                <p>Satılık ve kiralık daire, arsa, ofis, dükkan ve müstakil ev ilanlarını inceleyin.</p>
            </div>
        </div>

        <div class="listing-layout">

            <aside class="filters-card">
                <h3>Filtrele</h3>

                <form method="GET" class="filters-form">
                    <div class="form-group">
                        <label for="type">İlan Türü</label>
                        <select name="type" id="type">
                            <option value="">Tümü</option>
                            <option value="Daire" <?= $type === 'Daire' ? 'selected' : '' ?>>Daire</option>
                            <option value="Arsa" <?= $type === 'Arsa' ? 'selected' : '' ?>>Arsa</option>
                            <option value="Bina" <?= $type === 'Bina' ? 'selected' : '' ?>>Bina</option>
                            <option value="Müstakil Ev" <?= $type === 'Müstakil Ev' ? 'selected' : '' ?>>Müstakil Ev</option>
                            <option value="Dükkan" <?= $type === 'Dükkan' ? 'selected' : '' ?>>Dükkan</option>
                            <option value="Ofis" <?= $type === 'Ofis' ? 'selected' : '' ?>>Ofis</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Durum</label>
                        <select name="status" id="status">
                            <option value="">Tümü</option>
                            <option value="Satılık" <?= $status === 'Satılık' ? 'selected' : '' ?>>Satılık</option>
                            <option value="Kiralık" <?= $status === 'Kiralık' ? 'selected' : '' ?>>Kiralık</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Konum</label>
                        <input
                            type="text"
                            name="location"
                            id="location"
                            placeholder="İl / İlçe / Mahalle"
                            value="<?= htmlspecialchars($location); ?>"
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="min_price">Min Fiyat</label>
                            <input
                                type="number"
                                name="min_price"
                                id="min_price"
                                placeholder="0"
                                value="<?= htmlspecialchars($min_price); ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="max_price">Max Fiyat</label>
                            <input
                                type="number"
                                name="max_price"
                                id="max_price"
                                placeholder="10000000"
                                value="<?= htmlspecialchars($max_price); ?>"
                            >
                        </div>
                    </div>

                    <button type="submit" class="filter-btn">İlanları Göster</button>
                </form>
            </aside>

            <div class="listing-content">
                <div class="results-bar">
                    <div>
                        <strong><?= count($properties); ?></strong> ilan bulundu
                    </div>
                    <div class="results-note">
                        Güncel portföy listesi
                    </div>
                </div>

                <div class="property-list-modern">
                    <?php if (count($properties) > 0): ?>
                        <?php foreach ($properties as $property): ?>
                            <?php
                            $imageStmt = $pdo->prepare("
                                SELECT image_name
                                FROM property_images
                                WHERE property_id = :property_id
                                ORDER BY sort_order ASC, id ASC
                                LIMIT 1
                            ");
                            $imageStmt->execute(['property_id' => $property['id']]);
                            $firstImage = $imageStmt->fetch(PDO::FETCH_ASSOC);
                            ?>

                            <a href="property-detail.php?id=<?= $property['id']; ?>" class="property-row-card">

                                <div class="property-thumb">
                                    <?php if ($firstImage): ?>
                                        <img
                                            src="uploads/<?= htmlspecialchars($firstImage['image_name']); ?>"
                                            alt="<?= htmlspecialchars($property['title']); ?>"
                                        >
                                    <?php else: ?>
                                        <div class="no-image-box">Görsel Yok</div>
                                    <?php endif; ?>

                                    <span class="tag <?= strtolower($property['status']) === 'kiralık' ? 'rent' : 'sale'; ?>">
                                        <?= htmlspecialchars($property['status']); ?>
                                    </span>
                                </div>

                                <div class="property-main">
                                    <div class="property-main-top">
                                        <div>
                                            <h3><?= htmlspecialchars($property['title']); ?></h3>
                                            <p class="property-location"><?= htmlspecialchars($property['location']); ?></p>
                                        </div>

                                        <div class="property-price">
                                            <?= number_format($property['price'], 0, ',', '.'); ?> TL
                                        </div>
                                    </div>

                                    <div class="property-meta-row">
                                        <span><?= htmlspecialchars($property['type']); ?></span>
                                        <span><?= (int)$property['area']; ?> m²</span>
                                        <span><?= htmlspecialchars($property['rooms'] ?: '-'); ?></span>
                                        <span><?= htmlspecialchars($property['status']); ?></span>
                                    </div>

                                    <p class="property-desc">
                                        <?= htmlspecialchars(mb_strimwidth($property['description'] ?? 'Açıklama bulunmuyor.', 0, 180, '...')); ?>
                                    </p>

                                    <div class="property-bottom-row">
                                        <span class="detail-link">Detayları İncele</span>
                                        <span class="date-text">
                                            <?= !empty($property['created_at']) ? date('d.m.Y', strtotime($property['created_at'])) : ''; ?>
                                        </span>
                                    </div>
                                </div>

                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <h3>İlan bulunamadı</h3>
                            <p>Seçtiğiniz filtrelere uygun ilan yok. Filtreleri değiştirip tekrar deneyin.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>