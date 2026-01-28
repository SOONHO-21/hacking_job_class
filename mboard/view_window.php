<?php
    $num = $_GET["num"];
    $page  = isset($_GET['page'])  ? intval($_GET['page']) : 1;	// 기본값 1

    include "../include/session.php";

    include "../include/db_connect.php";
    $stmt = $con->prepare("SELECT * FROM board WHERE num = ?");
    $stmt->bind_param('i', $num);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = mysqli_fetch_assoc($result);

    $id = $row["id"];   // 아이디
    $name = $row["name"];   // 이름
    $subject = $row["subject"];  // 제목
    $regist_day = $row["regist_day"]; // 작성일

    $content = $row["content"];     // 작성일
    $is_html = $row["is_html"];
    
    if($is_html == "y") {
        $content = htmlspecialchars_decode($content, ENT_QUOTES);
    }
    else {
        $content = str_replace(" ", "&nbsp", $content);     // 공백 변환
        $content = str_replace("\n", "<br>", $content);     // 줄바꿈 변환
    }

    $file_name = $row["file_name"];
    $file_type = $row["file_type"];
    $file_copied = $row["file_copied"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<script>
function ripple_check_input(){
    if(!document.ripple_form.ripple_content.value) {    // 내용 확인
        alert("댓글 내용을 입력하세요.");
        document.board.content.focus();
        return;
    }
    document.ripple_form.submit();    // form.submit(). form 프로퍼티의 값들을 전송
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<body>
    <h2>회원 게시판 > 내용보기</h2>
    <ul>
        <div class="span4"><b>제목 :</b> <?=$subject?></div>    <!-- 제목 출력 -->
        <div class="span4"><?=$name?> | <?=$regist_day?></div>  <!-- 이름, 작성일 출력 -->
        <br>
        <?php
            echo $content;  // 글 내용 출력

            echo "<br><br>";

            if($file_name) {
                $file_path = "./data/".$file_copied;
                $file_size = filesize($file_path);

                if(str_contains($file_type, "image"))   // 업로드 파일이 이미지라면
                    echo "<img src='./data/$file_copied' width='660' height='450'>";     // 브라우저상에 이미지 출력
                
                echo "▷ 첨부파일 : $file_copied ($file_size Byte) &nbsp;&nbsp;&nbsp;&nbsp; <a href='download.php?file_name=$file_name&file_type=$file_type&file_copied=$file_copied'>$file_copied</a>";
            }
        ?>
    </ul>
    <?php
        $stmt = $con->prepare("SELECT * FROM ripple WHERE parent = ?");
        $stmt->bind_param('i', $num);
        $stmt->execute();
        $ripple_result = $stmt->get_result();

        while($row_ripple = mysqli_fetch_assoc($ripple_result)) {
            $ripple_num = $row_ripple["num"];
            $ripple_id = $row_ripple["id"];
            $ripple_name = $row_ripple["name"];
            echo $ripple_name;
            $ripple_content = $row_ripple["content"];

            $ripple_content = str_replace("\n", "<br>", $ripple_content);
            $ripple_content = str_replace(" ", "&nbsp", $ripple_content);
            $ripple_date = $row_ripple["regist_day"];
    ?>
	<div class="container-fluid">
		<div class="col-12 col-md-6 bg-primary text-white"><?=$ripple_name?></div>
		<div class="col-12 col-md-6 bg-primary text-white"><?=$ripple_date?></div>
		<span>
            <div>
                <?=$ripple_content?>
            </div>
        <?php
            if($userid==$ripple_id) {
        ?>
            <button class="btn btn-secondary" onclick="location.href='delete_ripple.php?num=<?=$num?>&ripple_num=<?=$ripple_num?>&page=<?=$page?>'">삭제하기</button>
        <?php
            }
            else
                echo "";
        ?>
		</span>
	</div>
    <?php
        }
        mysqli_close($con);
    ?>
    <!-- 댓글 입력 폼 -->
    <div class="mb-3">
        <form name="ripple_form" method="post" action="insert_ripple.php?num=<?=$num?>&page=<?=$page?>">
            <div class="form-floating mb-3">
                <label for="floatingInput">댓글쓰기</label>
                <br><br>
                <textarea class="form-control" name="ripple_content"></textarea>
            </div>
            <div>
                <button class="btn btn-primary" onclick="ripple_check_input()">댓글입력</button></li>
            </div>
        </form>
    </div>
    <ul>
        <li><button class="btn btn-primary" onclick="location.href='list.php?page=<?=$page?>'">목록보기</button></li>
    <?php
        if($userid==$id) {
    ?>
        <li><button class="btn btn-primary" onclick="location.href='modify_form.php?num=<?=$num?>&page=<?=$page?>'">수정하기</button></li>
        <li><button class="btn btn-primary" onclick="location.href='delete.php?num=<?=$num?>&page=<?=$page?>'">삭제하기</button></li>
    <?php
        }
    ?>
        <li><button class="btn btn-primary" onclick="location.href='form.php'">글쓰기</button></li>
    </ul>
</body>
</html>