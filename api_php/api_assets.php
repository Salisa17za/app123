<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "db_asset");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => $conn->connect_error]));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // ดึงข้อมูลครุภัณฑ์พร้อมชื่อหมวดหมู่
    $sql = "SELECT a.asset_id, a.asset_code, a.asset_name, c.category_name, a.purchase_date, a.price, a.image
            FROM assets a
            LEFT JOIN asset_categories c ON a.category_id = c.category_id
            ORDER BY a.asset_id DESC";
    $result = $conn->query($sql);
    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode(["success"=>true,"data"=>$data]);
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === "add") {
        $asset_code = $_POST['asset_code'];
        $asset_name = $_POST['asset_name'];
        $category_id = $_POST['category_id'];
        $purchase_date = $_POST['purchase_date'];
        $price = $_POST['price'];
        $image = "";

        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $target_dir = "uploads/";
            $image = time().'_'.basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir.$image);
        }

        $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category_id, purchase_date, price, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisds", $asset_code, $asset_name, $category_id, $purchase_date, $price, $image);
        $stmt->execute();
        echo json_encode(["success"=>true,"message"=>"เพิ่มครุภัณฑ์เรียบร้อย"]);
    }

    if ($action === "update") {
        $asset_id = $_POST['asset_id'];
        $asset_code = $_POST['asset_code'];
        $asset_name = $_POST['asset_name'];
        $category_id = $_POST['category_id'];
        $purchase_date = $_POST['purchase_date'];
        $price = $_POST['price'];

        $imageSql = "";
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $target_dir = "uploads/";
            $image = time().'_'.basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir.$image);
            $imageSql = ", image='$image'";
        }

        $sql = "UPDATE assets SET asset_code=?, asset_name=?, category_id=?, purchase_date=?, price=? $imageSql WHERE asset_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisdi", $asset_code, $asset_name, $category_id, $purchase_date, $price, $asset_id);
        $stmt->execute();
        echo json_encode(["success"=>true,"message"=>"แก้ไขครุภัณฑ์เรียบร้อย"]);
    }

    if ($action === "delete") {
        $asset_id = $_POST['asset_id'];
        $stmt = $conn->prepare("DELETE FROM assets WHERE asset_id=?");
        $stmt->bind_param("i", $asset_id);
        $stmt->execute();
        echo json_encode(["success"=>true,"message"=>"ลบครุภัณฑ์เรียบร้อย"]);
    }
}

$conn->close();
?>
