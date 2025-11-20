<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","db_asset");
if ($conn->connect_error) {
    die(json_encode(["success"=>false,"message"=>"Connection failed"]));
}

$sql = "SELECT a.asset_id, a.asset_code, a.asset_name, c.category_name, a.purchase_date, a.price, a.image
        FROM assets a
        LEFT JOIN asset_categories c ON a.category_id = c.category_id
        ORDER BY a.asset_id DESC";

$result = $conn->query($sql);
$data = [];
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
}

echo json_encode(["success"=>true,"data"=>$data]);
$conn->close();
?>
