<?php
session_start();

// 세션 변수 전체 제거
$_SESSION = [];

// 세션 변수 해제 (모든 변수 삭제)
session_unset();

// 세션 쿠키 제거
// 세션 ID 쿠키를 과거 시점으로 설정하여 삭제
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

echo "<script>
        location.href = '../index.php';
    </script>"
?>