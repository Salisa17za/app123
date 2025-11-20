<?php
include 'db_asset'; // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $asset_id = $_POST['asset_id'] ?? null;
    $asset_code = $_POST['asset_code'];
    $asset_name = $_POST['asset_name'];
    $category_name = $_POST['category_name'];
    $purchase_date = $_POST['purchase_date'];
    $price = $_POST['price'];

    // ถ้ามีการอัปโหลดรูปภาพ
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";  // โฟลเดอร์สำหรับเก็บไฟล์
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // ตรวจสอบว่าไฟล์เป็นภาพที่อนุญาตให้ใช้งาน
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $imageName = basename($_FILES["image"]["name"]);
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit;
        }
    } else {
        $imageName = null;
    }

    if ($asset_id) {
        // หากเป็นการแก้ไข
        $sql = "UPDATE assets SET asset_code = ?, asset_name = ?, category_name = ?, purchase_date = ?, price = ?, image = ? WHERE asset_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$asset_code, $asset_name, $category_name, $purchase_date, $price, $imageName, $asset_id]);
    } else {
        // หากเป็นการเพิ่มใหม่
        $sql = "INSERT INTO assets (asset_code, asset_name, category_name, purchase_date, price, image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$asset_code, $asset_name, $category_name, $purchase_date, $price, $imageName]);
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
