<?php
require("config.php");

$data = json_decode(file_get_contents("php://input"), true);

$asset_id     = $data["asset_id"];
$asset_name   = $data["asset_name"];
$category_id  = $data["category_id"];
$price        = $data["price"];

$sql = "UPDATE assets SET
        asset_name = ?,
        category_id = ?,
        price = ?
        WHERE asset_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sisi", 
    $asset_name, 
    $category_id, 
    $price, 
    $asset_id
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "อัปเดตสำเร็จ"]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}
?>
