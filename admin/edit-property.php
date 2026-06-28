<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = :id");
$stmt->execute(['id' => $id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    die('İlan bulunamadı');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $price = $_POST['price'] ?? 0;
    $location = trim($_POST['location'] ?? '');
    $area = $_POST['area'] ?? 0;
    $rooms = trim($_POST['rooms'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $update = $pdo->prepare("
        UPDATE properties 
        SET title = :title,
            type = :type,
            price = :price,
            location = :location,
            area = :area,
            rooms = :rooms,
            status = :status,
            description = :description
        WHERE id = :id
    ");

    $update->execute([
        'title' => $title,
        'type' => $type,
        'price' => $price,
        'location' => $location,
        'area' => $area,
        'rooms' => $rooms,
        'status' => $status,
        'description' => $description,
        'id' => $id
    ]);

    if (!empty($_FILES['images']['name'][0])) {
        $sortStmt = $pdo->prepare("
            SELECT COALESCE(MAX(sort_order), -1) AS max_sort
            FROM property_images
            WHERE property_id = :property_id
        ");
        $sortStmt->execute(['property_id' => $id]);
        $maxSort = (int)$sortStmt->fetch(PDO::FETCH_ASSOC)['max_sort'];

        $imageStmt = $pdo->prepare("
            INSERT INTO property_images (property_id, image_name, sort_order)
            VALUES (:property_id, :image_name, :sort_order)
        ");

        foreach ($_FILES['images']['name'] as $index => $originalName) {
            if ($_FILES['images']['error'][$index] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['images']['tmp_name'][$index];
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                    continue;
                }

                $newName = time() . '_' . $index . '_' . uniqid() . '.' . $extension;

                if (move_uploaded_file($tmpName, '../uploads/' . $newName)) {
                    $imageStmt->execute([
                        'property_id' => $id,
                        'image_name' => $newName,
                        'sort_order' => $maxSort + $index + 1
                    ]);
                }
            }
        }
    }

    header('Location: edit-property.php?id=' . $id);
    exit;
}

$imageStmt = $pdo->prepare("
    SELECT *
    FROM property_images
    WHERE property_id = :property_id
    ORDER BY sort_order ASC, id ASC
");
$imageStmt->execute(['property_id' => $id]);
$images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İlan Düzenle - Admin</title>
    <link rel="icon" type="image/png" href="../logo.png">
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
</head>
<body class="admin-body">

<div class="admin-form-page">
    <div class="admin-form-top">
        <div>
            <h1>İlan Düzenle</h1>
            <p>İlan bilgilerini güncelle, mevcut görselleri sil veya yeni görseller ekle.</p>
        </div>
        <div class="admin-topbar-actions">
            <a href="dashboard.php" class="admin-btn admin-btn-soft">← Dashboard</a>
        </div>
    </div>

    <div class="admin-form-card">
        <form method="POST" enctype="multipart/form-data" class="admin-form-grid">
            <div class="form-group">
                <label for="title">İlan Başlığı</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="type">Tür</label>
                <select id="type" name="type" required>
                    <option value="Daire" <?php if ($property['type'] === 'Daire') echo 'selected'; ?>>Daire</option>
                    <option value="Arsa" <?php if ($property['type'] === 'Arsa') echo 'selected'; ?>>Arsa</option>
                    <option value="Bina" <?php if ($property['type'] === 'Bina') echo 'selected'; ?>>Bina</option>
                    <option value="Müstakil Ev" <?php if ($property['type'] === 'Müstakil Ev') echo 'selected'; ?>>Müstakil Ev</option>
                    <option value="Dükkan" <?php if ($property['type'] === 'Dükkan') echo 'selected'; ?>>Dükkan</option>
                    <option value="Ofis" <?php if ($property['type'] === 'Ofis') echo 'selected'; ?>>Ofis</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Fiyat</label>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($property['price']); ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Konum</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required>
            </div>

            <div class="form-group">
                <label for="area">Metrekare</label>
                <input type="number" id="area" name="area" value="<?php echo htmlspecialchars($property['area']); ?>" required>
            </div>

            <div class="form-group">
                <label for="rooms">Oda Sayısı</label>
                <input type="text" id="rooms" name="rooms" value="<?php echo htmlspecialchars($property['rooms']); ?>">
            </div>

            <div class="form-group">
                <label for="status">Durum</label>
                <select id="status" name="status" required>
                    <option value="Satılık" <?php if ($property['status'] === 'Satılık') echo 'selected'; ?>>Satılık</option>
                    <option value="Kiralık" <?php if ($property['status'] === 'Kiralık') echo 'selected'; ?>>Kiralık</option>
                </select>
            </div>

            <div class="form-group">
                <label for="images">Yeni Görseller Ekle</label>
                <input type="file" id="images" name="images[]" multiple accept="image/*">
            </div>

            <div class="form-group full">
                <label for="description">Açıklama</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($property['description']); ?></textarea>
            </div>

            <div class="form-group full">
                <label>Mevcut Görseller</label>

                <?php if (!empty($images)): ?>
                    <div class="admin-image-grid">
                        <?php foreach ($images as $img): ?>
                            <div class="admin-image-item">
                                <img
                                    src="../uploads/<?php echo htmlspecialchars($img['image_name']); ?>"
                                    alt="İlan görseli"
                                >
                                <a
                                    href="delete-property-image.php?id=<?php echo $img['id']; ?>&property_id=<?php echo $property['id']; ?>"
                                    class="admin-btn admin-btn-danger admin-image-delete"
                                    onclick="return confirm('Bu görseli silmek istediğine emin misin?')"
                                >
                                    Görseli Sil
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Bu ilana ait görsel yok.</p>
                <?php endif; ?>
            </div>

            <div class="admin-form-actions full">
                <a href="dashboard.php" class="admin-btn admin-btn-soft">İptal</a>
                <button type="submit" class="admin-btn admin-btn-primary">Değişiklikleri Kaydet</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>