<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$imageId = (int)($_GET['id'] ?? 0);
$propertyId = (int)($_GET['property_id'] ?? 0);

if ($imageId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM property_images WHERE id = :id");
    $stmt->execute(['id' => $imageId]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($image) {
        $filePath = '../uploads/' . $image['image_name'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $deleteStmt = $pdo->prepare("DELETE FROM property_images WHERE id = :id");
        $deleteStmt->execute(['id' => $imageId]);
    }
}

header('Location: edit-property.php?id=' . $propertyId);
exit;