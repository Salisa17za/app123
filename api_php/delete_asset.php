<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$id    = $data['asset_id'] ?? 0;
$code  = $data['asset_code'] ?? '';
$name  = $data['asset_name'] ?? '';
$cat   = $data['category_id'] ?? null;
$date  = $data['purchase_date'] ?? null;
$price = $data['price'] ?? 0;
$image = $data['image'] ?? '';

$stmt = $conn->prepare("UPDATE assets SET asset_code=?, asset_name=?, category_id=?, purchase_date=?, price=?, image=? WHERE asset_id=?");
$stmt->bind_param("ssisssi", $code, $name, $cat, $date, $price, $image, $id);

if($stmt->execute()){
    echo json_encode(["success"=>true,"message"=>"ลบครุภัณฑ์เรียบร้อยแล้ว"]);
}else{
    echo json_encode(["success"=>false,"message"=>$conn->error]);
}
