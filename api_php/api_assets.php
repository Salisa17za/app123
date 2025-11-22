<?php
// ปิดการแสดง error เพื่อไม่ให้รบกวน JSON response
error_reporting(0);
ini_set('display_errors', 0);

// ตั้งค่า Headers
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// จัดการ OPTIONS request (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// เชื่อมต่อฐานข้อมูล
require_once 'condb.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // ========================
    // 📖 GET - ดึงข้อมูลครุภัณฑ์ทั้งหมด
    // ========================
    if ($method === 'GET') {
        $stmt = $conn->prepare("SELECT * FROM assets ORDER BY created_at DESC");
        $stmt->execute();
        $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            "success" => true,
            "data" => $assets,
            "count" => count($assets)
        ]);
        exit;
    }

    // ========================
    // 📝 POST - เพิ่ม / แก้ไข / ลบ ครุภัณฑ์
    // ========================
    if ($method === 'POST') {
        $action = $_POST['action'] ?? '';

        // ========================
        // ➕ เพิ่มครุภัณฑ์ใหม่
        // ========================
        if ($action === 'add') {
            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($_POST['asset_code']) || empty($_POST['asset_name']) || 
                empty($_POST['category_id']) || empty($_POST['purchase_date']) || 
                empty($_POST['price'])) {
                echo json_encode([
                    "success" => false,
                    "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"
                ]);
                exit;
            }

            $asset_code = trim($_POST['asset_code']);
            $asset_name = trim($_POST['asset_name']);
            $category_id = trim($_POST['category_id']);
            $purchase_date = $_POST['purchase_date'];
            $price = floatval($_POST['price']);

            // ตรวจสอบว่ารหัสครุภัณฑ์ซ้ำหรือไม่
            $checkStmt = $conn->prepare("SELECT asset_id FROM assets WHERE asset_code = :asset_code");
            $checkStmt->bindParam(':asset_code', $asset_code);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                echo json_encode([
                    "success" => false,
                    "message" => "รหัสครุภัณฑ์นี้มีอยู่ในระบบแล้ว"
                ]);
                exit;
            }
            
            // จัดการ Upload รูปภาพ
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                
                // สร้างโฟลเดอร์ถ้ายังไม่มี
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // ตรวจสอบประเภทไฟล์
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $fileType = $_FILES['image']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    echo json_encode([
                        "success" => false,
                        "message" => "อนุญาตเฉพาะไฟล์รูปภาพ (JPG, PNG, GIF)"
                    ]);
                    exit;
                }
                
                // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
                if ($_FILES['image']['size'] > 5242880) {
                    echo json_encode([
                        "success" => false,
                        "message" => "ขนาดไฟล์ต้องไม่เกิน 5MB"
                    ]);
                    exit;
                }
                
                // สร้างชื่อไฟล์ใหม่
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = 'asset_' . time() . '_' . uniqid() . '.' . $extension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image = $fileName;
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ"
                    ]);
                    exit;
                }
            }

            // เพิ่มข้อมูลลงฐานข้อมูล
            $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category_id, purchase_date, price, image) 
                                   VALUES (:asset_code, :asset_name, :category_id, :purchase_date, :price, :image)");
            
            $stmt->bindParam(':asset_code', $asset_code);
            $stmt->bindParam(':asset_name', $asset_name);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':purchase_date', $purchase_date);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':image', $image);

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "เพิ่มครุภัณฑ์สำเร็จ",
                    "asset_id" => $conn->lastInsertId()
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "เกิดข้อผิดพลาดในการเพิ่มครุภัณฑ์"
                ]);
            }
        }

        // ========================
        // ✏️ แก้ไขครุภัณฑ์
        // ========================
        elseif ($action === 'update') {
            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($_POST['asset_id']) || empty($_POST['asset_code']) || 
                empty($_POST['asset_name']) || empty($_POST['category_id']) || 
                empty($_POST['purchase_date']) || empty($_POST['price'])) {
                echo json_encode([
                    "success" => false,
                    "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"
                ]);
                exit;
            }

            $asset_id = intval($_POST['asset_id']);
            $asset_code = trim($_POST['asset_code']);
            $asset_name = trim($_POST['asset_name']);
            $category_id = trim($_POST['category_id']);
            $purchase_date = $_POST['purchase_date'];
            $price = floatval($_POST['price']);

            // ตรวจสอบว่ารหัสครุภัณฑ์ซ้ำหรือไม่ (ยกเว้นตัวเอง)
            $checkStmt = $conn->prepare("SELECT asset_id FROM assets WHERE asset_code = :asset_code AND asset_id != :asset_id");
            $checkStmt->bindParam(':asset_code', $asset_code);
            $checkStmt->bindParam(':asset_id', $asset_id);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                echo json_encode([
                    "success" => false,
                    "message" => "รหัสครุภัณฑ์นี้มีอยู่ในระบบแล้ว"
                ]);
                exit;
            }

            // จัดการรูปภาพใหม่ (ถ้ามี)
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // ตรวจสอบประเภทไฟล์
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $fileType = $_FILES['image']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    echo json_encode([
                        "success" => false,
                        "message" => "อนุญาตเฉพาะไฟล์รูปภาพ (JPG, PNG, GIF)"
                    ]);
                    exit;
                }
                
                if ($_FILES['image']['size'] > 5242880) {
                    echo json_encode([
                        "success" => false,
                        "message" => "ขนาดไฟล์ต้องไม่เกิน 5MB"
                    ]);
                    exit;
                }
                
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = 'asset_' . time() . '_' . uniqid() . '.' . $extension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    // ลบรูปเก่า
                    $oldStmt = $conn->prepare("SELECT image FROM assets WHERE asset_id = :asset_id");
                    $oldStmt->bindParam(':asset_id', $asset_id);
                    $oldStmt->execute();
                    $oldData = $oldStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($oldData && !empty($oldData['image'])) {
                        $oldImagePath = $uploadDir . $oldData['image'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    $image = $fileName;
                }
            }

            // อัปเดตข้อมูล
            if ($image) {
                // มีรูปใหม่
                $stmt = $conn->prepare("UPDATE assets SET 
                                       asset_code = :asset_code, 
                                       asset_name = :asset_name, 
                                       category_id = :category_id, 
                                       purchase_date = :purchase_date, 
                                       price = :price, 
                                       image = :image 
                                       WHERE asset_id = :asset_id");
                $stmt->bindParam(':image', $image);
            } else {
                // ไม่มีรูปใหม่
                $stmt = $conn->prepare("UPDATE assets SET 
                                       asset_code = :asset_code, 
                                       asset_name = :asset_name, 
                                       category_id = :category_id, 
                                       purchase_date = :purchase_date, 
                                       price = :price 
                                       WHERE asset_id = :asset_id");
            }

            $stmt->bindParam(':asset_code', $asset_code);
            $stmt->bindParam(':asset_name', $asset_name);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':purchase_date', $purchase_date);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':asset_id', $asset_id);

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "แก้ไขครุภัณฑ์สำเร็จ"
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "เกิดข้อผิดพลาดในการแก้ไขครุภัณฑ์"
                ]);
            }
        }

        // ========================
        // 🗑️ ลบครุภัณฑ์
        // ========================
        elseif ($action === 'delete') {
            if (empty($_POST['asset_id'])) {
                echo json_encode([
                    "success" => false,
                    "message" => "ไม่พบรหัสครุภัณฑ์"
                ]);
                exit;
            }

            $asset_id = intval($_POST['asset_id']);

            // ดึงข้อมูลรูปภาพก่อนลบ
            $stmt = $conn->prepare("SELECT image FROM assets WHERE asset_id = :asset_id");
            $stmt->bindParam(':asset_id', $asset_id);
            $stmt->execute();
            $asset = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // ลบรูปภาพ (ถ้ามี)
            if ($asset && !empty($asset['image'])) {
                $imagePath = 'uploads/' . $asset['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // ลบข้อมูลจากฐานข้อมูล
            $stmt = $conn->prepare("DELETE FROM assets WHERE asset_id = :asset_id");
            $stmt->bindParam(':asset_id', $asset_id);

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "ลบครุภัณฑ์สำเร็จ"
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "เกิดข้อผิดพลาดในการลบครุภัณฑ์"
                ]);
            }
        }

        // ========================
        // ❌ Action ไม่ถูกต้อง
        // ========================
        else {
            echo json_encode([
                "success" => false,
                "message" => "Action ไม่ถูกต้อง"
            ]);
        }
    }

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "เกิดข้อผิดพลาดจากฐานข้อมูล: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ]);
}
?>