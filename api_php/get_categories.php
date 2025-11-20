<?php
require 'config.php';

$sql = "SELECT * FROM asset_categories ORDER BY category_id ASC";
$result = $conn->query($sql);
$cats = [];

if($result){
    while($row = $result->fetch_assoc()){
        $cats[] = $row;
    }
    echo json_encode(["success"=>true,"data"=>$cats]);
}else{
    echo json_encode(["success"=>false,"message"=>$conn->error]);
}
