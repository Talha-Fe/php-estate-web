<?php
require_once 'config/db.php';
include 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC LIMIT 6");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero">
    <div class="container">
        <div class="hero-box">
            <div class="hero-content">
                <h1>Hayalindeki evi kolayca bul</h1>
                <p>
                    Satılık ve kiralık daire, arsa, ofis ve müstakil ev ilanlarını modern arayüzle incele.
                    Güvenilir portföy, şık sunum, hızlı erişim.
                </p>

                <form action="properties.php" method="GET" class="search-panel">
                    <div class="search-grid">
                        <div class="search-group">
                            <label>İlan Türü</label>
                            <select name="type">
                                <option value="">Tümü</option>
                                <option value="Daire">Daire</option>
                                <option value="Arsa">Arsa</option>
                                <option value="Bina">Bina</option>
                                <option value="Müstakil Ev">Müstakil Ev</option>
                                <option value="Dükkan">Dükkan</option>
                                <option value="Ofis">Ofis</option>
                            </select>
                        </div>

                        <div class="search-group">
                            <label>Durum</label>
                            <select name="status">
                                <option value="">Tümü</option>
                                <option value="Satılık">Satılık</option>
                                <option value="Kiralık">Kiralık</option>
                            </select>
                        </div>

                        <div class="search-group">
                            <label>Konum</label>
                            <input type="text" name="location" placeholder="İl / İlçe / Mahalle">
                        </div>

                        <div class="search-group">
                            <label>Min Fiyat</label>
                            <input type="number" name="min_price" placeholder="0">
                        </div>

                        <button type="submit" class="search-submit">İlan Ara</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="featured-properties">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Öne Çıkan İlanlar</h2>
                <p>En yeni ve dikkat çeken portföyler burada.</p>
            </div>
        </div>

        <div class="property-showcase">
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

                    <a href="property-detail.php?id=<?php echo $property['id']; ?>" class="property-card">

                        <div class="property-image">
                            <?php if ($firstImage): ?>
                                <img
                                    src="uploads/<?php echo htmlspecialchars($firstImage['image_name']); ?>"
                                    alt="<?php echo htmlspecialchars($property['title']); ?>"
                                >
                            <?php else: ?>
                                <img
                                    src="https://via.placeholder.com/400x300?text=Gorsel+Yok"
                                    alt="Görsel yok"
                                >
                            <?php endif; ?>

                            <span class="badge status">
                                <?php echo htmlspecialchars($property['status']); ?>
                            </span>
                        </div>

                        <div class="property-content">
                            <h3><?php echo htmlspecialchars($property['title']); ?></h3>

                            <p class="location">
                                <?php echo htmlspecialchars($property['location']); ?>
                            </p>

                            <div class="meta">
                                <span><?php echo (int)$property['area']; ?> m²</span>
                                <span><?php echo htmlspecialchars($property['rooms'] ?? '-'); ?></span>
                                <span><?php echo htmlspecialchars($property['type']); ?></span>
                            </div>

                            <div class="property-bottom">
                                <strong><?php echo number_format($property['price'], 0, ',', '.'); ?> TL</strong>
                                <span>Detayları Gör</span>
                            </div>
                        </div>

                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Henüz ilan bulunmuyor</h3>
                    <p>Sisteme eklenmiş bir ilan yok.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>