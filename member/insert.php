<?php
    $id = $_POST["id"];
    $id = htmlspecialchars($id, ENT_QUOTES);

    $pass = $_POST["pass"];

    $name = $_POST["name"];
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = $_POST["email"];
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    
    $regist_day = date("Y-m-d (H:i)");

    include "../include/db_connect.php";

    $hash_pw = password_hash($pass, PASSWORD_DEFAULT);

    function generatePublicId() {
        return substr(bin2hex(random_bytes(10)), 0, 10);
    }

    $public_id = generatePublicId();

    $stmt = $con->prepare("INSERT INTO _mem (id, pass, name, public_id, email, regist_day, level) values(?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param('ssssss', $id, $hash_pw, $name, $public_id, $email, $regist_day);
    $stmt->execute();
    
    mysqli_close($con);

    echo "<script>
		location.href = './login_form.php';
		</script>";
?>