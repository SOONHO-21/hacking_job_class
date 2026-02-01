<?php
    include "../include/session.php";
    include "../include/db_connect.php";

    $num = $_GET["num"];
    $page = $_GET["page"];

    $stmt = $con->prepare("SELECT id, file_copied FROM board WHERE num = ? and id = ?");
    $stmt->bind_param('is', $num, $userid);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "<script>
                window.alert('존재하지 않는 게시글이거나 권한이 없습니다.');
                location.href = 'list.php?page=$page';
            </script>";
        exit;
    }

    $upfile_dir = "/var/www/uploads/data/";
    $file_copied = $row['file_copied'] ?? "";
    
    // 파일명 안전 처리
    $file_copied_safe = basename($file_copied);

    if(!empty($file_copied_safe)) {
        $file_path = $upfile_dir . $file_copied_safe;

        // 2. 물리적 파일 삭제
        if (!empty($file_path) && file_exists($file_path)) {
            if (!unlink($file_path)) {
                echo "<pre>unlink 실패! last error: ";
            }
        }
    }
    
    //DB 삭제
    $stmt = $con->prepare("DELETE FROM board WHERE num = ?");
    $stmt->bind_param('i', $num);
    $stmt->execute();

    mysqli_close($con);

    echo "<script>
        location.href = 'list.php?page=$page';
        </script>";