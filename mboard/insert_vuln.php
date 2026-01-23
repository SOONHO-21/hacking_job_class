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

    $subject = $_POST["subject"];
    $content = $_POST["content"];
    $is_html = $_POST['is_html'] ?? 'n';		// HTML 쓰기

    $subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');  // XSS 방어. HTML 특수문자 변환

    if($is_html !== 'y'){
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    }

    $regist_day = date("Y-m-d (H:i)");
    
    $upload_dir = './data/';    // 첨부파일 저장 디렉토리
    $upfile_name = $_FILES["upfile"]["name"];
    $upfile_tmp_name = $_FILES["upfile"]["tmp_name"];
    $upfile_type = $_FILES["upfile"]["type"];
    $upfile_size = $_FILES["upfile"]["size"];
    $upfile_error = $_FILES["upfile"]["error"];

    if($upfile_name && !$upfile_error)
    {
        $file = explode(".", $upfile_name); // .을 기준으로 $upfile_name을 배열 형태로 분리
        $file_name = $file[0];
        $file_ext = $file[1];

        $copied_file_name = date("Y_m_d_H_i_s");
        $copied_file_name .= ".".$file_ext;
        $uploaded_file = $upload_dir.$copied_file_name;

        if($upfile_size > 10000000) {
            echo "
                <script>
                    alert('업로드 파일 크기가 지정된 용량(10MB)을 초과합니다!<br>파일 크기를 체크해주세요!');
                    history.go(-1);
                </script>
            ";
            exit;
        }

        if(!move_uploaded_file($upfile_tmp_name, $uploaded_file)) {
            echo"
                <script>
                    alert('파일을 지정한 디렉토리에 복사하는데 실패했습니다.');
                </script>
            ";
            exit;
        }
    }
    else
    {
        $upfile_name      = "";
		$upfile_type      = "";
		$copied_file_name = "";
    }

    include "../include/db_connect.php";

    $stmt = $con->prepare("INSERT INTO board (id, name, public_id, subject, content, is_html, regist_day, file_name, file_type, file_copied) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssssss', $userid, $username, $public_id, $subject, $content, $is_html, $regist_day, $upfile_name, $upfile_type, $copied_file_name);

    $stmt->execute();

    mysqli_close($con);

    // 목록 페이지로 이동
    echo "<script>
            location.href = 'list.php';
        </script>"
?>