<?php
    include "../include/session.php";
    include "../include/db_connect.php";

    $num = isset($_GET['num']) ? (int)$_GET['num'] : 0;

    $page = $_GET["page"];

    $stmt = $con->prepare("SELECT * FROM board WHERE num = ? AND id = ?");
    $stmt->bind_param('is', $num, $userid);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) {
        echo "
            <script>
                alert('남이 쓴 글은 수정할 수 없습니다.');
                history.go(-1);
            </script>
        ";
        exit;
    }

    $subject = $_POST["subject"];
    $content = $_POST["content"];

    $subject = htmlspecialchars($subject, ENT_QUOTES);
    
    $is_html = $_POST['is_html'] ?? 'n';
    if($is_html !== 'y'){
        $content = htmlspecialchars($content, ENT_QUOTES);
    }

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

    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $fileName = $row['file_name'];
        $upfile_type = $row['file_type'];
        $copied_file_name = $row['file_copied'];
    }

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
        if (in_array($fileExtension, ['jpg','jpeg','png','webp'], true)) {
            if (@getimagesize($file["tmp_name"]) === false) {
                die("이미지 파일이 아닙니다.");
            }
        }

        $file_copied = $row['file_copied'] ?? "";
        // 파일명 안전 처리
        $file_copied_safe = basename($file_copied);

        if(!empty($file_copied_safe)) {
            $file_path = $upload_dir . $file_copied_safe;

            // 2. 물리적 파일 삭제
            if (!empty($file_path) && file_exists($file_path)) {
                if (!unlink($file_path)) {
                    echo "<pre>unlink 실패! last error: ";
                }
            }
        }

        // 저장 파일명 생성
        $copied_file_name = bin2hex(random_bytes(16)) . "." . $fileExtension;
        $uploaded_file = $upload_dir . $copied_file_name;

        if(!move_uploaded_file($file["tmp_name"], $uploaded_file)) {
            die("파일 저장 실패");
        }
    }

    $stmt = $con->prepare("UPDATE board SET subject = ?, content = ?, is_html = ?, regist_day = ?, file_name = ?, file_type = ?, file_copied = ? WHERE num = ?");
    $stmt->bind_param('sssssssi', $subject, $content, $is_html, $regist_day, $fileName, $upfile_type, $copied_file_name, $num);
    $stmt->execute();

    mysqli_close($con);

    // 목록 페이지로 이동
    echo "<script>
            location.href = 'list.php';
        </script>"
?>