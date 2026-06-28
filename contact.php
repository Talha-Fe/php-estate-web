<?php
require_once 'config/db.php';
include 'includes/header.php';

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

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $phone === '' || $message === '') {
        $errorMessage = 'Lütfen zorunlu alanları doldurun.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages (name, phone, email, message)
            VALUES (:name, :phone, :email, :message)
        ");

        $stmt->execute([
            'name' => $name,
            'phone' => $phone,
            'email' => $email ?: null,
            'message' => $message
        ]);

        $successMessage = 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.';
    }
}
?>

<section class="contact-page">
    <div class="container">

        <div class="section-head">
            <div>
                <h2>İletişim</h2>
                <p>Sorularınız için bize ulaşın, danışmanlarımız size yardımcı olmaktan mutluluk duyacaktır.</p>
            </div>
        </div>

        <div class="contact-layout">

            <div class="contact-info-card">
                <h3>İletişim Bilgileri</h3>

                <div class="quick-contact">
                    <strong>Adres</strong>
                    <span>Aydınlı Mah. Dersaadet Cad. No:12G, Tuzla/İstanbul</span>
                </div>

                <div class="quick-contact">
                    <strong>Telefon</strong>
                    <span>0538 414 79 05</span>
                </div>

                <div class="quick-contact">
                    <strong>E-posta</strong>
                    <span>melekdemirgayrimenkul@gmail.com</span>
                </div>

                <div class="quick-contact">
                    <strong>Instagram</strong>
                    <span><a href="https://instagram.com/melek_demir_gayrimenkul" target="_blank" rel="noopener">@melek_demir_gayrimenkul</a></span>
                </div>

                <div class="quick-contact">
                    <strong>Çalışma Saatleri</strong>
                    <span>Pazartesi - Cumartesi, 09:00 - 19:00</span>
                </div>

                <div class="contact-map">
                    <iframe
                        src="https://maps.google.com/maps?q=Ayd%C4%B1nl%C4%B1+Mah.+Dersaadet+Cad.+No+12G+Tuzla+%C4%B0stanbul&output=embed"
                        width="100%"
                        height="260"
                        style="border:0;"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            </div>

            <div class="contact-form-card">
                <h3>Bize Yazın</h3>
                <p>Formu doldurun, talebinizle ilgili size dönüş yapalım.</p>

                <?php if ($successMessage): ?>
                    <div class="form-success-message"><?php echo htmlspecialchars($successMessage); ?></div>
                <?php endif; ?>

                <?php if ($errorMessage): ?>
                    <div class="form-error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>

                <form class="contact-form" method="POST">
                    <input type="text" name="name" placeholder="Ad Soyad" required>
                    <input type="tel" name="phone" placeholder="Telefon Numaranız" required>
                    <input type="email" name="email" placeholder="E-posta (opsiyonel)">
                    <textarea name="message" placeholder="Mesajınız" required></textarea>
                    <button type="submit" class="primary-btn">Mesaj Gönder</button>
                </form>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
