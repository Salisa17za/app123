<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'condb.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "success" => false,
            "message" => "Method not allowed"
        ]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || empty($data['username']) || empty($data['password'])) {
        echo json_encode([
            "success" => false,
            "message" => "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน"
        ]);
        exit;
    }

    $username = trim($data['username']);
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        // 💥 ใช้แบบเช็คตรง (เพราะ DB ไม่ได้เก็บ hash)
        if ($password === $user['password']) {

            echo json_encode([
                "success" => true,
                "message" => "เข้าสู่ระบบสำเร็จ",
                "customer_id" => (int)$user['customer_id'],
                "username" => $user['username'],
                "firstName" => $user['firstName'],
                "lastName" => $user['lastName'],
                "phone" => $user['phone']
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" => "รหัสผ่านไม่ถูกต้อง"
            ]);

        }

    } else {

        echo json_encode([
            "success" => false,
            "message" => "ไม่พบชื่อผู้ใช้นี้ในระบบ"
        ]);

    }

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ]);

}
