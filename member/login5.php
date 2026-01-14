<?php
    $id   = $_POST["id"];
    $pass = $_POST["pass"];

    include "../include/db_connect.php";

    if(isset($id) && isset($pass)) {
        $hash_pw = hash('sha256', $pass);

        $stmt = $con->prepare("SELECT * from _mem WHERE id=?");
        $stmt->bind_param('s', $id);
        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        if($row && password_verify($pass, $row['pass'])) {
            // 세션값 설정
            session_start();
            session_regenerate_id(true);
            
            $_SESSION["userid"] = $row["id"];
            $_SESSION["username"] = $row["name"];
            $_SESSION["public_id"] = $row["public_id"];
            $_SESSION["userlevel"] = $row["level"];

            echo "<script>
                     location.href = '../mboard/list.php';
                 </script>";
        }
        else {
            echo "<script>
                    window.alert('아이디 혹은 비밀번호가 틀립니다.')
                    history.go(-1)
                </script>";
            exit;
        }
    }
    mysqli_close($con);
?>