<?php
require 'config.php';

// ดึงข้อมูล assets พร้อม join หมวดหมู่
$sql = "SELECT a.*, c.category_name 
        FROM assets a 
        LEFT JOIN asset_categories c ON a.category_id=c.category_id
        ORDER BY a.asset_id DESC";

$result = $conn->query($sql);
$assets = [];

if($result){
    while($row = $result->fetch_assoc()){
        $assets[] = $row;
    }
    echo json_encode(["success"=>true, "data"=>$assets]);
}else{
    echo json_encode(["success"=>false,"message"=>$conn->error]);
}
