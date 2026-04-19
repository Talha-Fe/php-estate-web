<?php
require_once 'config/db.php';
include 'includes/header.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = :id");
$stmt->execute(['id' => $id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo '<div class="container" style="padding:40px 0;"><p>İlan bulunamadı.</p></div>';
    include 'includes/footer.php';
    exit;
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $phone === '' || $message === '') {
        $errorMessage = 'Lütfen tüm alanları doldurun.';
    } else {
        $msgStmt = $pdo->prepare("
            INSERT INTO property_messages (property_id, name, phone, message)
            VALUES (:property_id, :name, :phone, :message)
        ");

        $msgStmt->execute([
            'property_id' => $id,
            'name' => $name,
            'phone' => $phone,
            'message' => $message
        ]);

        $successMessage = 'Mesajınız başarıyla gönderildi.';
    }
}

$imageStmt = $pdo->prepare("
    SELECT id, image_name, sort_order
    FROM property_images
    WHERE property_id = :property_id
    ORDER BY sort_order ASC, id ASC
");
$imageStmt->execute(['property_id' => $id]);
$images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="detail-page">
    <div class="container">
        <div class="detail-layout">

            <div class="detail-card">
                <div class="detail-gallery">
                    <?php if (!empty($images)): ?>
                        <div class="detail-slider" data-detail-slider>
                            <div class="detail-main-stage">
                                <?php foreach ($images as $index => $img): ?>
                                    <img
                                        src="uploads/<?php echo htmlspecialchars($img['image_name']); ?>"
                                        alt="<?php echo htmlspecialchars($property['title']); ?>"
                                        class="detail-slide <?php echo $index === 0 ? 'active' : ''; ?>"
                                    >
                                <?php endforeach; ?>

                                <?php if (count($images) > 1): ?>
                                    <button type="button" class="detail-slider-btn prev" onclick="changeDetailSlide(this, -1)">‹</button>
                                    <button type="button" class="detail-slider-btn next" onclick="changeDetailSlide(this, 1)">›</button>
                                <?php endif; ?>
                            </div>

                            <?php if (count($images) > 1): ?>
                                <div class="detail-thumb-grid">
                                    <?php foreach ($images as $index => $img): ?>
                                        <img
                                            src="uploads/<?php echo htmlspecialchars($img['image_name']); ?>"
                                            alt="<?php echo htmlspecialchars($property['title']); ?>"
                                            class="detail-thumb <?php echo $index === 0 ? 'active' : ''; ?>"
                                            onclick="setDetailSlide(this, <?php echo $index; ?>)"
                                        >
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="detail-main-image">
                            <img
                                src="https://via.placeholder.com/900x500?text=Gorsel+Yok"
                                alt="Görsel yok"
                            >
                        </div>
                    <?php endif; ?>
                </div>

                <div class="detail-head">
                    <div>
                        <h1><?php echo htmlspecialchars($property['title']); ?></h1>
                        <p class="detail-location"><?php echo htmlspecialchars($property['location']); ?></p>
                    </div>

                    <div class="detail-price">
                        <?php echo number_format((float)$property['price'], 0, ',', '.'); ?> TL
                    </div>
                </div>

                <div class="detail-badges">
                    <span><?php echo htmlspecialchars($property['type']); ?></span>
                    <span><?php echo htmlspecialchars($property['status']); ?></span>
                    <span><?php echo (int)$property['area']; ?> m²</span>
                    <span><?php echo htmlspecialchars($property['rooms'] ?: '-'); ?> Oda</span>
                </div>

                <div class="detail-section">
                    <h3>İlan Özellikleri</h3>

                    <div class="detail-grid">
                        <div class="info-item">
                            <strong>İlan Türü</strong>
                            <span><?php echo htmlspecialchars($property['type']); ?></span>
                        </div>

                        <div class="info-item">
                            <strong>Durum</strong>
                            <span><?php echo htmlspecialchars($property['status']); ?></span>
                        </div>

                        <div class="info-item">
                            <strong>Metrekare</strong>
                            <span><?php echo (int)$property['area']; ?> m²</span>
                        </div>

                        <div class="info-item">
                            <strong>Oda Sayısı</strong>
                            <span><?php echo htmlspecialchars($property['rooms'] ?: '-'); ?></span>
                        </div>

                        <div class="info-item">
                            <strong>Konum</strong>
                            <span><?php echo htmlspecialchars($property['location']); ?></span>
                        </div>

                        <div class="info-item">
                            <strong>Fiyat</strong>
                            <span><?php echo number_format((float)$property['price'], 0, ',', '.'); ?> TL</span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Açıklama</h3>
                    <div class="detail-description">
                        <?php echo nl2br(htmlspecialchars($property['description'] ?? 'Açıklama girilmemiş.')); ?>
                    </div>
                </div>
            </div>

            <aside class="detail-sidebar">
                <div class="agent-card">
                    <h3>Danışman İletişim</h3>
                    <p>İlan hakkında hızlıca bilgi alın.</p>

                    <div class="quick-contact">
                        <strong>Telefon</strong>
                        <span>0 (5xx) xxx xx xx</span>
                    </div>

                    <div class="quick-contact">
                        <strong>E-posta</strong>
                        <span>info@mdgayrimenkul.com</span>
                    </div>

                    <?php if ($successMessage): ?>
                        <div class="form-success-message"><?php echo htmlspecialchars($successMessage); ?></div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="form-error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>

                    <form class="contact-form" method="POST">
                        <input type="text" name="name" placeholder="Ad Soyad" required>
                        <input type="tel" name="phone" placeholder="Telefon Numaranız" required>
                        <textarea name="message" placeholder="Mesajınız" required></textarea>
                        <button type="submit" class="primary-btn">Mesaj Gönder</button>
                    </form>
                </div>
            </aside>

        </div>
    </div>
</section>

<script>
function changeDetailSlide(button, direction) {
    const slider = button.closest('[data-detail-slider]');
    const slides = slider.querySelectorAll('.detail-slide');
    const thumbs = slider.querySelectorAll('.detail-thumb');

    let activeIndex = 0;

    slides.forEach((slide, index) => {
        if (slide.classList.contains('active')) {
            activeIndex = index;
        }
        slide.classList.remove('active');
    });

    thumbs.forEach(thumb => thumb.classList.remove('active'));

    let newIndex = activeIndex + direction;

    if (newIndex < 0) newIndex = slides.length - 1;
    if (newIndex >= slides.length) newIndex = 0;

    slides[newIndex].classList.add('active');
    if (thumbs[newIndex]) thumbs[newIndex].classList.add('active');
}

function setDetailSlide(thumb, index) {
    const slider = thumb.closest('[data-detail-slider]');
    const slides = slider.querySelectorAll('.detail-slide');
    const thumbs = slider.querySelectorAll('.detail-thumb');

    slides.forEach(slide => slide.classList.remove('active'));
    thumbs.forEach(t => t.classList.remove('active'));

    slides[index].classList.add('active');
    thumbs[index].classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>