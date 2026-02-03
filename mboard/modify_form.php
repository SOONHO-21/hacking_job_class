<?php
    include "../include/session.php";  //세션 관리
    include "../include/db_connect.php";

    $num = $_GET["num"];
    $page = $_GET["page"];

    $stmt = $con->prepare("SELECT * FROM board WHERE num = ? AND id = ?");
    $stmt->bind_param('is', $num, $userid);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = mysqli_fetch_assoc($result);

    if(!$row) {
        echo "
            <script>
                alert('남이 쓴 글은 수정할 수 없습니다.');
                history.go(-1);
            </script>
        ";
        exit;
    }

    $subject = $row["subject"];
    $content = $row["content"];
    $is_html = $row["is_html"]; 	// HTML 쓰기
    if($is_html=="y")
        $html_checked = "checked";
    else
        $html_checked = "";

    $regist_day = date("Y-m-d (H:i)");  // UTC 기준 현재 '년-월-일 (시:분)'
    $file_name = $row["file_name"];

    mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Document</title>
<link rel="stylesheet" href="../css/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script>
function check_input(){
    if(!document.board.subject.value) {    // 제목 확인
        alert("제목을 입력하세요!");    
        document.board.subject.focus();
        return;
    }
    if(!document.board.content.value) {    // 내용 확인
        alert("내용을 입력하세요!");
        document.board.content.focus();
        return;
    }

    document.board.submit();    // form.submit(). form 프로퍼티의 값들을 전송
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</head>
<body>
    <h2>게시판 > 글쓰기</h2>
    <form name="board" method="post" action="modify.php?num=<?=$num?>&page=<?=$page?>" enctype="multipart/form-data">
        <ul class="list-group">
            <li class="list-group-item">
                <span class="badge bg-secondary">이름 : </span>
                <span><?=$username?></span>
            </li>
            <li class="list-group-item">
                <span class="badge bg-secondary">제목 : </span>
                <span><input name="subject" type="text" value=<?=$subject?>></span>
            </li>
            <li class="list-group-item">
                <span class="badge bg-secondary">내용 : </span>
                <span>
                    <textarea name="content"><?=$content?></textarea>
                </span>
            </li>
            <li class="list-group-item">
                <span> 첨부 파일 : </span>
                <span><input type="file" name="upfile"><?=$file_name?></span>
            </li>
        </ul>
        <ul>
            <li><button class="btn btn-primary" type="button" onclick="check_input()">저장하기</button></li>
            <li><button class="btn btn-secondary" type="button" onclick="location.href='list.php'">목록보기</button></li>
        </ul>
    </form>
</body>
</html>