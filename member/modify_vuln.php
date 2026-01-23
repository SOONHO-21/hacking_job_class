<?php
    include "../include/session.php";
    include "../include/db_connect.php";

    // $allowed_page = "modify_form.php";    // 허용할 페이지
    // $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''; // Referer 헤더가 비어있는지 체크

    // 취약한 Referer헤더 검증 구현
    // if (empty($referer) || strpos($referer, $allowed_page) === false) {  // Referer가 비어있거나, 허용된 페이지가 아니면 접근 거부
    //     die("잘못된 접근입니다.");
    // }

    // $expected_domain = 'localhost';

    // Referer 헤더를 가져옵니다.
    // $referer = $_SERVER['HTTP_REFERER'] ?? '';

    if (!empty($referer)) {
        $referer_host = parse_url($referer, PHP_URL_HOST);
        
        // Referer의 호스트가 예상 도메인과 일치하는지 확인합니다.
        if ($referer_host !== $expected_domain) {
            // 요청 처리 (예: 비밀번호 변경, 정보 수정 등)
            die("오류: 유효하지 않은 요청 출처입니다. CSRF 공격이 의심됩니다.");
        }
    }

    $current_pass = $_POST["current_pass"];  // 입력받은 현재 비밀번호
    $pass = $_POST["pass"];
    $hash_pw = "";

    $name = $_POST["name"];
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = $_POST["email"];
    // $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

    $csrf_token = $_POST["csrf_token"];

    $stmt = $con->prepare("SELECT pass FROM _mem WHERE id=?");
    $stmt->bind_param('s', $userid);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
 
    if(!password_verify($current_pass, $row['pass'])){   // 현재 비밀번호 확인 로직
        http_response_code(403);
        exit('잘못된 입력입니다.');
    }

    // CSRF 토큰 검증
    function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && ($_SESSION['csrf_token'] == $token);
    }

    if(!validateCSRFToken($csrf_token)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }
    
    $hash_pw = password_hash($pass, PASSWORD_DEFAULT);

    if(isset($_FILES['profile_img']) && $_FILES['profile_img']['name'] != "") {     // modify_form.php에서 <input type="file" name="profile_img"> 코드에 근거. 프로필 사진이 있을 경우
        $upload_dir = "./profile_upload/";
        // 원본 파일명 그대로 사용
        $file_name = $_FILES['profile_img']['name'];
        $file_tmp = $_FILES['profile_img']['tmp_name'];

        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_name = $userid."_".time().".".$file_ext;

        move_uploaded_file($file_tmp, $upload_dir.$new_name);

        $stmt = $con->prepare("UPDATE _mem SET pass = ?, name = ?, email = ?, profile_img = ? WHERE id=?");
        $stmt->bind_param('sssss', $hash_pw, $name, $email, $new_name, $userid);
        $stmt->execute();
    } else {
        $stmt = $con->prepare("UPDATE _mem SET pass = ?, name = ?, email = ? WHERE id=?");
        $stmt->bind_param('ssss', $hash_pw, $name, $email, $userid);
        $stmt->execute();
    }

    $stmt = $con->prepare("UPDATE board SET name = ? WHERE id=?");
    $stmt->bind_param('ss', $name, $userid);
    $stmt->execute();

    mysqli_close($con);

    // 목록 페이지로 이동
    echo "<script>
        location.href = '../index.php';
        </script>"
?>