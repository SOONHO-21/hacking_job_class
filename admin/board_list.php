<?php
include "../include/db_connect.php";
include "admin_check.php";

$stmt = $con->prepare("SELECT num, name, subject, regist_day FROM board ORDER BY num DESC");
$stmt->execute();
$result = $stmt->get_result();

function generateCSRFToken() {
    if(!isset($_SESSION['csrf_token'])) {   // 요청마다 CSRF 토큰 갱신
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;   // CSRF 토큰을 세션에 등록
    }
    return $_SESSION['csrf_token'];
}
?>
<h2>게시판 관리</h2>
<table>
<tr><th>번호</th><th>제목</th><th>작성자</th><th>등록일</th><th>관리</th></tr>
<?php while($row = $result->fetch_assoc()) {?>
<tr>
    <td><?=$row['num']?></td>
    <td><?=$row['name']?></td>
    <td><?=$row['subject']?></td>
    <td><?=$row['regist_day']?></td>
    <td>
    <!-- <a href="board_delete.php?num=<?=$row['num']?>" onclick="return confirm('정말 삭제하시겠습니까?')">글 삭제</a> -->
    <form action="board_delete.php?num=<?=$row['num']?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <button type="submit">삭제</button>
    </form>
    </td>
</tr>
<?php } ?>
</table>