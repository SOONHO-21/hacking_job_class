<?php
    include "../include/session.php";
    include "../include/db_connect.php";

    $current_pass = $_POST["current_pass"];  // 입력받은 현재 비밀번호
    $pass = $_POST["pass"];
    $hash_pw = "";

    $name = $_POST["name"];
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = $_POST["email"];
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

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

    $upload_dir = realpath('/var/www/uploads/profile/');    // 프로필 사진파일 저장 디렉토리
    if ($upload_dir === false) {
        die("업로드 디렉토리가 존재하지 않습니다.");
    }
    $upload_dir = rtrim($upload_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    $allowed_Extensions = ['jpg', 'png', 'jepg']; // 화이트리스트로 허용할 확장자
    $file = $_FILES['profile_img'] ?? null;

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));   // 소문자로 파일명 변환 후 확장자 추출

    $fileName = basename($file['name']);     // 파일 이름 추출
    $upfile_tmp_name = $file["tmp_name"];
    $upfile_type = $file["type"];
    $upfile_error = $file["error"];

    if($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            die("업로드 오류 코드: ".$file['error']);
        }

        // 확장자 화이트리스트 기반 검증
        if(!in_array($fileExtension, $allowed_Extensions, true)) {
            die("허용되지 않는 확장자입니다.");
        }

        // 용량 제한
        if($file["size"] > 10000000) {
            die("업로드 파일 크기가 10MB를 초과합니다.");
        }

        // 실제 MIME 검증(finfo) (DB에 저장하기 위함)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $real_mime = finfo_file($finfo, $file["tmp_name"]);
        finfo_close($finfo);

        // getimagesize로 이미지가 진짜 맞는지 추가 확인
        if (in_array($fileExtension, ['jpg','jpeg','png','webp'], true)) {
            if (@getimagesize($file["tmp_name"]) === false) {
                die("이미지 파일이 아닙니다.");
            }
        }

        // 저장 파일명 생성
        $new_name = bin2hex(random_bytes(16)) . "." . $fileExtension;

        $uploaded_file = $upload_dir . $new_name;

        if(!move_uploaded_file($file["tmp_name"], $uploaded_file)) {
            die("파일 저장 실패");
        }

        $stmt = $con->prepare("UPDATE _mem SET pass = ?, name = ?, email = ?, profile_img = ? WHERE id = ?");
        $stmt->bind_param('sssss', $hash_pw, $name, $email, $new_name, $userid);
        $stmt->execute();
    }
    else {
        $stmt = $con->prepare("UPDATE _mem SET pass = ?, name = ?, email = ?, profile_img = null WHERE id=?");
        $stmt->bind_param('ssss', $hash_pw, $name, $email, $userid);
        $stmt->execute();
    }

    $stmt = $con->prepare("UPDATE board SET name = ? WHERE id = ?");
    $stmt->bind_param('ss', $name, $userid);
    $stmt->execute();

    mysqli_close($con);

    // 목록 페이지로 이동
    echo "<script>
        location.href = '../index.php';
        </script>"
?>