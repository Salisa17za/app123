<?php
require 'config.php';

// รับค่า JSON จาก Vue.js
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// ตัวอย่าง: user เก็บใน DB table `users` (username / password)
$sql = "SELECT * FROM users WHERE username=? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $user = $result->fetch_assoc();
    // ตรวจสอบรหัสผ่าน (plain text ง่าย ๆ หรือใช้ password_hash)
    if($password === $user['password']){
        echo json_encode(["success"=>true, "message"=>"Login สำเร็จ"]);
    } else {
        echo json_encode(["success"=>false, "message"=>"รหัสผ่านไม่ถูกต้อง"]);
    }
}else{
    echo json_encode(["success"=>false, "message"=>"ไม่พบผู้ใช้"]);
}
