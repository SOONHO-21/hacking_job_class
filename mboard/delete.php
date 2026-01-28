<?php
    include "../include/session.php";
    include "../include/db_connect.php";

    $num = $_GET["num"];
    $page = $_GET["page"];

    $stmt = $con->prepare("SELECT id FROM board WHERE num = ?");
    $stmt->bind_param('i', $num);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($userid != $row["id"]) {
        echo "<script>
                window.alert('글 삭제는 해당 글 작성자만 가능합니다. no hack');
                location.href = 'list.php?page=$page';
            </script>";
    } else {
        $stmt = $con->prepare("DELETE FROM board WHERE num = ?");
        $stmt->bind_param('i', $num);
        $stmt->execute();

        mysqli_close($con);

        echo "<script>
            location.href = 'list.php?page=$page';
            </script>";
    }