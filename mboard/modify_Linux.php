<?php
    include "../include/session.php";
    include "../include/db_connect.php";

    $num = $_GET["num"];
    $page = $_GET["page"];

    $stmt = $con->prepare("SELECT * FROM board WHERE num = ?");
    $stmt->bind_param('i', $num);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$userid) {
        echo "
            <script>
                alert('게시판 수정은 로그인 후 이용해 주세요!');
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

    $upload_dir = '/var/www/uploads/data/';    // 첨부파일 저장 디렉토리
    $upload_dir = realpath($upload_dir);

    $allowed_Extensions = ['txt', 'jpg', 'jepg', 'webp', 'png', 'hwp', 'pdf', 'docx', 'doc', 'docm']; // 화이트리스트로 허용할 확장자
    $allowed_mime_types = array('image/jpeg', 'image/png', 'image/gif', 'text/plain');
    $file = $_FILES['upfile'];

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));   // 소문자로 파일명 변환 후 확장자 추출
    $fileName = basename($file['name']);     // 파일 이름 추출

    $upfile_tmp_name = $file["tmp_name"];
    $upfile_type = $file["type"];
    $upfile_size = $file["size"];
    $upfile_error = $file["error"];

    if($fileName && !$upfile_error) {
        $copied_file_name = bin2hex(random_bytes(8));
        $copied_file_name .= ".".$fileExtension;

        if(!in_array($fileExtension, $allowed_Extensions) || !in_array($upfile_type, $allowed_mime_types)) {     // 확장자 필터링
            echo "
                <script>
                    alert('허용되지 않는 확장자입니다. 파일 업로드를 차단합니다.');
                    history.go(-1);
                </script>
            ";
            exit;
        }
        else if($upfile_size > 10000000) {  // 파일 용량 필터링
            echo "
                <script>
                    alert('업로드 파일 크기가 지정된 용량(10MB)을 초과합니다!<br>파일 크기를 체크해주세요!');
                    history.go(-1);
                </script>
            ";
            exit;
        }
        else {
            // 확장자가 블랙리스트에 없고, 파일 사이즈가 10MB이하면 업로드 진행
            $uploaded_file = $upload_dir.$copied_file_name;
            if(!move_uploaded_file($upfile_tmp_name, $uploaded_file)) {
                echo"
                    <script>
                        alert('파일을 지정한 디렉토리에 복사하는데 실패했습니다.');
                    </script>
                ";
                exit;
            }
        }
    }
    else {
        $fileName      = "";
		$upfile_type      = "";
		$copied_file_name = "";
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