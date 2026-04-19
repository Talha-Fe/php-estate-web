<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: dashboard.php');
    exit;
}

/* İlan var mı kontrol et */
$checkStmt = $pdo->prepare("SELECT id FROM properties WHERE id = :id");
$checkStmt->execute(['id' => $id]);
$property = $checkStmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    header('Location: dashboard.php');
    exit;
}

/* İlana ait tüm görselleri bul */
$imageStmt = $pdo->prepare("
    SELECT image_name
    FROM property_images
    WHERE property_id = :property_id
");
$imageStmt->execute(['property_id' => $id]);
$images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

/* Uploads klasöründeki dosyaları sil */
foreach ($images as $image) {
    if (!empty($image['image_name'])) {
        $filePath = '../uploads/' . $image['image_name'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}

/* Önce resim kayıtlarını sil */
$deleteImagesStmt = $pdo->prepare("
    DELETE FROM property_images
    WHERE property_id = :property_id
");
$deleteImagesStmt->execute(['property_id' => $id]);

/* Sonra ilanı sil */
$deletePropertyStmt = $pdo->prepare("
    DELETE FROM properties
    WHERE id = :id
");
$deletePropertyStmt->execute(['id' => $id]);

header('Location: dashboard.php');
exit;