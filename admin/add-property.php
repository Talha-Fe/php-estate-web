<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $price = $_POST['price'] ?? 0;
    $location = trim($_POST['location'] ?? '');
    $area = $_POST['area'] ?? 0;
    $rooms = trim($_POST['rooms'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $stmt = $pdo->prepare("
        INSERT INTO properties (title, type, price, location, area, rooms, status, description)
        VALUES (:title, :type, :price, :location, :area, :rooms, :status, :description)
    ");

    $stmt->execute([
        'title' => $title,
        'type' => $type,
        'price' => $price,
        'location' => $location,
        'area' => $area,
        'rooms' => $rooms ?: null,
        'status' => $status,
        'description' => $description
    ]);

    $propertyId = $pdo->lastInsertId();

    if (!empty($_FILES['images']['name'][0])) {
        $imageStmt = $pdo->prepare("
            INSERT INTO property_images (property_id, image_name, sort_order)
            VALUES (:property_id, :image_name, :sort_order)
        ");

        foreach ($_FILES['images']['name'] as $index => $originalName) {
            if ($_FILES['images']['error'][$index] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['images']['tmp_name'][$index];

                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $newName = time() . '_' . $index . '_' . uniqid() . '.' . $extension;

                move_uploaded_file($tmpName, '../uploads/' . $newName);

                $imageStmt->execute([
                    'property_id' => $propertyId,
                    'image_name' => $newName,
                    'sort_order' => $index
                ]);
            }
        }
    }

    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni İlan Ekle - Admin</title>
    <link rel="icon" type="image/png" href="../logo.png">
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
</head>
<body class="admin-body">

<div class="admin-form-page">
    <div class="admin-form-top">
        <div>
            <h1>Yeni İlan Ekle</h1>
            <p>Yeni portföy bilgilerini gir ve sisteme kaydet.</p>
        </div>
        <div class="admin-topbar-actions">
            <a href="dashboard.php" class="admin-btn admin-btn-soft">← Dashboard</a>
        </div>
    </div>

    <div class="admin-form-card">
        <form method="POST" enctype="multipart/form-data" class="admin-form-grid">
            <div class="form-group">
                <label for="title">İlan Başlığı</label>
                <input type="text" id="title" name="title" placeholder="Örn: Deniz manzaralı lüks daire" required>
            </div>

            <div class="form-group">
                <label for="type">Tür</label>
                <select id="type" name="type" required>
                    <option value="Daire">Daire</option>
                    <option value="Arsa">Arsa</option>
                    <option value="Bina">Bina</option>
                    <option value="Müstakil Ev">Müstakil Ev</option>
                    <option value="Dükkan">Dükkan</option>
                    <option value="Ofis">Ofis</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Fiyat</label>
                <input type="number" id="price" name="price" placeholder="Örn: 2500000" required>
            </div>

            <div class="form-group">
                <label for="location">Konum</label>
                <input type="text" id="location" name="location" placeholder="İl / İlçe / Mahalle" required>
            </div>

            <div class="form-group">
                <label for="area">Metrekare</label>
                <input type="number" id="area" name="area" placeholder="Örn: 145" required>
            </div>

            <div class="form-group">
                <label for="rooms">Oda Sayısı</label>
                <input type="text" id="rooms" name="rooms" placeholder="Örn: 3+1">
            </div>

            <div class="form-group">
                <label for="status">Durum</label>
                <select id="status" name="status" required>
                    <option value="Satılık">Satılık</option>
                    <option value="Kiralık">Kiralık</option>
                </select>
            </div>

            <div class="form-group">
                <label for="images">İlan Görselleri</label>
                <input type="file" id="images" name="images[]" multiple accept="image/*">
            </div>

            <div class="form-group full">
                <label for="description">Açıklama</label>
                <textarea id="description" name="description" placeholder="İlan açıklamasını yazın..."></textarea>
            </div>

            <div class="admin-form-actions full">
                <a href="dashboard.php" class="admin-btn admin-btn-soft">İptal</a>
                <button type="submit" class="admin-btn admin-btn-primary">İlanı Kaydet</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>