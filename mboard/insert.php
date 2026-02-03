<?php
    include "../include/session.php";

    if(!$userid) {
        echo "
            <script>
                alert('게시판 글쓰기는 로그인 후 이용해 주세요!');
                history.go(-1);
            </script>
        ";
        exit;
    }

    $subject = $_POST["subject"] ?? "";
    $content = $_POST["content"] ?? "";
    $is_html = $_POST['is_html'] ?? 'n';

    $subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

    $regist_day = date("Y-m-d (H:i)");

    // 업로드 디렉토리 준비
    $upload_dir = realpath('/var/www/uploads/data/');
    if ($upload_dir === false) {
        die("업로드 디렉토리가 존재하지 않습니다.");
    }
    // rtrim() : 문자열의 오른쪽(끝)에서 공백이나 특정 문자를 제거하는 데 사용
    // 경로 끝에 붙어있는 "슬래시(/)" 또는 "역슬래시(\)" 를 지우고 다시 운영체제에 맞는 구분자를 1개 붙이는 것
    $upload_dir = rtrim($upload_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    // 허용 확장자 / MIME
    $allowed_Extensions = ['txt', 'jpg', 'jpeg', 'webp', 'png', 'hwp', 'pdf', 'docx', 'doc', 'docm'];

    $file = $_FILES['upfile'] ?? null;

    $fileName = "";
    $upfile_type = "";
    $copied_file_name = "";

    if($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {

        if ($file['error'] !== UPLOAD_ERR_OK) {
            die("업로드 오류 코드: ".$file['error']);
        }

        $fileName = basename($file['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

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
        
        $upfile_type = $real_mime;

        // 이미지면 getimagesize로 이미지가 진짜 맞는지 추가 확인
        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            if (@getimagesize($file["tmp_name"]) === false) {
                die("이미지 파일이 아닙니다.");
            }
        }

        // 저장 파일명 생성
        $copied_file_name = bin2hex(random_bytes(16)) . "." . $fileExtension;

        $uploaded_file = $upload_dir . $copied_file_name;

        if(!move_uploaded_file($file["tmp_name"], $uploaded_file)) {
            die("파일 저장 실패");
        }
    }

    include "../include/db_connect.php";

    $stmt = $con->prepare("INSERT INTO board (id, name, public_id, subject, content, regist_day, file_name, file_type, file_copied) values(?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssss', $userid, $username, $public_id, $subject, $content, $regist_day, $fileName, $upfile_type, $copied_file_name);
    $stmt->execute();

    mysqli_close($con);

    echo "<script>location.href = 'list.php';</script>";
?>