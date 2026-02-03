<?php
include "../include/db_connect.php";
include "admin_check.php";

$num = $_GET['num'];

$csrf_token = $_POST["csrf_token"];     // POST 메시지로 CSRF 토큰 받음

// CSRF 토큰 검증
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && ($_SESSION['csrf_token'] === $token);   // CSRF 토큰이 세션에 등록되어 있는지, POST로 받은 CSRF 토큰이 세션에 등록되어있는 CSRF 토큰과 같은지 확인
}

if(!validateCSRFToken($csrf_token)) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

$stmt = $con->prepare("SELECT 1 FROM board WHERE num = ?");
$stmt->bind_param('i', $num);
$stmt->execute();
$stmt->store_result();
$row_count = $stmt->num_rows;

if($row_count > 0){
    $stmt = $con->prepare("DELETE FROM board WHERE num = ?");
    $stmt->bind_param('i', $num);
    $stmt->execute();

    mysqli_close($con);

    echo "<script>alert('게시글이 삭제되었습니다.');location.href='board_list.php';</script>";
} else {
    echo "<script>alert('게시글이 없습니다.');location.href='board_list.php';</script>";
}