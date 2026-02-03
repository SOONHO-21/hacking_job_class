<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<?php
    include "../include/session.php";

    if(!$userid) {
        echo "<script>
                window.alert('회원 정보수정은 로그인한 사용자만 할 수 있습니다.');
                history.go(-1);
            </script>";
    }
    
    include "../include/db_connect.php";

    $stmt = $con->prepare("SELECT * FROM _mem WHERE id = ?");
    $stmt->bind_param('s', $userid);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $id = $row["id"];
    $pass = $row["pass"];
    $name = $row["name"];
    $email = $row["email"];
    $profile_img = $row['profile_img'];
    $regist_day = date("Y-m-d (H:i)");

    unset($_SESSION['csrf_token']);     // 다음 요청 때 CSRF Token 갱신을 위해 폐기

    function generateCSRFToken() {
        if(!isset($_SESSION['csrf_token'])) {   // 요청마다 CSRF 토큰 갱신
            $token = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $token;   // CSRF 토큰을 세션에 등록
        }
        return $token;
    }
?>
<script>
    function check_input(){
        if(!document.member.current_pass.value) {    // 현재 비밀번호 확인 프론트엔드 로직
            alert("현재 비밀번호를 입력하세요!");
            document.member.current_pass.focus();
            return;
        }
        if(!document.member.pass.value) {
            alert("비밀번호를 입력하세요!");
            document.member.pass.focus();
            return;
        }
        if(!document.member.pass_confirm.value) {
            alert("비밀번호 확인을 입력하세요!");
            document.member.pass_confirm.focus();
            return;
        }
        if(!document.member.name.value) {
            alert("이름을 입력하세요!");
            document.member.name.focus();
            return;
        }
        if(!document.member.email.value) {
            alert("이메일 주소를 입력하세요!");
            document.member.email.focus();
            return;
        }
        if(document.member.pass.value != document.member.pass_confirm.value) {
            alert("비밀번호가 일치하지 않습니다. \n 다시 입력해 주세요!");
            document.member.pass.focus();
            document.member.pass.select();
            return;
        }
        document.member.submit();   // 양식에 다 맞게 작성했으면 insert.php 로직으로 제출
    }
    function reset_form(){
        document.member.pass.value = "";
        document.member.pass_confirm.value = "";
        document.member.name.value = "";
        document.member.email.value = "";
        document.member.id.focus();
        return;
    }
    function check_id(){    // 아이디 중복 체크
        window.open("check_id.php?id=" + document.member.id.value,
            "IDcheck",
            "left=700,top=300,width=380,height=160,scrollbars=no,resizable=yes");
    }
</script>
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<body> 
    <form name="member" action="modify.php" method="post" enctype="multipart/form-data">
        <div class="board_form">
            <h2>회원 정보 수정</h2>
            <ul class="list-group">
                <li class="list-group-item">
                    <span class="badge bg-secondary">아이디</span>
                    <span><?=$userid?></span>
                </li>
                <!--현재 비밀번호 확인 프론트엔드 로직-->
                <li class="list-group-item">
                    <span class="badge bg-secondary">현재 비밀번호</span>
                    <span><input type="password" name="current_pass"></span>
                </li>
                <li class="list-group-item">
                    <span class="badge bg-secondary">비밀번호</span>
                    <span><input type="password" name="pass"></span>
                </li>
                <li class="list-group-item">
                    <span class="badge bg-secondary">비밀번호 확인</span>
                    <span><input type="password" name="pass_confirm"></span>
                </li>
                <li class="list-group-item">
                    <span class="badge bg-secondary">이름</span>
                    <span><input type="text" name="name" value=<?=$name?>></span>
                </li>
                <li class="list-group-item">
                    <span class="badge bg-secondary">이메일</span>
                    <span><input type="text" name="email" value=<?=$email?>></span>
                </li>
                <!--csrf 토큰값 적용-->
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <br><br>
                <img src="profile_image.php?public_id=<?=$public_id?>" width="220" height="150"><br>
                <label>프로필 사진 변경:</label>
                <input type="file" name="profile_img">
            </ul>
            <ul class="buttons">
                <li><button class="btn btn-primary" type="button" onclick="check_input()">저장하기</button></li>
                <li><button class="btn btn-secondary" type="button" onclick="reset_form()">취소하기</button></li>
            </ul>
        </div>
    </form>
</body>
</html>