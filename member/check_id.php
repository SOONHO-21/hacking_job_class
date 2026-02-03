<!DOCTYPE html>
<head>
<meta charset="utf-8">
<style>
.close { margin:20px 0 0 120px; cursor:pointer; }
</style>
</head>
<body>
    <h3>아이디 중복체크</h3>
    <div>
<?php
    $id = $_GET["id"];

    if(!$id){
        echo("아이디를 입력해 주세요!");
    }
    else{
        include "../include/db_connect.php";

        $stmt = $con->prepare("select * from _mem where id = ?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $new_record = mysqli_num_rows($result);

        if($new_record) {    // 조회되는 쿼리 결과가 있으면 이미 있는 아이디라는 뜻
            echo $id." 아이디는 중복됩니다.<br>";
            echo "다른 아이디를 사용해 주세요!<br>";
        } else {
            echo $id." 아이디는 사용 가능합니다.<br>";
        }
        mysqli_close($con);
    }
?>
    </div>
    <div class="close">
        <button type="button" onclick="javascript:self.close()">창 듣기</button>
    </div>
</body>
</html>