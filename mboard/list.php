<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<?php
    include "../include/session.php";  // 세션 처리
?>
<body>
    <h2>게시판 > 목록보기</h2>
    <!-- 검색 폼 -->
    <form method="get" action="list.php">
        <select name="search_field">
            <option value="subject">제목</option>
            <option value="content">내용</option>
            <option value="subject_content">제목+내용</option>
        </select>
        <input type="text" name="search_word" placeholder="검색어 입력">
        <button type="submit">검색</button>
    </form>
<?php
    if($userid) {
?>
    <li><button class="btn btn-secondary" onclick="location.href='../member/modify_form.php'">회원정보 수정</button></li>
    <li><button class="btn btn-secondary" onclick="location.href='../member/logout.php'">로그아웃</button></li>
    <li><button class="btn btn-secondary" onclick="location.href='../member/profile.php?u=<?=$public_id?>'">프로필</button></li>
<?php
    } else {
?>
    <li><button class="btn btn-secondary" onclick="location.href='../member/form.php'">회원가입</button></li>
    <li><button class="btn btn-secondary" onclick="location.href='../member/login_form.php'">로그인</button></li>
<?php
    }
?>
    <ul class="list-group">
    <li class="list-group-item">
        <span>번호</span>
        <span>제목</span>
        <span>글쓴이</span>
        <span>첨부</span>
        <span>등록일</span>
    </li>
<?php
    if(isset($_GET["page"]))
        $page = $_GET["page"];
    else
        $page = 1;

    include "../include/db_connect.php";

    $search_field = isset($_GET['search_field']) ? $_GET['search_field'] : '';
    $search_word = isset($_GET['search_word']) ? $_GET['search_word'] : '';
    
    if($search_word) {
        if($search_field == "subject"){
            $stmt = $con->prepare("SELECT * FROM board WHERE subject LIKE ? ORDER BY num DESC");
            $search_term = "%" . $search_word . "%";
            $stmt->bind_param('s', $search_term);
        } else if($search_field == "content") {
            $stmt = $con->prepare("SELECT * FROM board WHERE content LIKE ? ORDER BY num DESC");
            $search_term = "%" . $search_word . "%";
            $stmt->bind_param('s', $search_term);
        } else if($search_field == "subject_content") {
            $stmt = $con->prepare("SELECT * FROM board WHERE subject LIKE ? OR content LIKE ? ORDER BY num DESC");
            $search_term = "%" . $search_word . "%";
            $stmt->bind_param('ss', $search_term, $search_term);
        } else {
            $stmt = $con->prepare("SELECT * FROM board ORDER BY num DESC");
        }
    }
    else {
        $stmt = $con->prepare("SELECT * FROM board ORDER BY num DESC");
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $total_record = mysqli_num_rows($result);   // 전체 글 수

    $scale = 4;

    // 전체 페이지 수($total_page) 계산
    if($total_record % $scale == 0)
        $total_page = floor($total_record/$scale);
    else
        $total_page = floor($total_record/$scale) + 1;

    // 표시할 페이지($page)에 따라 $start 계산
    $start = (intval($page) - 1) * $scale;

    $number = $total_record - $start;   // 실제 브라우저 상에 출력되는 글 번호
    for($i=$start; $i<$start+$scale && $i < $total_record; $i++) {
        mysqli_data_seek($result, $i);  // $result 위치(포인터) 이동
        $row = mysqli_fetch_assoc($result); // 하나의 레코드 가져오기

        $num = $row["num"];
        $id = $row["id"];
        $name = $row["name"];
        $public_id = $row["public_id"];
        $subject = $row["subject"];
        $regist_day = $row["regist_day"];
        if($row["file_name"])   // 첨부 파일이 있으면
            $file_image = "<img src='./file.png'>";
        else
            $file_image = " ";
?>
        <li class="list-group-item">
            <span class="badge bg-secondary"><?=$number?></span>
            <span><a href="view.php?num=<?=$num?>&page=<?=$page?>"><?=$subject?></a></span>
            <span class="badge bg-secondary">
                <a href="../member/profile.php?u=<?=$public_id?>">
                    <?=$name?>
                </a>
            </span>
            <span><?=$file_image?></span>
            <span><?=$regist_day?></span>
        </li>
<?php
        $number--;
    }
    mysqli_close($con);
?>
    </ul>

<!-- 페이지 번호 매김 -->
    <ul class="list-group">
<?php
    if($total_page >= 2 && $page >= 2) {
        $new_page = $page - 1;
        echo "<li><a href=list.php?page=$new_page>◀ 이전</a> </li>";
    }
    else {
        echo "<li>&nbsp;</li>";
    }

    // 게시판 목록 하단에 페이지 링크 번호 출력
    for($i = 1; $i <= $total_page; $i++) {
        if($page == $i)
            echo "<li>$page</li>";
        else
            echo "<li> <a href='list.php?page=$i'>$i</a> </li>";
    }

    if($total_page >= 2 && $page != $total_page) {
        $new_page = $page + 1;
        echo "<li> <a href='list.php?page=$new_page'>다음 ▶</a> </li>";
    }
    else {
        echo "<li>&nbsp;</li>";
    }
?>
    </ul> <!-- 페이지 번호 매김 끝 -->

	<ul>
		<li><button class="btn btn-primary" onclick="location.href='list.php?page=<?=$page?>'">목록</button></li>
		<li><button class="btn btn-secondary" onclick="location.href='form.php'">글쓰기</button></li>
	</ul>

    <a href="../serve_jwt_login_page/login_form.php">JWT Login Test Page</a>
</body>
</html>