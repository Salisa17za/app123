<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli("localhost","root","","db_asset");
if ($conn->connect_error) {
    die(json_encode(["success"=>false,"message"=>"Connection failed"]));
}

$data = json_decode(file_get_contents("php://input"), true);

$action = $data['action'] ?? '';

if($action == "add"){
    $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category_id, purchase_date, price) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssisd", $data['asset_code'], $data['asset_name'], $data['category_id'], $data['purchase_date'], $data['price']);
    if($stmt->execute()){
        echo json_encode(["success"=>true, "message"=>"เพิ่มครุภัณฑ์เรียบร้อย"]);
    } else {
        echo json_encode(["success"=>false, "message"=>"เพิ่มไม่สำเร็จ"]);
    }
}

elseif($action == "edit"){
    $stmt = $conn->prepare("UPDATE assets SET asset_code=?, asset_name=?, category_id=?, purchase_date=?, price=? WHERE asset_id=?");
    $stmt->bind_param("ssisdi", $data['asset_code'], $data['asset_name'], $data['category_id'], $data['purchase_date'], $data['price'], $data['asset_id']);
    if($stmt->execute()){
        echo json_encode(["success"=>true, "message"=>"แก้ไขครุภัณฑ์เรียบร้อย"]);
    } else {
        echo json_encode(["success"=>false, "message"=>"แก้ไขไม่สำเร็จ"]);
    }
}

elseif($action == "delete"){
    $stmt = $conn->prepare("DELETE FROM assets WHERE asset_id=?");
    $stmt->bind_param("i", $data['asset_id']);
    if($stmt->execute()){
        echo json_encode(["success"=>true, "message"=>"ลบครุภัณฑ์เรียบร้อย"]);
    } else {
        echo json_encode(["success"=>false, "message"=>"ลบไม่สำเร็จ"]);
    }
}

$conn->close();
?>
