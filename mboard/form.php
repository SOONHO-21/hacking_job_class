<?php
    include "../include/session.php";  //세션 관리
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Document</title>
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
<link rel="stylesheet" href="../css/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<body>
    <h2>게시판 > 글쓰기</h2>
    <form name="board" method="post" action="insert.php" enctype="multipart/form-data">
        <ul class="list-group">
            <li class="list-group-item">
                <span class="badge bg-secondary">이름 : </span>
                <span class="col2"><?=$username?></span>
            </li>
            <li class="list-group-item">
                <span class="badge bg-primary">제목 : </span>
                <span class="col2"><input name="subject" type="text"></span>
            </li>
            <li class="list-group-item">
                <span class="badge bg-secondary">내용 : </span>
                <span class="col2">
                    <textarea name="content"></textarea>
                </span>
            </li>
            <li>
                <span class="col1"> 첨부 파일 : </span>
                <span class="col2"><input type="file" name="upfile"></span>
            </li>
        </ul>
        <ul class="buttons">
            <li><button type="button" class="btn btn-primary" onclick="check_input()">저장하기</button></li>
            <li><button type="button" class="btn btn-secondary" onclick="location.href='list.php'">목록보기</button></li>
        </ul>
    </form>
</body>
</html>