<?php
    include "../include/session.php";
    include "../include/db_connect.php";

    $num = $_GET["num"];
    $ripple_num = $_GET["ripple_num"];
    $page = $_GET["page"];

    $stmt = $con->prepare("SELECT id FROM ripple WHERE num = ? AND id = ?");
    $stmt->bind_param('is', $ripple_num, $userid);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) {
        echo "<script>
                window.alert('댓글 삭제는 해당 댓글 작성자만 가능합니다.');
                location.href = 'list.php?page=$page';
            </script>";
    } else {
        $stmt = $con->prepare("DELETE FROM ripple WHERE num = ?");
        $stmt->bind_param('i', $ripple_num);
        $stmt->execute();

        mysqli_close($con);

        echo "<script>
                location.href = 'view.php?page=$page&num=$num';
            </script>";
    }