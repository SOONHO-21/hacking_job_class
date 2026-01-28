<?php
    if (!isset($_GET['u']) || empty($_GET['u'])) {
        echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
        exit;
    }

    $public_id = $_GET['u'];
    
    include "../include/db_connect.php";

    $stmt = $con->prepare("SELECT * FROM _mem WHERE public_id = ?");
    $stmt->bind_param("s", $public_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = mysqli_fetch_assoc($result);

    $id = $row["id"];
    $pass = $row["pass"];
    $name = $row["name"];
    $email = $row["email"];
    $regist_day = date("Y-m-d (H:i)");
?>
<script>
    function check_id(){    // 아이디 중복 체크
        window.open("check_id.php?id=" + document.member.id.value,
            "IDcheck",
            "left=700,top=300,width=380,height=160,scrollbars=no,resizable=yes");
    }
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body> 
    <h2>프로필</h2>
    <?php if($profile_img) { ?>
        <img src="./profile_upload/<?=$profile_img?>" width="220" height="150"><br>
    <?php } else { ?>
        <img src="../img/default_profile.png" width="220" height="150"><br>
    <?php } ?>

    <ul class="list-group">
        <li class="list-group-item">
            <span class="col1">이름</span>
            <span class="col2"><?=$name?></span>
        </li>
        <li class="list-group-item">
            <span class="col1">이메일</span>
            <span class="col2"><?=$email?></span>
        </li>
    </ul>

    <span class="col1">내가 쓴 글</span>
    <br>
    <?php
        $stmt = $con->prepare("SELECT * FROM board WHERE id=? ORDER BY num DESC");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = mysqli_fetch_assoc($result)){
            $num = $row["num"];
            $subject = $row["subject"];
    ?>
    <li class="list-group-item">
        <a href = "../mboard/view.php?num=<?=$num?>"><?=$subject?></a>
    </li>
    <?php
        }
    ?>
</body>
</html>