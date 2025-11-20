<?php
// แสดง error
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

// เชื่อมฐานข้อมูล
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_asset";

$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error){
    die(json_encode(["success"=>false,"message"=>"Database connection failed: ".$conn->connect_error]));
}
