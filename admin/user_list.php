<?php
include "../include/db_connect.php";
include "admin_check.php";

$stmt = $con->prepare("SELECT num, id, name, email, level FROM _mem ORDER BY num ASC");
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
<h2>회원 관리</h2>
<table>
    <tr><th>번호</th><th>아이디</th><th>이름</th><th>이메일</th><th>레벨</th><th>프로필</th><th>관리</th></tr>
    <?php while($row = $result->fetch_assoc()) {?>
    <tr>
        <td><?=$row['num']?></td>
        <td><?=$row['id']?></td>
        <td><?=$row['name']?></td>
        <td><?=$row['email']?></td>
        <td><?=$row['level'] == 9 ? '관리자': '유저'?></td>
        <td>
        <?php if($row['level'] != 9) {?>
        <!-- <a href="user_delete.php?num=<?=$row['num']?>" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a> -->
        <form action="user_delete.php?num=<?=$row['num']?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <button type="submit">삭제</button>
        </form>
        <?php } ?>
        </td>
    </tr>
    <?php } ?>
    </tr>
</table>