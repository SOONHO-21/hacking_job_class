<?php
session_start();
include "../include/db_connect.php";

$public_id = $_GET['public_id'];

$stmt = $con->prepare("SELECT * FROM _mem WHERE public_id = ?");
$stmt->bind_param("s", $public_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

$defaultImgDir = "/var/www/img/default_profile.png";
$baseDir = "/var/www/uploads/profile/";

$filename = basename($row['profile_img']); // 경로 조작 방어
if($filename)
    $path = $baseDir . $filename;
else
    $path = $defaultImgDir;

// 실제 파일 존재 확인
if (!is_file($path)) {
    http_response_code(404);
    exit;
}

// MIME 타입 추출 (실제 파일 기준)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $path);
finfo_close($finfo);

// 허용 이미지 타입만 통과
$allowed = [
    'image/jpg',
    'image/jpeg',
    'image/png',
    'image/webp'
];

if (!in_array($mime, $allowed, true)) {
    http_response_code(403);
    exit;
}

// 출력
header("Content-Type: $mime");
header("Content-Length: " . filesize($path));
readfile($path);
exit;