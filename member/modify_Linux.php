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

    $upload_dir = '/var/www/uploads/profile/';    // 프로필 사진파일 저장 디렉토리
    $allowed_Extensions = ['jpg', 'png', 'jepg']; // 화이트리스트로 허용할 확장자
    $allowed_mime_types = array('image/jpg', 'image/jpeg', 'image/png');
    $file = $_FILES['profile_img'];

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));   // 소문자로 파일명 변환 후 확장자 추출
    $fileName = basename($file['name']);     // 파일 이름 추출

    $upfile_name = $file["name"];
    $upfile_tmp_name = $file["tmp_name"];
    $upfile_type = $file["type"];
    $upfile_error = $file["error"];

    if($upfile_name && !$upfile_error) {
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
            $new_name = $userid."_".time().".".$fileExtension;
            // 확장자가 블랙리스트에 없고, 파일 사이즈가 10MB이하면 업로드 진행
            $uploaded_file = $upload_dir.$new_name;
            if(move_uploaded_file($upfile_tmp_name, $uploaded_file)) {
                
            }
            else {
                echo"
                    <script>
                        alert('파일을 지정한 디렉토리에 복사하는데 실패했습니다.');
                    </script>
                ";
                exit;
            }
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

    $stmt = $con->prepare("UPDATE board SET name = ? WHERE id=?");
    $stmt->bind_param('ss', $name, $userid);
    $stmt->execute();

    mysqli_close($con);

    // 목록 페이지로 이동
    echo "<script>
        location.href = '../index.php';
        </script>"
?>